import os
import time

dir_path = "D:\\KATALON_TUBES_PPL\\MOVR_Admin_testing"
now = time.time()
five_minutes = 5 * 60

print("Recent files:")
for root, dirs, files in os.walk(dir_path):
    for f in files:
        if f.endswith(".log") or f.endswith(".xml") or f.endswith(".png") or f.endswith(".html"):
            fp = os.path.join(root, f)
            try:
                mtime = os.path.getmtime(fp)
                if now - mtime < five_minutes:
                    print(f"{fp} - {time.ctime(mtime)}")
            except Exception as e:
                pass
