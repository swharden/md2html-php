<?php

require_once('misc.php');

/** This class stores details about an article by only inspecting its header */
class ArticleInfo
{
    public string $path;
    public string $folderName;
    public int $modified;
    public string $title = "";
    public string $description = "";
    public int $dateTime;
    public string $dateString;
    public string $dateStringShort;
    public array $tags = array();
    public array $tagsSanitized = array();
    public int $contentOffset = 0;

    function __construct(string $markdownFilePath)
    {
        if (is_file($markdownFilePath) == false)
            throw new Exception("Markdown file does not exist: " . $markdownFilePath);

        $this->path = realpath($markdownFilePath);
        $this->folderName = basename(dirname($this->path));
        $this->modified = filemtime($this->path);
        $this->dateTime = $this->modified;
        $this->updateDateStrings();
        $this->processHeaderItems();
    }

    private function updateDateStrings()
    {
        $this->dateString = date("F jS, Y", $this->dateTime);
        $this->dateStringShort = date("Y-m-d", $this->dateTime);
    }

    private function processHeaderItems()
    {
        $file = fopen($this->path, "r");

        $firstLine = fgets($file);
        if (startsWith($firstLine, '---') == false) {
            fclose($file);
            return;
        }

        while (!feof($file)) {
            $line = fgets($file);
            if (startsWith($line, '---')) {
                break;
            }
            $parts = explode(":", $line, 2);
            if (count($parts) == 2) {
                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $this->processHeaderItem($key, $value);
            }
        }

        $this->contentOffset = ftell($file);
        fclose($file);
    }

    private function processHeaderItem(string $key, string $value)
    {
        if ($key == "title") {
            $this->title = $value;
            return;
        }

        if ($key == "description") {
            $this->description = $value;
            return;
        }

        if ($key == "date") {
            $dateParts = date_parse($value);
            $this->dateTime = mktime(
                $dateParts['hour'],
                $dateParts['minute'],
                $dateParts['second'],
                $dateParts['month'],
                $dateParts['day'],
                $dateParts['year']
            );
            $this->updateDateStrings();
            return;
        }

        if ($key == "tags") {
            foreach (explode(',', $value) as $tag) {
                $this->tags[] .= trim($tag);
                $this->tagsSanitized[] .= sanitizeLinkUrl(trim($tag));
            }
            return;
        }
    }
}
