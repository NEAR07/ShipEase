# scripts/partlist_converter.py
import sys
import os
import re
import pandas as pd
from openpyxl import Workbook
from openpyxl.styles import Font, Border, Side, Alignment

# Fungsi process_file (dari kode sebelumnya)
def process_file(file_path):
    profile_types = []
    bar_numbers = []

    with open(file_path, 'r') as file:
        current_profile = None
        for line in file:
            line = line.replace('|', '').strip()
            profile_match = re.search(r"Profile type\s*:\s*(\S+)", line)
            if profile_match:
                current_profile = profile_match.group(1)
            bar_number_match = re.search(r"Bar number\s*:\s*(\d+)", line)
            if bar_number_match and current_profile:
                profile_types.append(current_profile)
                bar_numbers.append(int(bar_number_match.group(1)))

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
    # Proses file .list untuk resume
    resume_data = process_file(list_file_path)
    # Parse file .list untuk data utama
    data, header_data = parse_list_file(list_file_path)
    # Parse file .lst
    lst_data = parse_lst_file(lst_file_path)
    # Load file .csv
    csv_data = pd.read_csv(csv_file_path, delimiter=';', quotechar='"').values.tolist()

    wb = Workbook()
    ws = wb.active

    # Header baris pertama
    ws.append(["PROJECT =", header_data["Object"], "BLOCK =", header_data["Block"], "DATE =", header_data["Date"], "", "", "", "", "", "RESUME"])
    # Header baris kedua
    headers = [
        "PROFILE TYPE", "BAR-CODE", "LENGTH BAR", "MAT", "BAR NUMBER",
        "TOT LENGTH", "SCRAP IRON", "PART NAME", "Cut off Length", "", "",
        "Profile Type", "Height", "", "Width", "", "Thick1", "", "Thick2", "Bar number resume"
    ]
    ws.append(headers)

    # Font tebal untuk header
    bold_font = Font(bold=True)
    for cell in ws[1] + ws[2]:
        cell.font = bold_font

    # Set lebar kolom
    ws.column_dimensions['A'].width = 15
    ws.column_dimensions['B'].width = 16
    ws.column_dimensions['C'].width = 12
    ws.column_dimensions['D'].width = 9
    ws.column_dimensions['E'].width = 13
    ws.column_dimensions['F'].width = 12
    ws.column_dimensions['G'].width = 12
    ws.column_dimensions['H'].width = 21
    ws.column_dimensions['I'].width = 13
    ws.column_dimensions['L'].width = 18
    ws.column_dimensions['M'].width = 7
    ws.column_dimensions['N'].width = 7
    ws.column_dimensions['O'].width = 7
    ws.column_dimensions['P'].width = 7
    ws.column_dimensions['Q'].width = 7
    ws.column_dimensions['R'].width = 7
    ws.column_dimensions['S'].width = 7
    ws.column_dimensions['T'].width = 20

    # Set tinggi baris
    ws.row_dimensions[2].height = 45.75  # Tinggi baris header kedua

    # Fungsi untuk mengkonversi kolom numerik
    def convert_to_number(ws, row):
        columns_to_convert = [2, 3, 5, 6, 7, 9, 13, 14, 15, 16, 17, 19]  # Kolom yang akan dikonversi
        for col in columns_to_convert:
            cell = ws.cell(row=row, column=col)
            if cell.value is not None:
                try:
                    cell.value = float(cell.value)
                except ValueError:
                    pass

    # Fungsi untuk mengkonversi kolom barcode
    def convert_to_number_barcode(ws, row):
        columns_to_convert = [2]  # Kolom BAR-CODE
        for col in columns_to_convert:
            cell = ws.cell(row=row, column=col)
            if cell.value is not None:
                try:
                    value_as_int = int(float(cell.value))
                    cell.value = str(value_as_int)
                    cell.number_format = '@'
                except ValueError:
                    pass

    # Isi data
    bcode_tracker = {key: 0 for key in lst_data}
    for i in range(len(data["Part"])):
        part_value = data["Part"][i]
        cut_off_length = data["Cut off Length"][i]

        if cut_off_length in lst_data:
            index = bcode_tracker[cut_off_length] % len(lst_data[cut_off_length])
            part_value = lst_data[cut_off_length][index]
            bcode_tracker[cut_off_length] += 1

        # Split profile type
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

    # Set tinggi baris untuk data
    for row in range(3, ws.max_row + 1):
        ws.row_dimensions[row].height = 25

    # Konversi kolom numerik
    for row in range(3, ws.max_row + 1):
        convert_to_number(ws, row)

    # Cocokkan data CSV untuk kolom BAR-CODE
    for excel_row in range(3, ws.max_row + 1):
        excel_col_a = ws.cell(row=excel_row, column=1).value
        excel_col_c = ws.cell(row=excel_row, column=3).value
        excel_col_d = ws.cell(row=excel_row, column=4).value

        excel_col_a_str = str(excel_col_a).strip() if excel_col_a is not None else ""
        excel_col_d_str = str(excel_col_d).strip() if excel_col_d is not None else ""
        excel_col_c_int = int(excel_col_c) if isinstance(excel_col_c, float) else excel_col_c
        excel_col_c_str = str(excel_col_c_int).strip() if excel_col_c_int is not None else ""

        for csv_row in csv_data:
            if len(csv_row) < 4:
                continue
            # Konversi semua elemen CSV ke string sebelum strip
            csv_col_b = str(csv_row[1]).strip()
            csv_col_c = str(csv_row[2]).strip()
            csv_col_d = str(csv_row[3]).strip()

            if excel_col_a_str == csv_col_b and excel_col_c_str == csv_col_c and excel_col_d_str == csv_col_d:
                ws.cell(row=excel_row, column=2).value = csv_row[0]
                convert_to_number_barcode(ws, excel_row)
                break

    # Set alignment
    for row in ws.iter_rows(min_row=2, max_row=ws.max_row):
        for cell in row:
            if 1 <= cell.column <= 9 or 12 <= cell.column <= 20:
                cell.alignment = Alignment(horizontal='center', vertical='center')
            if cell.column in [1, 8, 12]:
                cell.alignment = Alignment(horizontal='left', vertical='center')

    # Remove duplicate values in columns A to G
    unique_rows = set()
    for row in range(3, ws.max_row + 1):
        row_data = tuple(ws.cell(row=row, column=col).value for col in range(1, 8))
        if row_data in unique_rows:
            for col in range(1, 8):
                ws.cell(row=row, column=col).value = None
        else:
            unique_rows.add(row_data)

    # Set borders
    thin_border = Border(
        left=Side(style="thin"),
        right=Side(style="thin"),
        top=Side(style="thin"),
        bottom=Side(style="thin")
    )
    top_thick_border = Border(
        left=Side(style="thin"),
        right=Side(style="thin"),
        top=Side(style="thick"),
        bottom=Side(style="thin")
    )
    thick_border = Border(
        left=Side(style="thick"),
        right=Side(style="thick"),
        top=Side(style="thick"),
        bottom=Side(style="thick")
    )

    # Border untuk header baris kedua
    for cell in ws[2]:
        if 1 <= cell.column <= 9 or 12 <= cell.column <= 20:
            cell.border = thick_border

    # Border untuk data
    for row in ws.iter_rows(min_row=3, max_row=ws.max_row):
        if all(ws.cell(row=row[0].row, column=col).value not in [None, "", " "] for col in range(1, 10)):
            for cell in row:
                if 1 <= cell.column <= 9:
                    cell.border = top_thick_border
        else:
            for cell in row:
                if 1 <= cell.column <= 9 and cell.value not in [None, "", " "]:
                    cell.border = thin_border

    # Border untuk kolom 12-20 pada baris terakhir dengan data
    last_row_with_data = 0
    for row in ws.iter_rows(min_row=2, max_row=ws.max_row, min_col=12, max_col=20):
        if any(cell.value not in [None, "", " "] for cell in row):
            last_row_with_data = row[0].row

    if last_row_with_data > 0:
        for cell in ws.iter_rows(min_row=2, max_row=last_row_with_data, min_col=12, max_col=20):
            for sub_cell in cell:
                sub_cell.border = thin_border

    # Simpan file Excel
    wb.save(output_file_path)
    print(f"Excel file generated successfully at: {output_file_path}")

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