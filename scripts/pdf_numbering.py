# import sys
# from PyPDF2 import PdfReader
# from reportlab.pdfgen import canvas
# from reportlab.pdfbase import pdfmetrics
# from reportlab.pdfbase.ttfonts import TTFont
# from io import BytesIO
# import fitz
# import json
# import os

# def number_pdf(input_pdf_path, output_pdf_path, page_data_json):
#     try:
#         # Register font Verdana
#         font_path = r"C:\Windows\Fonts\verdana.ttf"
#         pdfmetrics.registerFont(TTFont("Verdana", font_path))

#         # Parse page data dari JSON
#         page_data = json.loads(page_data_json)
#         print(f"Page data: {page_data}")

#         # Buka PDF asli
#         doc = fitz.open(input_pdf_path)
#         total_pages = len(doc)
#         print(f"Total pages: {total_pages}")

#         temp_files = []
#         temp_file_paths = []  # Untuk menyimpan path file sementara di disk

#         for i in range(total_pages):
#             block = page_data.get(str(i), {}).get("block", "")
#             sheet = page_data.get(str(i), {}).get("sheet", "")
#             print(f"Page {i}: block={block}, sheet={sheet}")

#             if block or sheet:
#                 page = doc[i]
#                 rect = page.rect
#                 print(f"Page {i} dimensions: width={rect.width}, height={rect.height}")

#                 # Buat PDF sementara dengan ReportLab
#                 temp_pdf = BytesIO()
#                 c = canvas.Canvas(temp_pdf, pagesize=(rect.width, rect.height))
#                 c.setFont("Verdana", 6)

#                 # Posisi teks sesuai PyQt5 (A3: 740, 18)
#                 x, y = (740, 18)
#                 print(f"Drawing text at x={x + 12.5}, y={y + 10.2} for block, x={x + 1}, y={y - 0.4} for sheet")
#                 c.drawString(x + 12.5, y + 10.2, f"{block}")
#                 c.drawString(x + 1, y - 0.4, f"{sheet}")

#                 c.save()
#                 temp_pdf.seek(0)

#                 # Simpan PDF sementara untuk debugging
#                 with open(f"temp_page_{i}.pdf", "wb") as f:
#                     f.write(temp_pdf.getvalue())

#                 # Jika ingin menghapus temp
#                 # temp_file_path = f"temp_page_{i}.pdf"
#                 # temp_file_path = f"temp_page_{i}.pdf"
#                 # with open(temp_file_path, "wb") as f:
#                 #     f.write(temp_pdf.getvalue())
#                 # temp_file_paths.append(temp_file_path)  # Simpan path untuk dihapus nanti


#                 # Gabungkan dengan halaman asli
#                 temp_doc = fitz.open(stream=temp_pdf.read(), filetype="pdf")
#                 page.show_pdf_page(page.rect, temp_doc, 0)
#                 temp_files.append(temp_pdf)

#         # Simpan PDF yang telah diproses
#         doc.save(output_pdf_path)
#         doc.close()

#         # Bersihkan file sementara
#         for temp_file in temp_files:
#             temp_file.close()

	
#         # Hapus file sementara di disk
#         for temp_file_path in temp_file_paths:
#             if os.path.exists(temp_file_path):
#                 os.remove(temp_file_path)
#                 print(f"Deleted temporary file: {temp_file_path}")

#         print("PDF processed successfully")
#         return True
#     except Exception as e:
#         print(f"Error: {str(e)}")
#         return False

# if __name__ == "__main__":
#     if len(sys.argv) != 4:
#         print("Usage: python pdf_numbering.py <input_pdf> <output_pdf> <page_data_json>")
#         sys.exit(1)

#     input_pdf = sys.argv[1]
#     output_pdf = sys.argv[2]
#     page_data_json = sys.argv[3]

#     success = number_pdf(input_pdf, output_pdf, page_data_json)
#     sys.exit(0 if success else 1)

import sys
from PyPDF2 import PdfReader
from reportlab.pdfgen import canvas
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from io import BytesIO
import fitz
import json
import os

def number_pdf(input_pdf_path, output_pdf_path, json_file_path):
    try:
        font_path = r"C:\Windows\Fonts\verdana.ttf"
        pdfmetrics.registerFont(TTFont("Verdana", font_path))

        with open(json_file_path, 'r') as f:
            page_data = json.load(f)
        print(f"Page data: {page_data}")

        doc = fitz.open(input_pdf_path)
        total_pages = len(doc)
        print(f"Total pages: {total_pages}")

        temp_files = []
        for i in range(total_pages):
            block = page_data.get(str(i), {}).get("block", "")
            sheet = page_data.get(str(i), {}).get("sheet", "")
            print(f"Page {i}: block={block}, sheet={sheet}")

            if block or sheet:
                page = doc[i]
                rect = page.rect
                print(f"Page {i} dimensions: width={rect.width}, height={rect.height}")

                temp_pdf = BytesIO()
                c = canvas.Canvas(temp_pdf, pagesize=(rect.width, rect.height))
                c.setFont("Verdana", 6)

                # Sesuaikan koordinat untuk A3
                x, y = (740, 18)
                print(f"Drawing text at x={x + 12.5}, y={y + 10.2} for block, x={x + 1}, y={y - 0.4} for sheet")
                c.drawString(x + 12.5, y + 10.2, f"{block}")
                c.drawString(x + 1, y - 0.4, f"{sheet}")

                c.save()
                temp_pdf.seek(0)

                temp_doc = fitz.open(stream=temp_pdf.read(), filetype="pdf")
                page.show_pdf_page(page.rect, temp_doc, 0)
                temp_doc.close()  # Pastikan temp_doc ditutup
                temp_files.append(temp_pdf)

        # Simpan file dan verifikasi
        doc.save(output_pdf_path)
        doc.close()

        output_size = os.path.getsize(output_pdf_path)
        print(f"Output file size: {output_size} bytes")
        if output_size == 0:
            raise Exception("Output file is empty")

        for temp_file in temp_files:
            temp_file.close()

        print("PDF processed successfully")
        return True
    except Exception as e:
        print(f"Error: {str(e)}")
        return False

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Usage: python pdf_numbering.py <input_pdf> <output_pdf> <json_file_path>")
        sys.exit(1)

    input_pdf = sys.argv[1]
    output_pdf = sys.argv[2]
    json_file_path = sys.argv[3]

    success = number_pdf(input_pdf, output_pdf, json_file_path)
    sys.exit(0 if success else 1)