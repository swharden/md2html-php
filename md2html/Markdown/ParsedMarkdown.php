<?php

require_once(__DIR__ . '/Parsedown.php');

/* This class is a Parsedown wrapper with extra custom functions:
 *   - frontmatter (header) defines title, description, tags, and date
 *   - images automatically link to themselves
 *   - headings are automatically anchored
 *   - ![](TOC) inserts table of contents for all headings
 *   - ![](youTubeUrl) embeds YouTube video
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

    public function __construct($markdownText)
    {
        // custom modifications to the markdown
        $mdLines = $this->processHeaderAndReturnBodyLines($markdownText);
        $mdLines = $this->updateSpecialCodes($mdLines);

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

    function updateSpecialCodes(array $mdLines): array
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
                $mdLines[$i] = $this->getSpecialCode($url);
            }
        }
        return $mdLines;
    }

    function getSpecialCode($url): string
    {
        // make YouTube links embedded videos
        if (strstr($url, "youtube.com/") || strstr($url, "youtu.be/")) {
            $url = "https://www.youtube.com/embed/" . basename($url);
            $html = "<div class='ratio ratio-16x9'><iframe src='$url' class='border shadow' frameborder='0' allowfullscreen " .
                "allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'></iframe></div>";
            return "<div class='container my-5'>$html</div>";
        }

        // make images link to themselves
        if (
            $this->endsWith($url, ".png") || $this->endsWith($url, ".jpg") || $this->endsWith($url, ".jpeg") ||
            $this->endsWith($url, ".bmp") || $this->endsWith($url, ".gif")
        ) {
            // this area customizes spacing around the image
            return "<a href='$url'><img src='$url' /></a>";
        }

        // If this is a table of contents, mark it with HTML so we can come back to it later
        if ($url == "TOC") {
            return "<!--TOC-->";
        }

        // we didn't do anything special, so return the URL so it will be a clickable link
        return $url;
    }

    function addAnchorsToHeadingsAndUpdateTOC($html): string
    {
        $toc = "";
        $lines = explode("\n", $html);
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

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
