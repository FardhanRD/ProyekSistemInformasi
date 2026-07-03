import re

print("Scanning for INSERT statements in movr_database_dump.sql...")
with open("D:\\PABW\\Proyek_MOVR\\storage\\app\\movr_database_dump.sql", "r", encoding="utf-8", errors="ignore") as f:
    for line in f:
        if line.startswith("INSERT INTO"):
            m = re.match(r"INSERT INTO `([^`]+)` VALUES", line)
            if m:
                print(f"Table: {m.group(1)} - {len(line)} bytes")
