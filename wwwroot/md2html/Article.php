<?php

require('Parsedown.php');
require('misc.php');

/** This class represents an article that came from a markdown file (with an optional header containing frontmatter) */
class Article
{
    public string $markdown;
    public string $sourceHtml;
    public string $html;
    public int $modified;

    // these details come from the front matter
    public string $title = "";
    public string $description = "";
    public string $postDate = "";
    public array $tags = array();

    function __construct(string $markdownFilePath)
    {
        if (!file_exists($markdownFilePath))
            throw new Exception("Markdown file does not exist: " . $markdownFilePath);

        $this->modified = filemtime($markdownFilePath);
        $this->markdown = file_get_contents($markdownFilePath);
        $this->sourceHtml = htmlspecialchars($this->markdown);

        // custom modifications to the Markdown
        $mdLines = $this->processFrontMatter($this->markdown);
        $mdLines = $this->updateSpecialCodes($mdLines);

        // convert array of markdown lines to HTML
        $Parsedown = new Parsedown();
        $this->html = $Parsedown->text(implode("\n", $mdLines));

        // custom modifications to the HTML
        $this->html = $this->addAnchorsToHeadingsAndUpdateTOC($this->html);
        $this->html = $this->prettyPrintCodeBlocks($this->html);
    }

    private function processFrontMatter(string $mdRaw): array
    {
        $lines = explode("\n", $mdRaw);

        if (trim($lines[0]) != "---")
            return $lines;

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line == "---")
                break;

            // populate tags
            $trimmedLine = trim($line);
            if (startsWith($trimmedLine, "- ")) {
                $this->tags[] .= str_replace("- ", "", $trimmedLine);
                continue;
            }

            // populate key/value pairs
            $parts = explode(":", $line, 2);
            if (count($parts) == 2) {
                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1]);

                switch ($key) {
                    case "title":
                        $this->title = $value;
                        break;
                    case "description":
                        $this->description = $value;
                        break;
                    case "date":
                        $dateParts = date_parse($value);
                        $postDate = mktime(
                            $dateParts['hour'],
                            $dateParts['minute'],
                            $dateParts['second'],
                            $dateParts['month'],
                            $dateParts['day'],
                            $dateParts['year']
                        );
                        $this->postDate = date("F jS, Y", $postDate);
                        break;
                }
            }
        }

        return array_slice($lines, $i + 1);
    }

    private function updateSpecialCodes(array $mdLines): array
    {
        for ($i = 0; $i < count($mdLines); $i++) {
            $trimmedLine = trim($mdLines[$i]);
            $isSpecialLink = startsWith($trimmedLine, "![](") && endsWith($trimmedLine, ")");
            if ($isSpecialLink) {
                $url = substr($trimmedLine, 4, strlen($trimmedLine) - 5);
                $mdLines[$i] = $this->getSpecialCode($url);
            }
        }
        return $mdLines;
    }

    private function getSpecialCode($url): string
    {
        // make YouTube links embedded videos
        if (strstr($url, "youtube.com/") || strstr($url, "youtu.be/")) {
            $url = "https://www.youtube.com/embed/" . basename($url);
            return "<div class='ratio ratio-16x9'><iframe src='$url' frameborder='0' allowfullscreen " .
                "allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'></iframe></div>";
        }

        // make images link to themselves
        if (
            endsWith($url, ".png") || endsWith($url, ".jpg") || endsWith($url, ".jpeg") ||
            endsWith($url, ".bmp") || endsWith($url, ".gif")
        ) {
            return "<a href='$url'><img src='$url' class='markdownImage' /></a>";
        }

        // If this is a table of contents, mark it with HTML so we can come back to it later
        if ($url == "TOC") {
            return "<!--TOC-->";
        }

        // we didn't do anything special, so return the URL so it will be a clickable link
        return $url;
    }

    private function addAnchorsToHeadingsAndUpdateTOC($html): string
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
            $url = sanitizeLinkUrl($headerLabel);
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

    private function prettyPrintCodeBlocks(string $html): string
    {
        $html = str_replace("<pre><code class", "<pre class='prettyprint'><code class", $html);
        $html = str_replace("<pre><code>", "<pre class='prettyprint'><code class='nocode'>", $html);
        return $html;
    }
}
