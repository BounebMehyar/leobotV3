import csv
import json
import os

# Directory containing the JSON files
json_directory = 'depertments'
# Output CSV file path
csv_file_path = 'questions_departements.csv'

# Collect all JSON filenames in the directory
json_files = [f for f in os.listdir(json_directory) if f.endswith('.json')]

# Prepare to write to CSV
with open(csv_file_path, mode='w', newline='', encoding='utf-8') as csv_file:
    csv_writer = csv.writer(csv_file)
    csv_writer.writerow(['question', 'departement'])  # Write headers

    # Process each JSON file
    for json_file in json_files:
        departement = json_file.replace('.json', '')  # Extract departement name from filename
        with open(os.path.join(json_directory, json_file), 'r', encoding='utf-8') as file:
            questions = json.load(file)
            for question in questions:
                csv_writer.writerow([question['question'], departement])

print("All JSON files have been merged into one CSV file.")
