"""
This script modernizes how tags are listed in the frontmatter of markdown files.

OLD FORMAT:
tags:
  - circuit
  - programming
  - radio

NEW FORMAT:
tags: circuit, programming, radio
"""
import os
import glob
import time
import shutil

BLOGPATH = "../wwwroot/blog"


def backupMarkdownFiles():
    mdFiles = glob.glob(BLOGPATH+"/*/index.md")
    mdFiles = [os.path.abspath(x) for x in mdFiles]
    for filePath in mdFiles:
        filePath2 = filePath + '.' + str(int(time.time())) + ".backup"
        print("BACKING UP:", filePath2)
        shutil.copy(filePath, filePath2)


def deleteMarkdownBackups():
    mdFiles = glob.glob(BLOGPATH+"/*/*.backup")
    mdFiles = [os.path.abspath(x) for x in mdFiles]
    for filePath in mdFiles:
        print("DELETING:", filePath)
        os.remove(filePath)


if __name__ == "__main__":
    print("DONE")

    mdFiles = glob.glob(BLOGPATH+"/*/index.md")
    mdFiles = [os.path.abspath(x) for x in mdFiles]
    for filePath in mdFiles:
        print(filePath)
        with open(filePath, 'rb') as f:
            lines = f.readlines()

        if (lines[0].startswith(b'---') == False):
            print("NO HEADER")
            continue

        tags = []
        lastHeaderLine = 2
        for i in range(1, len(lines)):
            line = lines[i].decode("utf-8")
            if (line.strip().startswith("- ")):
                tags.append(line.split("-", 1)[1].strip())
            if (len(line.strip()) == 0):
                lines[i] = ""
            if (line.startswith('---')):
                lastHeaderLine = i
                break

        for i in range(1, lastHeaderLine):
            line = lines[i].decode("utf-8")
            if line.strip().startswith("tags"):
                lines[i] = "tags: "+", ".join(tags)+"\r\n"
            if line.strip().startswith("- "):
                lines[i] = ""
            if (type(lines[i]) != bytes):
                lines[i] = lines[i].encode("utf-8")

        with open(filePath, 'wb') as f:
            f.writelines(lines)
