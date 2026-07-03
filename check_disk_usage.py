import os

start_path = "C:\\Users\\Acer\\.gemini\\antigravity"
print(f"Checking sizes under {start_path}...")

sizes = []
for root, dirs, files in os.walk(start_path):
    for f in files:
        fp = os.path.join(root, f)
        try:
            sz = os.path.getsize(fp)
            sizes.append((fp, sz))
        except Exception:
            pass

sizes.sort(key=lambda x: x[1], reverse=True)
print("\nTop 50 largest files:")
for fp, sz in sizes[:50]:
    print(f"{sz / (1024*1024):.2f} MB - {fp}")
