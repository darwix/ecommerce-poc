import os
import zipfile

OUT = os.path.join("..", "..", "deploy", "acme-theme.zip")
SKIP_DIRS = {"node_modules"}
SKIP_FILES = {"package.json", "package-lock.json", "zip.py"}
SKIP_EXT = (".scss", ".map")

with zipfile.ZipFile(OUT, "w", zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk("."):
        dirs[:] = [d for d in dirs if d not in SKIP_DIRS]
        for f in files:
            if f in SKIP_FILES or f.endswith(SKIP_EXT):
                continue
            path = os.path.join(root, f)
            # forward slashes: PowerShell Compress-Archive writes backslashes, WP rejects them
            z.write(path, "acme/" + os.path.relpath(path, ".").replace(os.sep, "/"))
print("wrote", os.path.abspath(OUT))
