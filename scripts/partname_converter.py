import os
import cv2
import numpy as np
import ezdxf
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from ezdxf.addons.drawing import RenderContext, Frontend
from ezdxf.addons.drawing.matplotlib import MatplotlibBackend
from ultralytics import YOLO
import sys

# Fungsi pemrosesan DXF (tetap sama)
def render_dxf_to_image(doc):
    fig = plt.figure(figsize=(10, 10), dpi=100)
    ax = fig.add_axes([0, 0, 1, 1])
    ctx = RenderContext(doc)
    backend = MatplotlibBackend(ax)
    Frontend(ctx, backend).draw_layout(doc.modelspace(), finalize=True)
    xmin, xmax = ax.get_xlim()
    ymin, ymax = ax.get_ylim()
    fig.canvas.draw()
    img = np.frombuffer(fig.canvas.tostring_argb(), dtype=np.uint8)
    img = img.reshape(fig.canvas.get_width_height()[::-1] + (4,))
    img = img[:, :, 1:]
    img = cv2.cvtColor(img, cv2.COLOR_RGB2BGR)
    plt.close(fig)
    transform_data = {
        'dxf_bounds': (xmin, xmax, ymin, ymax),
        'img_size': img.shape[:2][::-1]
    }
    return img, transform_data

def dxf_to_pixel(point, dxf_bounds, img_size):
    x, y = point
    xmin, xmax, ymin, ymax = dxf_bounds
    width, height = img_size
    x_norm = (x - xmin) / (xmax - xmin)
    y_norm = (y - ymin) / (ymax - ymin)
    px = int(x_norm * width)
    py = int((1 - y_norm) * height)
    return px, py

def pixel_to_dxf(point, dxf_bounds, img_size):
    px, py = point
    xmin, xmax, ymin, ymax = dxf_bounds
    width, height = img_size
    x_dxf = xmin + (px / width) * (xmax - xmin)
    y_dxf = ymin + ((1 - (py / height)) * (ymax - ymin))
    return x_dxf, y_dxf

def process_dxf(doc, detection_boxes, transform_data, filename):
    msp = doc.modelspace()
    entities_to_remove = []
    
    reference_text_height = None
    print(f"\nDebugging {filename}: Searching for MTEXT in FRAG-30...")
    for entity in msp.query("MTEXT"):
        print(f" - Found MTEXT: layer={entity.dxf.layer}, color={entity.dxf.color}, text='{entity.text.strip()}'")
        if (entity.dxf.layer == "FRAG-30" and 
            entity.dxf.color == 1 and 
            entity.text.strip() == r"{\W0.800000;PART NAME}"):
            try:
                reference_text_height = entity.dxf.char_height
                print(f"Reference text height found in {filename}: {reference_text_height}")
                break
            except AttributeError:
                print(f"Warning: No valid 'char_height' attribute for MTEXT in FRAG-30 in {filename}")
                break
    
    if reference_text_height is None:
        print(f"Warning: No matching MTEXT found in FRAG-30 for {filename}, using default height.")

    for entity in msp:
        if entity.dxftype() in ['TEXT', 'MTEXT']:
            try:
                insert_point = entity.dxf.insert
                px, py = dxf_to_pixel(
                    (insert_point.x, insert_point.y),
                    transform_data['dxf_bounds'],
                    transform_data['img_size']
                )
                for box in detection_boxes:
                    x1, y1, x2, y2 = box
                    if x1 <= px <= x2 and y1 <= py <= y2:
                        entities_to_remove.append((entity, box))
                        break
            except AttributeError:
                continue

    for entity, _ in entities_to_remove:
        msp.delete_entity(entity)

    if detection_boxes:
        x1, y1, x2, y2 = detection_boxes[0]
        box_height = y2 - y1
        box_width = x2 - x1
        if reference_text_height is not None:
            text_height = reference_text_height
            print(f"Using reference text height: {text_height}")
        else:
            text_height = max(box_height * 0.8, 10)
            print(f"Using default text height: {text_height}")

        filename_without_ext = os.path.splitext(filename)[0]
        filename_parts = filename_without_ext.split("-")
        if len(filename_parts) > 7:
            text_to_insert = "-".join(filename_parts[3:7])
        elif len(filename_parts) > 3:
            text_to_insert = "-".join(filename_parts[3:])
        else:
            text_to_insert = filename_without_ext
        print(f"Text to insert: {text_to_insert}")

        offset_x = box_width * 0.1
        dxf_x, dxf_y = pixel_to_dxf(
            (x1 + offset_x, (y1 + y2) // 2),
            transform_data['dxf_bounds'],
            transform_data['img_size']
        )

        msp.add_text(
            text=text_to_insert.upper(),
            dxfattribs={
                'insert': (dxf_x, dxf_y),
                'height': text_height - 3,
                'layer': 'SCALE',
                'color': 1
            }
        )
        print(f"Added text '{text_to_insert}' with height {text_height} at ({dxf_x}, {dxf_y}) in layer SCALE")

    return len(entities_to_remove)

# Fungsi utama untuk memproses folder
def main():
    if len(sys.argv) != 3:
        print("Usage: python partname_converter.py <input_folder> <output_folder>")
        sys.exit(1)

    input_folder = sys.argv[1]
    output_folder = sys.argv[2]
    
    # Tentukan jalur model relatif terhadap lokasi skrip
    script_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(script_dir, "model_partname.pt")

    if not os.path.exists(input_folder):
        print(f"Error: Input folder not found at {input_folder}")
        sys.exit(1)

    if not os.path.exists(model_path):
        print(f"Error: Model file not found at {model_path}")
        sys.exit(1)

    if not os.path.exists(output_folder):
        os.makedirs(output_folder, exist_ok=True)
        print(f"Created output folder at {output_folder}")

    try:
        model = YOLO(model_path)
        print("Starting DXF processing...")

        for filename in os.listdir(input_folder):
            if filename.lower().endswith('.dxf'):
                file_path = os.path.join(input_folder, filename)
                output_path = os.path.join(output_folder, filename)
                print(f"Processing {filename}...")
                try:
                    doc = ezdxf.readfile(file_path)
                    dxf_image, transform_data = render_dxf_to_image(doc)
                    results = model(dxf_image)
                    detection_boxes = []
                    for result in results:
                        for box in result.boxes:
                            if model.names[int(box.cls[0])] == 'DO':
                                x1, y1, x2, y2 = map(int, box.xyxy[0].cpu().numpy())
                                detection_boxes.append((x1, y1, x2, y2))

                    if detection_boxes:
                        deleted_count = process_dxf(doc, detection_boxes, transform_data, filename)
                        if deleted_count > 0:
                            doc.saveas(output_path)
                            print(f"Deleted {deleted_count} entities and added filename text in {filename}")
                        else:
                            print(f"No matching entities found in {filename}")
                    else:
                        print(f"No DO detected in {filename}")
                except Exception as e:
                    print(f"Error processing {filename}: {str(e)}")
        print("Processing completed!")
    except Exception as e:
        print(f"Error during processing: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()