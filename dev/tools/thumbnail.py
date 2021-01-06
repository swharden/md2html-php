"""
This script makes thumbnails of images in subfoldes
"""

import glob
import os
import shutil
from PIL import Image

MAX_WIDTH = 750
MAX_HEIGHT = 500
THUMB_EXT = "_thumb.jpg"

for folder in glob.glob("./*"):

    images = []
    for ext in ["jpeg", "jpg", "png"]:
        images += glob.glob(folder+"/*."+ext)

    for image in images:
        fileName = os.path.basename(image)

        if fileName.endswith(THUMB_EXT):
            continue

        fnameWithoutExt = ".".join(fileName.split(".")[:-1])
        fname2 = os.path.join(folder, fnameWithoutExt+THUMB_EXT)
        if os.path.exists(fname2):
            continue

        im = Image.open(image)
        im = im.convert('RGB')
        width, height = im.size

        sizeFracX = width / MAX_WIDTH
        sizeFracY = height / MAX_HEIGHT

        imageNeedsReduction = sizeFracX > 1 or sizeFracY > 1
        imageIsPng = image.endswith(".png")
        imageNeedsThumb = imageNeedsReduction or image.endswith(".png")
        if not imageNeedsThumb:
            continue

        if imageNeedsReduction:
            isWiderThanHigh = width > height
            sizeRatio = width / height
            if isWiderThanHigh:
                newX = MAX_WIDTH
                newY = int(MAX_WIDTH / sizeRatio)
                assert newX > newY
            else:
                newY = MAX_HEIGHT
                newX = int(MAX_HEIGHT * sizeRatio)
                assert newY >= newX
        else:
            newX = width
            newY = height

        im = im.resize((newX, newY))
        im.save(fname2, quality=90)
        print(f"{fileName} ({width}, {height}) -> ({newX}, {newY})")
