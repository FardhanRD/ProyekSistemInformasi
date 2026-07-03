import os

print("Scanning C:\\Users\\Acer children...")
dir_sizes = []
for name in os.listdir("C:\\Users\\Acer"):
    path = os.path.join("C:\\Users\\Acer", name)
    if os.path.isdir(path):
        total_size = 0
        try:
            for root, dirs, files in os.walk(path):
                # Speed up scanning by ignoring known large library dirs if needed, but let's do a broad count
                for f in files:
                    try:
                        total_size += os.path.getsize(os.path.join(root, f))
                    except Exception:
                        pass
            dir_sizes.append((path, total_size))
        except Exception:
            pass

dir_sizes.sort(key=lambda x: x[1], reverse=True)
print("\nFolders in C:\\Users\\Acer by size:")
for d, sz in dir_sizes:
    print(f"{sz / (1024*1024*1024):.2f} GB - {d}")
