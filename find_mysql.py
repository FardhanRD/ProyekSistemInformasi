import os

paths = ["C:\\laragon\\bin\\mysql", "C:\\xampp\\mysql\\bin", "C:\\mysql\\bin"]
found = []
for p in paths:
    if os.path.exists(p):
        for root, dirs, files in os.walk(p):
            if "mysql.exe" in files:
                found.append(os.path.join(root, "mysql.exe"))

print("Found mysql.exe at:")
for f in found:
    print(f)
