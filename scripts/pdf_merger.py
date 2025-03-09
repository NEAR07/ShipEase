import sys
import os
from PyPDF2 import PdfMerger

def merge_pdfs(input_dir, output_path):
    try:
        merger = PdfMerger()
        
        # Get sorted list of PDF files
        pdf_files = sorted([f for f in os.listdir(input_dir) if f.lower().endswith('.pdf')])
        
        if not pdf_files:
            raise ValueError("No PDF files found in directory")
            
        for pdf_file in pdf_files:
            pdf_path = os.path.join(input_dir, pdf_file)
            with open(pdf_path, 'rb') as f:
                merger.append(f)
        
        with open(output_path, 'wb') as f:
            merger.write(f)
            
        return True
    except Exception as e:
        print(f"Error: {str(e)}")
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python pdf_merger.py <input_directory> <output_file>")
        sys.exit(1)
        
    input_dir = sys.argv[1]
    output_file = sys.argv[2]
    
    success = merge_pdfs(input_dir, output_file)
    sys.exit(0 if success else 1)