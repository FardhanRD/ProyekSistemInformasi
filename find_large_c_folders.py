import os

print("Scanning C:\\ for large root folders...")
ignore_list = ["Windows", "Program Files", "Program Files (x86)", "ProgramData", "System Volume Information", "$Recycle.Bin", "Config.Msi"]

root_dirs = []
try:
    for name in os.listdir("C:\\"):
        if name in ignore_list:
            continue
        path = os.path.join("C:\\", name)
        if os.path.isdir(path):
            root_dirs.append(path)
except Exception as e:
    print(f"Error listing C:\\: {e}")

dir_sizes = []
for d in root_dirs:
    total_size = 0
    try:
        for root, dirs, files in os.walk(d):
            # Speed up AppData scanning
            if "AppData\\Local\\Microsoft" in root:
                continue
            for f in files:
                try:
                    total_size += os.path.getsize(os.path.join(root, f))
                except Exception:
                    pass
        dir_sizes.append((d, total_size))
    except Exception as e:
        print(f"Error scanning {d}: {e}")

dir_sizes.sort(key=lambda x: x[1], reverse=True)
print("\nRoot folders on C:\\ by size:")
for d, sz in dir_sizes:
    print(f"{sz / (1024*1024*1024):.2f} GB - {d}")
