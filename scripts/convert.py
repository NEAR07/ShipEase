
from pdf2docx import Converter
import sys
import os

# Get the input and output file paths from command-line arguments
input_pdf_path = sys.argv[1]
output_docx_filename = os.path.basename(input_pdf_path).replace('.pdf', '.docx')
output_docx_path = os.path.join(os.path.expanduser("~"), 'Downloads', output_docx_filename)

# Convert PDF to DOCX
cv = Converter(input_pdf_path)
cv.convert(output_docx_path, start=0, end=None)
cv.close()

print("Conversion completed.")

