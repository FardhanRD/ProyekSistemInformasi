import os
import time

now = time.time()
fifteen_minutes = 15 * 60

print("Recent screenshots:")
# Search common folders: Temp, AppData, Katalon workspaces
search_paths = [
    "C:\\Users\\Acer\\AppData\\Local\\Temp",
    "D:\\KATALON_TUBES_PPL"
]

for sp in search_paths:
    if os.path.exists(sp):
        for root, dirs, files in os.walk(sp):
            for f in files:
                if f.endswith(".png") or f.endswith(".jpg"):
                    fp = os.path.join(root, f)
                    try:
                        mtime = os.path.getmtime(fp)
                        if now - mtime < fifteen_minutes:
                            print(f"{fp} - {time.ctime(mtime)}")
                    except Exception:
                        pass
