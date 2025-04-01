# scripts/partlist_converter.py
import sys
import os
import re
import pandas as pd
from openpyxl import Workbook
from openpyxl.styles import Font, Border, Side, Alignment
import logging

# Fungsi process_file (dari kode sebelumnya)
# Setup logging
logging.basicConfig(filename='partlist_converter.log', level=logging.DEBUG, 
                    format='%(asctime)s - %(levelname)s - %(message)s')

def process_file(file_path):
    logging.info(f"Processing file: {file_path}")
    profile_types = []
    bar_numbers = []
    try:
        with open(file_path, 'r') as file:
            current_profile = None
            for line in file:
                line = line.replace('|', '').strip()
                profile_match = re.search(r"Profile type\s*:\s*(\S+)", line)
                if profile_match:
                    current_profile = profile_match.group(1)
                    logging.debug(f"Found profile type: {current_profile}")
                bar_number_match = re.search(r"Bar number\s*:\s*(\d+)", line)
                if bar_number_match and current_profile:
                    profile_types.append(current_profile)
                    bar_numbers.append(int(bar_number_match.group(1)))
                    logging.debug(f"Found bar number: {bar_number_match.group(1)}")
    except Exception as e:
        logging.error(f"Error processing file {file_path}: {str(e)}")
        raise

    profile_summary = {}
    for profile, bar in zip(profile_types, bar_numbers):
        if profile not in profile_summary:
            profile_summary[profile] = []
        profile_summary[profile].append(bar)

    profile_type_resumes = []
    bar_number_resumes = []
    for profile, bars in profile_summary.items():
        profile_type_resumes.append(profile)
        bar_number_resumes.append(len(bars))

    df = pd.DataFrame({
        "Profile type resume": profile_type_resumes,
        "Bar number resume": bar_number_resumes
    })
    logging.info("Processed file into DataFrame successfully")
    return df

# Fungsi split_profile_type (dari kode sebelumnya)
def split_profile_type(profile_type):
    pattern = r'[A-Za-z]+|\d+|X'
    matches = re.findall(pattern, profile_type)
    default_values = ['X', '0', 'X', '0', 'X', '0', 'X', '0']
    matches.extend(default_values[len(matches):])
    return matches[:8]

# Fungsi parse_list_file (dari kode sebelumnya)
def parse_list_file(file_path):
    data = {
        "Profile type": [],
        "Bar-codenr": [],
        "Length bar": [],
        "Material": [],
        "Bar number": [],
        "Total length": [],
        "Scrap-iron": [],
        "Part": [],
        "Cut off Length": []
    }
    header_data = {"Object": "", "Block": "", "Date": ""}

    with open(file_path, 'r') as file:
        part_section = False
        common_data = {}
        for line in file:
            line = line.replace('|', '').strip()
            object_match = re.search(r"Object:\s*(\d+)", line)
            block_match = re.search(r"Block:\s*(\d+)", line)
            date_match = re.search(r"Date:\s*(\S+)", line)

            if object_match:
                header_data["Object"] = object_match.group(1)
            if block_match:
                header_data["Block"] = block_match.group(1)
            if date_match:
                header_data["Date"] = date_match.group(1)

            if re.match(r"^Part\s+Cut off Length", line):
                part_section = True
                continue

            if part_section and re.match(r"^\d+\s+\d+", line):
                parts = line.split()
                if len(parts) >= 2:
                    data["Part"].append(parts[0])
                    data["Cut off Length"].append(parts[1])
                    for key in common_data:
                        data[key].append(common_data[key])
                continue

            profile_match = re.search(r"Profile type\s*:\s*(.*)", line)
            barcode_match = re.search(r"Bar-codenr\s*:\s*(.*)", line)
            length_bar_match = re.search(r"Length bar\s*:\s*(.*)", line)
            material_match = re.search(r"Material\s*:\s*(.*)", line)
            bar_number_match = re.search(r"Bar number\s*:\s*(.*)", line)
            total_length_match = re.search(r"Total length\s*:\s*(.*)", line)
            scrap_iron_match = re.search(r"Scrap-iron\s*:\s*(.*)", line)

            if profile_match:
                common_data["Profile type"] = profile_match.group(1).split()[0].strip()
            if barcode_match:
                common_data["Bar-codenr"] = barcode_match.group(1).strip()
            if length_bar_match:
                common_data["Length bar"] = length_bar_match.group(1).split()[0].strip()
            if material_match:
                common_data["Material"] = material_match.group(1).split()[0].strip()
            if bar_number_match:
                common_data["Bar number"] = bar_number_match.group(1).strip()
            if total_length_match:
                common_data["Total length"] = total_length_match.group(1).strip()
            if scrap_iron_match:
                common_data["Scrap-iron"] = scrap_iron_match.group(1).strip()

    return data, header_data

# Fungsi parse_lst_file (dari kode sebelumnya)
def parse_lst_file(lst_file_path):
    lst_data = {}
    with open(lst_file_path, 'r') as file:
        for line in file:
            line = line.strip()
            if line.startswith('*'):
                parts = line.split('|')
                if len(parts) >= 6:
                    bcode = parts[0].strip('* ')
                    length = parts[5].strip()
                    if length not in lst_data:
                        lst_data[length] = []
                    lst_data[length].append(bcode)
    return lst_data

# Fungsi untuk memproses dan menggabungkan data ke Excel
def convert_list_to_xlsx(list_file_path, lst_file_path, csv_file_path, output_file_path):
    logging.info(f"Starting conversion: {list_file_path}, {lst_file_path}, {csv_file_path} -> {output_file_path}")
    try:
        resume_data = process_file(list_file_path)
        data, header_data = parse_list_file(list_file_path)
        lst_data = parse_lst_file(lst_file_path)
        csv_data = pd.read_csv(csv_file_path, delimiter=';', quotechar='"').values.tolist()
        logging.info("All input files parsed successfully")

        wb = Workbook()
        ws = wb.active
        ws.append(["PROJECT =", header_data["Object"], "BLOCK =", header_data["Block"], "DATE =", header_data["Date"], "", "", "", "", "", "RESUME"])
        headers = [
            "PROFILE TYPE", "BAR-CODE", "LENGTH BAR", "MAT", "BAR NUMBER",
            "TOT LENGTH", "SCRAP IRON", "PART NAME", "Cut off Length", "", "",
            "Profile Type", "Height", "", "Width", "", "Thick1", "", "Thick2", "Bar number resume"
        ]
        ws.append(headers)
        logging.debug("Excel headers written")

        bold_font = Font(bold=True)
        for cell in ws[1] + ws[2]:
            cell.font = bold_font

        # Set lebar kolom
        ws.column_dimensions['B'].width = 16
        ws.column_dimensions['D'].width = 9
        ws.column_dimensions['H'].width = 21
        ws.column_dimensions['L'].width = 18
        ws.column_dimensions['T'].width = 20
        ws.column_dimensions['A'].width = 15
        for col in ['C', 'F', 'G']:
            ws.column_dimensions[col].width = 12
        for col in ['E', 'I']:
            ws.column_dimensions[col].width = 13
        for col in range(14, 17):
            ws.column_dimensions[chr(64 + col)].width = 7

        # Isi data
        bcode_tracker = {key: 0 for key in lst_data}
        for i in range(len(data["Part"])):
            part_value = data["Part"][i]
            cut_off_length = data["Cut off Length"][i]

            if cut_off_length in lst_data:
                index = bcode_tracker[cut_off_length] % len(lst_data[cut_off_length])
                part_value = lst_data[cut_off_length][index]
                bcode_tracker[cut_off_length] += 1

            split_profile = split_profile_type(resume_data["Profile type resume"][i]) if i < len(resume_data["Profile type resume"]) else ['X', '0', 'X', '0', 'X', '0', 'X', '0']
            row = [
                data["Profile type"][i],
                data["Bar-codenr"][i],
                data["Length bar"][i],
                data["Material"][i],
                data["Bar number"][i],
                data["Total length"][i],
                data["Scrap-iron"][i],
                part_value,
                cut_off_length,
                "",
                ""
            ] + split_profile + [resume_data["Bar number resume"][i] if i < len(resume_data["Bar number resume"]) else ""]
            ws.append(row)

            wb.save(output_file_path)
            logging.info(f"Excel file saved successfully at: {output_file_path}")
    except Exception as e:
        logging.error(f"Error during conversion: {str(e)}")
        raise

if __name__ == "__main__":
    if len(sys.argv) != 5:
        print("Usage: python partlist_converter.py <list_file> <lst_file> <csv_file> <output_file>")
        sys.exit(1)

    list_file = sys.argv[1]
    lst_file = sys.argv[2]
    csv_file = sys.argv[3]
    output_file = sys.argv[4]

    convert_list_to_xlsx(list_file, lst_file, csv_file, output_file)
    print(f"Excel file generated successfully at: {output_file}")