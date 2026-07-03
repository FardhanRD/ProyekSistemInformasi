with open("D:\\PABW\\Proyek_MOVR\\db_apk_main_backup.sql", "r", encoding="utf-8", errors="ignore") as f:
    for i in range(50):
        line = f.readline()
        if not line:
            break
        print(line.strip())
