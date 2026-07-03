import re

print("Scanning for INSERT statements in db_apk_main_backup.sql...")
with open("D:\\PABW\\Proyek_MOVR\\db_apk_main_backup.sql", "r", encoding="utf-8", errors="ignore") as f:
    for line in f:
        if line.startswith("INSERT INTO"):
            # Print table name and first 100 chars of insert
            m = re.match(r"INSERT INTO `([^`]+)` VALUES", line)
            if m:
                print(f"Table: {m.group(1)} - {len(line)} bytes")
