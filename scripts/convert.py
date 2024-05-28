from pdf2docx import Converter
import sys
import os

if len(sys.argv) != 3:
    print("Usage: convert.py <input_pdf_path> <output_docx_path>")
    sys.exit(1)

input_pdf_path = sys.argv[1]
output_docx_path = sys.argv[2]

cv = Converter(input_pdf_path)
cv.convert(output_docx_path, start=0, end=None)
cv.close()

print("Conversion completed.")
