<?php

require_once(__DIR__ . '/Parsedown.php');

/* This class is a Parsedown wrapper with extra custom functions:
 *   - frontmatter (header) defines title, description, tags, and date
 *   - ![](image.jpg) adds an image tag with width, height, alt, and links to itself
 *   - headings are automatically anchored
 *   - ![](TOC) inserts table of contents for all headings
 *   - ![](youTubeUrl) embeds YouTube video
 *   - local images will be measured so width and height tags can be defined
 */
class ParsedMarkdown
{
    // meta
    public string $title;
    public string $description;
    public array $tags = [];
    public string $date;

    // content
    public string $html;

    /**
     * Populate information about a webpage from raw markdown text.
     *  - Markdown will be converted to HTML (with special replacements for things like YouTube videos)
     *  - If frontmatter is present, it will be read to populate metadata information.
     *  - If a folder path is given, ![](images) will be measured to populate width and height tags.
     */
    public function __construct(string $markdownText, string $mdFolderPath = null)
    {
        // custom modifications to the markdown
        $mdLines = $this->processHeaderAndReturnBodyLines($markdownText);
        $mdLines = $this->updateSpecialCodes($mdLines, $mdFolderPath);

        // convert markdown to html
        $Parsedown = new Parsedown();
        $html = $Parsedown->text(implode("\n", $mdLines));

        // custom modifications to the HTML
        $html = $this->addAnchorsToHeadingsAndUpdateTOC($html);
        $html = $this->prettyPrintCodeBlocks($html);
        $this->html = $html;
    }

    private function processHeaderAndReturnBodyLines(string $markdownText): array
    {
        $mdLines = explode("\n", $markdownText);

        if (!$this->startsWith($mdLines[0], '---'))
            return $mdLines;

        for ($i = 1; $i < count($mdLines); $i++) {
            if ($this->startsWith($mdLines[$i], '---'))
                break;
            $parts = explode(":", $mdLines[$i], 2);
            if (count($parts) == 2) {
                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $this->processHeaderItem($key, $value);
            }
        }

        return array_slice($mdLines, $i + 1);
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
            $this->date = mktime(
                $dateParts['hour'],
                $dateParts['minute'],
                $dateParts['second'],
                $dateParts['month'],
                $dateParts['day'],
                $dateParts['year']
            );
            return;
        }

        if ($key == "tags") {
            foreach (explode(',', $value) as $tag) {
                $this->tags[] .= trim($tag);
            }
            return;
        }
    }

    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0)
            return true;
        return (substr($string, -$len) === $endString);
    }

    function sanitizeLinkUrl($url): string
    {
        $valid = "";
        foreach (str_split(strtolower(trim($url))) as $char)
            $valid .= (ctype_alnum($char)) ? $char : "-";
        while (strpos($valid, "--"))
            $valid = str_replace("--", "-", $valid);
        return trim($valid, '-');
    }

    function updateSpecialCodes(array $mdLines, string $mdFolderPath): array
    {
        $isInCodeBlock = false;
        for ($i = 0; $i < count($mdLines); $i++) {
            $trimmedLine = trim($mdLines[$i]);
            if ($this->startsWith($trimmedLine, "```")) {
                $isInCodeBlock = !$isInCodeBlock;
            }
            if ($isInCodeBlock)
                continue;
            $isSpecialLink = $this->startsWith($trimmedLine, "![](") && $this->endsWith($trimmedLine, ")");
            if ($isSpecialLink) {
                $url = substr($trimmedLine, 4, strlen($trimmedLine) - 5);
                $mdLines[$i] = $this->getSpecialCode($url, $mdFolderPath);
            }
        }
        return $mdLines;
    }

    // Special codes are markdown lines formatted like ![](this) where "this" is a URL
    function getSpecialCode(string $url, string $mdFolderPath): string
    {
        // Embedded YouTube video
        $isYouTubeUrl =
            strstr($url, "youtube.com/") ||
            strstr($url, "youtu.be/");
        if ($isYouTubeUrl)
            return $this->getYouTubeHtml($url);

        // Embedded image
        $isImageTag =
            $this->endsWith($url, ".png") ||
            $this->endsWith($url, ".gif") ||
            $this->endsWith($url, ".bmp") ||
            $this->endsWith($url, ".jpg") ||
            $this->endsWith($url, ".jpeg");
        if ($isImageTag)
            return $this->getImageHtml($url, $mdFolderPath);

        // Table of contents
        if ($url == "TOC") {
            return "<!--TOC-->";
        }

        // No matching special case, so show an old fashioned link
        return "<a href='$url'>$url</a>";
    }

    function getYouTubeHtml(string $url): string
    {
        $url = "https://www.youtube.com/embed/" . basename($url);
        $html = "<div class='ratio ratio-16x9'><iframe src='$url' class='border shadow' frameborder='0' allowfullscreen " .
            "allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'></iframe></div>";
        return "<div class='container my-5'>$html</div>";
    }

    function getImageHtml(string $url, string $markdownFolderPath): string
    {
        $alt = basename($url);
        $alt = substr($url, 0, (strrpos($url, ".")));
        $imageHtmlWithoutSize = "<a href='$url'><img src='$url' alt='$alt' /></a>";

        $isRemoteFile = strpos($url, "://");
        if ($isRemoteFile)
            return $imageHtmlWithoutSize;

        $unknownLocalPath = is_null($markdownFolderPath);
        if ($unknownLocalPath)
            return $imageHtmlWithoutSize;

        $imagePath = $markdownFolderPath . DIRECTORY_SEPARATOR . $url;
        $pathIsValid = file_exists($imagePath);
        if (!$pathIsValid)
            return $imageHtmlWithoutSize;

        list($width, $height) = getimagesize($imagePath);
        return "<a href='$url'><img src='$url' alt='$alt' width='$width' height='$height' /></a>";
    }

    function addAnchorsToHeadingsAndUpdateTOC($html): string
    {
        $toc = "";
        $lines = explode("\n", $html);
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            if (trim($line) == '<!--TOC-->')
                $toc = "";

            $isHeaderLine = (substr($line, 0, 2) == "<h" && substr($line, 3, 1) == ">");
            if ($isHeaderLine == false)
                continue;

            $headerLevel = substr($line, 2, 1);
            $headerLabel = substr($line, 4, strlen($line) - 9);
            $url = $this->sanitizeLinkUrl($headerLabel);
            $lines[$i] = "<h$headerLevel id='$url'><a href='#$url'>$headerLabel</a></h$headerLevel>";
            if ($i > 0) {
                $lines[$i] = '<hr class="invisible" />' . $lines[$i];
            }
            $tocIndent = "&nbsp;&nbsp;&nbsp;&nbsp;";
            $tocIndent = str_repeat($tocIndent, $headerLevel - 1);
            $toc .= "<div>$tocIndent<a href='#$url'>$headerLabel</a></div>";
        }
        $html = join("\n", $lines);
        $html = str_replace("<!--TOC-->", $toc, $html);
        return $html;
    }

    function prettyPrintCodeBlocks(string $html): string
    {
        $html = str_replace("<pre><code class", "<pre class='prettyprint'><code class", $html);
        $html = str_replace("<pre><code>", "<pre class='prettyprint'><code class='nocode'>", $html);
        return $html;
    }
}
