import os
import sys

start_path = "C:\\Users\\Acer"
print(f"Scanning {start_path} for large directories...")

dir_sizes = {}
count = 0

for root, dirs, files in os.walk(start_path):
    # Skip some massive folders we don't want to crawl or don't have permission to
    if any(p in root for p in ["AppData\\Local\\Microsoft", "AppData\\Local\\Google\\Chrome\\User Data"]):
        continue
    
    current_size = 0
    for f in files:
        fp = os.path.join(root, f)
        try:
            current_size += os.path.getsize(fp)
        except Exception:
            pass
    
    if current_size > 50 * 1024 * 1024: # > 50 MB
        dir_sizes[root] = current_size
    
    count += 1
    if count % 5000 == 0:
        # Keep it running fast, limit total dirs scanned to 50000
        if count > 50000:
            break

sorted_dirs = sorted(dir_sizes.items(), key=lambda x: x[1], reverse=True)
print("\nTop large directories:")
for d, sz in sorted_dirs[:30]:
    print(f"{sz / (1024*1024):.2f} MB - {d}")
