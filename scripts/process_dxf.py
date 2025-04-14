import os
import shutil
import cv2
import numpy as np
import ezdxf
import re
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from ezdxf.addons.drawing import RenderContext, Frontend
from ezdxf.addons.drawing.matplotlib import MatplotlibBackend
from ultralytics import YOLO
import sys

# Get the directory where the script is located
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

# Configuration: Model paths are now relative to the script location
MODEL_PATH = os.path.join(SCRIPT_DIR, 'best.pt')
MODEL_PATH_UPINFORE = os.path.join(SCRIPT_DIR, 'best - copy.pt')

# Input and Output folders will be passed as command-line arguments
if len(sys.argv) < 3:
    print("Error: Please provide INPUT_FOLDER and OUTPUT_FOLDER as arguments.")
    sys.exit(1)

INPUT_FOLDER = sys.argv[1]
OUTPUT_FOLDER = sys.argv[2]

# Ensure OUTPUT_FOLDER is a directory
if os.path.exists(OUTPUT_FOLDER) and not os.path.isdir(OUTPUT_FOLDER):
    print(f"Error: {OUTPUT_FOLDER} already exists as a file, not a folder!")
    sys.exit(1)

# Create output folder if it doesn’t exist
os.makedirs(OUTPUT_FOLDER, exist_ok=True)

# Initialize YOLO model
model = YOLO(MODEL_PATH)

def render_dxf_to_image(doc):
    """Render DXF ke gambar dan kembalikan transformasi koordinat"""
    fig = plt.figure(figsize=(10, 10), dpi=100)
    ax = fig.add_axes([0, 0, 1, 1])
    ctx = RenderContext(doc)
    backend = MatplotlibBackend(ax)
    Frontend(ctx, backend).draw_layout(doc.modelspace(), finalize=True)

    # Dapatkan batas rendering dalam koordinat DXF
    xmin, xmax = ax.get_xlim()
    ymin, ymax = ax.get_ylim()
    
    # Update canvas dan konversi ke array numpy
    fig.canvas.draw()
    img = np.frombuffer(fig.canvas.tostring_argb(), dtype=np.uint8)
    img = img.reshape(fig.canvas.get_width_height()[::-1] + (4,))
    img = img[:, :, 1:]
    img = cv2.cvtColor(img, cv2.COLOR_RGB2BGR)

    plt.close(fig)
    
    # Simpan informasi transformasi
    transform_data = {
        'dxf_bounds': (xmin, xmax, ymin, ymax),
        'img_size': img.shape[:2][::-1]  # (width, height)
    }
    
    return img, transform_data

def dxf_to_pixel(point, dxf_bounds, img_size):
    """Konversi koordinat DXF ke koordinat pixel gambar"""
    x, y = point
    xmin, xmax, ymin, ymax = dxf_bounds
    width, height = img_size
    
    # Normalisasi koordinat
    x_norm = (x - xmin) / (xmax - xmin)
    y_norm = (y - ymin) / (ymax - ymin)
    
    # Konversi ke pixel
    px = int(x_norm * width)
    py = int((1 - y_norm) * height)  # Balik sumbu Y untuk OpenCV
    
    return px, py

def process_dxf(doc, detection_boxes, transform_data, filename):
    """Hapus hanya entitas TEXT/MTEXT dalam bounding box dan gantikan dengan nama file yang sesuai"""
    msp = doc.modelspace()
    entities_to_remove = []
    
    for entity in msp:
        if entity.dxftype() in ['TEXT', 'MTEXT']:
            try:
                # Dapatkan koordinat insertion point
                insert_point = entity.dxf.insert
                
                # Konversi ke koordinat pixel
                px, py = dxf_to_pixel(
                    (insert_point.x, insert_point.y),
                    transform_data['dxf_bounds'],
                    transform_data['img_size']
                )
                
                # Cek apakah berada dalam salah satu bounding box
                for box in detection_boxes:
                    x1, y1, x2, y2 = box
                    if x1 <= px <= x2 and y1 <= py <= y2:
                        entities_to_remove.append((entity, box))
                        break
                        
            except AttributeError:
                continue

    # Hapus entitas yang terdeteksi
    for entity, _ in entities_to_remove:
        msp.delete_entity(entity)

    if detection_boxes:
        # Ambil bounding box yang pertama untuk menentukan posisi teks
        x1, y1, x2, y2 = detection_boxes[0]

        # Hitung tinggi teks agar sesuai dengan bounding box
        box_height = y2 - y1
        box_width = x2 - x1

        # Perkiraan tinggi teks agar sesuai dengan tinggi bounding box
        text_height = max(box_height * 0.8, 10)  # 80% dari tinggi bounding box, minimal 10

        # Ekstrak bagian nama file setelah tanda "-" ketiga
        filename_without_ext = os.path.splitext(filename)[0]
        filename_parts = filename_without_ext.split("-")
        text_to_insert = "-".join(filename_parts[3:7]) if len(filename_parts) > 7 else filename_without_ext

        # Perkirakan lebar teks berdasarkan tinggi teks
        estimated_text_width = text_height * 0.6 * len(text_to_insert)

        # Jika teks lebih lebar dari bounding box, kecilkan ukuran teks
        if estimated_text_width > box_width:
            text_height *= box_width / estimated_text_width

        # Geser teks sedikit ke kanan
        offset_x = box_width * 0.1  # Geser 10% dari lebar bounding box
        dxf_x, dxf_y = pixel_to_dxf(
            (x1 + offset_x, (y1 + y2) // 2),
            transform_data['dxf_bounds'],
            transform_data['img_size']
        )

        # Tambahkan teks baru
        msp.add_text(
            text=text_to_insert.upper(),
            dxfattribs={
                'insert': (dxf_x, dxf_y),
                'height': text_height,
                'layer': 'SCALE',
                'color': 1
            }
        )

    return len(entities_to_remove)

def pixel_to_dxf(point, dxf_bounds, img_size):
    """Konversi koordinat pixel ke koordinat DXF"""
    px, py = point
    xmin, xmax, ymin, ymax = dxf_bounds
    width, height = img_size
    
    # Normalisasi koordinat
    x_dxf = xmin + (px / width) * (xmax - xmin)
    y_dxf = ymin + ((1 - (py / height)) * (ymax - ymin))  # Balik Y untuk DXF
    
    return x_dxf, y_dxf

def get_group_name(filename):
    """Fungsi untuk mendapatkan nama grup dari file"""
    filename_without_ext = os.path.splitext(filename)[0]
    filename_parts = filename_without_ext.split("-")
    return "-".join(filename_parts[:3]) if len(filename_parts) > 3 else filename_without_ext

def group_files_in_output_folder(output_folder):
    """Mengelompokkan file DXF berdasarkan nama grup"""
    for filename in os.listdir(output_folder):
        if filename.lower().endswith('.dxf'):
            file_path = os.path.join(output_folder, filename)
            
            # Tentukan folder berdasarkan grup nama file
            group_name = get_group_name(filename)
            group_folder = os.path.join(output_folder, group_name)
            os.makedirs(group_folder, exist_ok=True)
            
            # Pindahkan file ke folder yang sesuai
            shutil.move(file_path, os.path.join(group_folder, filename))
            print(f"Moved {filename} to {group_folder}")


def merge_do_boxes_to_main_file(folder_path):
    """Gabungkan objek DO dan hanya MTEXT cyan tertentu di FRAG-11 ke dalam Block Reference di file utama."""
    dxf_files = [f for f in os.listdir(folder_path) if f.lower().endswith('.dxf')]
    if len(dxf_files) < 1:
        return

    # Tentukan file utama (file pertama)
    main_file = dxf_files[0]
    main_path = os.path.join(folder_path, main_file)
    main_doc = ezdxf.readfile(main_path)
    main_msp = main_doc.modelspace()

    # Hitung ketinggian maksimum dari konten utama
    current_max_y = calculate_max_y(main_msp)
    padding = 1000  # Jarak antar objek DO

    block_counter = 1  # Counter unik untuk nama block
    allowed_texts = {f"{{ {i}}}" for i in range(1, 10)} | {"{ P}", "{ S}", "{ C}"}  # Set berisi teks yang diizinkan

    for filename in dxf_files[1:]:
        file_path = os.path.join(folder_path, filename)
        doc = ezdxf.readfile(file_path)
        msp = doc.modelspace()

        # Kumpulkan objek DO dan MTEXT yang memenuhi kriteria
        entities_to_group = []
        for entity in msp:
            if entity.dxf.layer == "SCALE" or (entity.dxftype() == "MTEXT" and entity.dxf.layer == "FRAG-11" and entity.dxf.color == 4):
                text_value = entity.text.strip() if hasattr(entity, "text") else ""

                # **Hanya** MTEXT yang ada dalam `allowed_texts` yang dimasukkan
                if entity.dxftype() == "MTEXT" and text_value not in allowed_texts:
                    continue  

                new_entity = entity.copy()
                if hasattr(new_entity.dxf, 'insert'):
                    new_entity.dxf.insert = (new_entity.dxf.insert.x, new_entity.dxf.insert.y - (current_max_y + padding))
                elif hasattr(new_entity.dxf, 'start'):
                    new_entity.dxf.start = (new_entity.dxf.start.x, new_entity.dxf.start.y - (current_max_y + padding))
                    new_entity.dxf.end = (new_entity.dxf.end.x, new_entity.dxf.end.y - (current_max_y + padding))

                entities_to_group.append(new_entity)

        # Jika ada objek yang memenuhi syarat, buat block reference
        if entities_to_group:
            block_name = f"BLOCK_{block_counter}"
            block_counter += 1

            if block_name in main_doc.blocks:
                block = main_doc.blocks.get(block_name)
            else:
                block = main_doc.blocks.new(name=block_name)

            # Tambahkan entitas ke dalam block
            for entity in entities_to_group:
                block.add_entity(entity)

            # Tambahkan block reference ke modelspace utama
            main_msp.add_blockref(block_name, insert=(0, current_max_y + padding))

        # Update ketinggian maksimum setelah menambahkan block
        current_max_y = calculate_max_y(main_msp)

        # Hapus file setelah diproses
        os.remove(file_path)

    # Simpan perubahan ke file utama
    main_doc.saveas(main_path)
    print(f"All allowed objects merged into blocks in {main_file}.")


def calculate_max_y(msp):
    """Hitung koordinat Y maksimum dari semua entitas"""
    max_y = -np.inf
    for entity in msp:
        if entity.dxftype() == 'DO':
            if hasattr(entity.dxf, 'insert'):
                max_y = max(max_y, entity.dxf.insert.y)
            elif hasattr(entity.dxf, 'start'):
                max_y = max(max_y, entity.dxf.start.y, entity.dxf.end.y)
    return max_y if max_y != -np.inf else 0

def dxf_to_image_IUF(dxf_path):
    """Konversi DXF ke image OpenCV dengan backend Agg"""
    doc = ezdxf.readfile(dxf_path)
    fig = plt.figure()
    ax = fig.add_axes([0, 0, 1, 1])
    ctx = RenderContext(doc)
    out = MatplotlibBackend(ax)
    Frontend(ctx, out).draw_layout(doc.modelspace(), finalize=True)
    
    # Gunakan buffer langsung dengan Agg backend
    fig.canvas.draw()
    width, height = fig.canvas.get_width_height()
    img = np.frombuffer(fig.canvas.buffer_rgba(), dtype=np.uint8)
    img = img.reshape((height, width, 4))
    img = cv2.cvtColor(img, cv2.COLOR_RGBA2BGR)
    plt.close(fig)
    return img

def rename_dxf_IUF(folder_path, MODEL_PATH_UPINFORE):
    model = YOLO(MODEL_PATH_UPINFORE)
    
    for file in os.listdir(folder_path):
        if file.endswith(".dxf"):
            dxf_path = os.path.join(folder_path, file)
            img = dxf_to_image_IUF(dxf_path)
            
            results = model(img)[0]
            
            detected_classes = set()
            for result in results.boxes:
                if result.conf >= 0.2:
                    class_name = model.names[int(result.cls)]
                    if class_name in ["UP<--", "IN<--", "FORE<--", "-->UP", "-->IN", "-->FORE"]:
                        detected_classes.add(class_name)
            
            if detected_classes:
                cleaned_classes = [cls.replace("<--", "").replace("-->", "") for cls in detected_classes]
                new_name = f"{'_'.join(sorted(cleaned_classes))}_{file}"
                os.rename(dxf_path, os.path.join(folder_path, new_name))
                print(f"Renamed {file} -> {new_name}")
            else:
                print(f"No relevant class detected in {file}")

# ========================================================================================================
                
def extract_number_from_filename(filename):
    parts = filename.split('-')
    if len(parts) > 3:
        number_part = parts[2]
        if number_part.isdigit():
            return int(number_part)
    return None

def adjust_mtext_height(doc):
    for entity in doc.modelspace().query("MTEXT"):
        if entity.dxf.color == 1 and entity.dxf.layer == "FRAG-11":
            entity.dxf.char_height = 13.9049

def get_specific_mtext_numbers(doc, filename):
    numbers = []
    green_numbers = set()
    skip_number = extract_number_from_filename(filename)
    
    for entity in doc.modelspace().query("MTEXT"):
        if entity.dxf.color == 3:
            match = re.search(r'\{L\s*=\s*(\d+)\}', entity.text)
            if match:
                green_numbers.add(int(match.group(1)))
    
    for entity in doc.modelspace().query("MTEXT"):
        if entity.dxf.color == 1 and round(entity.dxf.char_height, 4) == 13.9049:
            text = entity.text
            if '-' in text:
                continue
            mtext_position = entity.dxf.insert
            if mtext_position.x == 909.3870:
                continue
            matches = re.findall(r'\b\d+\b', text)
            for match in matches:
                number = int(match)
                if skip_number is not None and number == skip_number:
                    continue
                if number < 500 and number not in green_numbers:
                    numbers.append(number)
    
    return sorted(numbers)

def get_frag_11_mtext(doc):
    frag_texts = []
    for entity in doc.modelspace().query("MTEXT"):
        if entity.dxf.layer == "FRAG-11" and entity.dxf.color == 3:
            text = entity.text.strip()
            if text.isupper() and '=' not in text:
                frag_texts.append(text)
    return sorted(frag_texts)

def process_dxf_folder(folder_path):
    dxf_files = [f for f in os.listdir(folder_path) if f.lower().endswith('.dxf')]
    
    for dxf_file in dxf_files:
        dxf_path = os.path.join(folder_path, dxf_file)
        try:
            doc = ezdxf.readfile(dxf_path)
            adjust_mtext_height(doc)
            extracted_numbers = get_specific_mtext_numbers(doc, dxf_file)
            frag_texts = get_frag_11_mtext(doc)

            # Ambil prefix dari nama file yang sudah diproses oleh rename_dxf_IUF
            file_parts = dxf_file.split("_", 1)  # Pisahkan berdasarkan underscore pertama
            prefix = file_parts[0] if file_parts[0] in ["UP", "IN", "FORE"] else ""  # Ambil prefix jika valid

            # # Ambil teks setelah strip '-' terakhir
            # last_part = dxf_file.rsplit("-", 1)[-1].replace(".dxf", "")

            # Bentuk nama file baru dengan prefix yang benar
            new_base_name = f"{prefix}_{'_'.join(map(str, extracted_numbers))}_{'_'.join(frag_texts)}_{file_parts[-1][:-4]}"

            # Ambil teks setelah tanda '-' pertama dan sebelum tanda '-' kedua
            middle_part = dxf_file.split("-", 2)[1] if len(dxf_file.split("-", 2)) > 2 else ""

            # Pindahkan middle_part ke depan jika ditemukan
            if middle_part:
                new_base_name = f"{middle_part}_{new_base_name}"

            new_folder_name = new_base_name.split('_prf-')[0]
            new_folder_path = os.path.join(folder_path, new_folder_name)
            os.makedirs(new_folder_path, exist_ok=True)
            
            new_file_name = f"{new_base_name}.dxf"
            new_file_path = os.path.join(new_folder_path, new_file_name)
            os.rename(dxf_path, new_file_path)
            print(f"Renamed and moved: {dxf_file} -> {new_file_path}")
        except Exception as e:
            print(f"Error processing {dxf_file}: {e}")

def sanitize_block_name(name):
    """Membersihkan nama block agar valid di DXF (tanpa spasi dan karakter khusus)."""
    return re.sub(r'[^\w\d_]', '_', name)  # Hanya menyisakan huruf, angka, dan underscore (_)

def group_all_entities_into_block(main_path):
    """Mengelompokkan semua objek dalam file utama menjadi satu block reference dengan nama file DXF."""
    try:
        doc = ezdxf.readfile(main_path)
        msp = doc.modelspace()
        
        # Ambil nama file tanpa ekstensi dan bersihkan untuk nama block
        raw_block_name = os.path.splitext(os.path.basename(main_path))[0]
        block_name = sanitize_block_name(raw_block_name)

        # Hapus block lama jika sudah ada
        if block_name in doc.blocks:
            doc.blocks.delete_block(block_name)

        # Buat block baru
        block = doc.blocks.new(name=block_name)

        # Salin semua entitas ke dalam block
        entities_to_group = list(msp.query("*"))  # Ambil semua entitas
        for entity in entities_to_group:
            new_entity = entity.copy()
            block.add_entity(new_entity)

        # Hapus entitas asli dari modelspace
        for entity in entities_to_group:
            msp.delete_entity(entity)

        # Tambahkan block reference ke modelspace
        msp.add_blockref(block_name, insert=(0, 0))

        # Simpan perubahan
        doc.saveas(main_path)
        print(f"Semua entitas dalam {main_path} telah dikelompokkan menjadi block '{block_name}'.")

    except Exception as e:
        print(f"Error saat mengelompokkan entitas dalam {main_path}: {e}")


if __name__ == "__main__":
    # Modifikasi loop utama
    for filename in os.listdir(INPUT_FOLDER):
        if filename.lower().endswith('.dxf'):
            file_path = os.path.join(INPUT_FOLDER, filename)

            try:
                # Baca file DXF
                doc = ezdxf.readfile(file_path)

                # Render ke gambar dan dapatkan transformasi
                dxf_image, transform_data = render_dxf_to_image(doc)

                # Deteksi dengan YOLO
                results = model(dxf_image)

                # Ekstrak bounding boxes untuk class DO
                detection_boxes = []
                for result in results:
                    for box in result.boxes:
                        if model.names[int(box.cls[0])] == 'DO':
                            x1, y1, x2, y2 = map(int, box.xyxy[0].cpu().numpy())
                            detection_boxes.append((x1, y1, x2, y2))

                if detection_boxes:
                    deleted_count = process_dxf(doc, detection_boxes, transform_data, filename)
                    if deleted_count > 0:
                        output_path = os.path.join(OUTPUT_FOLDER, filename)
                        doc.saveas(output_path)
                        print(f"Deleted {deleted_count} entities and added filename text in {filename}")
                    else:
                        print(f"No matching entities found in {filename}")
                else:
                    print(f"No DO detected in {filename}")

            except Exception as e:
                print(f"Error processing {filename}: {str(e)}")
    
    rename_dxf_IUF(OUTPUT_FOLDER, MODEL_PATH_UPINFORE)
    process_dxf_folder(OUTPUT_FOLDER)
    # Mengelompokkan file hasil modifikasi
    group_files_in_output_folder(OUTPUT_FOLDER)
    print("Grouping completed.")

    # Loop melalui setiap folder dalam OUTPUT_FOLDER
    for group_folder in os.listdir(OUTPUT_FOLDER):
        folder_path = os.path.join(OUTPUT_FOLDER, group_folder)
        if os.path.isdir(folder_path):
            for filename in os.listdir(folder_path):
                if filename.lower().endswith(".dxf"):
                    dxf_path = os.path.join(folder_path, filename)
                    try:
                        doc = ezdxf.readfile(dxf_path)
                        msp = doc.modelspace()

                        # Cari text height dari MTEXT merah di layer FRAG-30 dengan teks "{\W0.800000;PART NAME}"
                        reference_text_height = None

                        for entity in msp.query("MTEXT"):
                            if entity.dxf.layer == "FRAG-30" and entity.dxf.color == 1 and entity.text.strip() == r"{\W0.800000;PART NAME}":
                                reference_text_height = entity.dxf.char_height
                                print(f"Reference text height found in {dxf_path}: {reference_text_height}")  # Debugging
                                break  # Ambil satu nilai pertama yang ditemukan

                        # Jika ditemukan reference text height, terapkan ke TEXT merah di layer SCALE
                        if reference_text_height:
                            text_contents = []

                            for entity in msp.query("TEXT"):
                                if entity.dxf.layer == "SCALE" and entity.dxf.color == 1:
                                    text_contents.append(entity.dxf.text)  # Simpan teks sebelum diubah
                                    print(f"Updating {dxf_path}: {entity.dxf.height} -> {reference_text_height}")  # Debugging
                                    entity.dxf.height = reference_text_height  # Terapkan ukuran baru

                            doc.saveas(dxf_path)  # Simpan perubahan
                            print(f"Updated red TEXT heights in {dxf_path} to {reference_text_height}")

                            # Tampilkan teks yang ditemukan di TEXT merah layer SCALE
                            if text_contents:
                                print(f"\nTexts in RED TEXT on 'SCALE' layer in {dxf_path}:")
                                for text in text_contents:
                                    print(f" - {text}")

                    except Exception as e:
                        print(f"Error updating text height in {dxf_path}: {e}")

    print("Final step: All red TEXT heights in layer 'SCALE' set based on FRAG-30 reference and texts displayed.")

    # Gabungkan bounding box DO ke file utama dalam tiap folder
    for group_folder in os.listdir(OUTPUT_FOLDER):
        folder_path = os.path.join(OUTPUT_FOLDER, group_folder)
        if os.path.isdir(folder_path):
            merge_do_boxes_to_main_file(folder_path)
            main_file = os.listdir(folder_path)[0]  # Ambil file utama pertama
            main_path = os.path.join(folder_path, main_file)
            group_all_entities_into_block(main_path)
    
    print("Semua bounding box DO telah digabungkan ke file utama dalam tiap folder!")


    # Tahap akhir: Hapus bagian dari "prf" hingga akhir dalam nama file

    for group_folder in os.listdir(OUTPUT_FOLDER):
        folder_path = os.path.join(OUTPUT_FOLDER, group_folder)
        if os.path.isdir(folder_path):
            for filename in os.listdir(folder_path):
                if filename.lower().endswith(".dxf"):
                    old_file_path = os.path.join(folder_path, filename)
                    
                    # Hilangkan bagian dari "_prf" hingga akhir
                    new_filename = filename.split("_prf", 1)[0] + ".dxf"
                    new_file_path = os.path.join(folder_path, new_filename)
                    
                    # Ganti nama file jika namanya berubah
                    if old_file_path != new_file_path:
                        os.rename(old_file_path, new_file_path)
                        print(f"Renamed final file: {old_file_path} -> {new_file_path}")

    print("Final cleanup: 'prf' and following parts removed from filenames.")

    # Tahap akhir: Pindahkan semua file DXF ke folder utama dan hapus sub-folder
    for group_folder in os.listdir(OUTPUT_FOLDER):
        folder_path = os.path.join(OUTPUT_FOLDER, group_folder)

        if os.path.isdir(folder_path):
            for root, dirs, files in os.walk(folder_path, topdown=False):  # Loop dari yang terdalam
                for filename in files:
                    if filename.lower().endswith(".dxf"):
                        old_file_path = os.path.join(root, filename)
                        new_file_path = os.path.join(OUTPUT_FOLDER, filename)

                        # Pindahkan file DXF ke folder utama
                        shutil.move(old_file_path, new_file_path)
                        print(f"Moved: {old_file_path} -> {new_file_path}")

                # Setelah file dipindah, hapus folder kosong
                if not os.listdir(root):  # Cek apakah folder kosong
                    os.rmdir(root)
                    print(f"Deleted empty folder: {root}")

    print("Final cleanup: All DXF files moved to the main output folder and sub-folders removed.")
    