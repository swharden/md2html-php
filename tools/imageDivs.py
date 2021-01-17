"""
The goal of this script is to automatically replace old div tags in markdown with new ones.

OLD:
<div class="center">

NEW
<div class="text-center">

"""
import os
import glob
import time
import shutil

BLOGPATH = "../wwwroot/blog"

if __name__ == "__main__":
    mdFiles = glob.glob(BLOGPATH+"/*/index.md")
    mdFiles = [os.path.abspath(x) for x in mdFiles]
    for filePath in mdFiles:

        with open(filePath, 'rb') as f:
            lines = f.readlines()

        for i, originalLine in enumerate(lines):
            line = originalLine.decode('utf-8')
            if not line.startswith("<div class"):
                continue

            if "center" in line and not "text-center" in line:
                line = line.replace("center", "text-center")

            if "border" in line and not "img-border" in line:
                line = line.replace("border", "img-border")

            if "medium" in line and not "img-medium" in line:
                line = line.replace("medium", "img-medium")

            if "small" in line and not "img-small" in line:
                line = line.replace("small", "img-small")

            if "thumbnail" in line and not "img-thumbnail" in line:
                line = line.replace("thumbnail", "img-thumbnail")

            if originalLine.decode('utf-8').strip() != line.strip():
                print()
                print(filePath)
                print(line)
                lines[i] = line.encode('utf-8')

        with open(filePath, 'wb') as f:
            f.writelines(lines)
