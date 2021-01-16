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
        with open(filePath, errors='ignore') as f:
            lines = f.readlines()
        print(len(lines))
        with open(filePath, 'w', errors='ignore') as f:
            f.writelines(lines)
