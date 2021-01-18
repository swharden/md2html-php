<?php

require_once('Parsedown.php');
require_once('misc.php');
require_once('ArticleInfo.php');

/** This class represents a markdown file article */
class Article
{
    public string $markdown;
    public string $sourceHtml;
    public string $html;
    public ArticleInfo $info;

    /** Create an article from a markdown file. 
     * If a base URL is given, relative URLs will be prefixed by it. */
    function __construct(string $markdownFilePath)
    {
        if (!file_exists($markdownFilePath))
            throw new Exception("Markdown file does not exist: " . $markdownFilePath);

        $this->info = new ArticleInfo($markdownFilePath);
        $this->markdown = file_get_contents($markdownFilePath);
        $this->sourceHtml = htmlspecialchars($this->markdown);

        // custom modifications to the Markdown
        $mdBody = substr($this->markdown, $this->info->contentOffset);
        $mdLines = explode("\n", $mdBody);
        $mdLines = $this->updateSpecialCodes($mdLines);

        // convert array of markdown lines to HTML
        $Parsedown = new Parsedown();
        $this->html = $Parsedown->text(implode("\n", $mdLines));

        // custom modifications to the HTML
        $this->html = $this->addAnchorsToHeadingsAndUpdateTOC($this->html);
        $this->html = $this->prettyPrintCodeBlocks($this->html);
    }

    private function updateSpecialCodes(array $mdLines): array
    {
        $isInCodeBlock = false;
        for ($i = 0; $i < count($mdLines); $i++) {
            $trimmedLine = trim($mdLines[$i]);
            if (startsWith($trimmedLine, "```")) {
                $isInCodeBlock = !$isInCodeBlock;
            }
            if ($isInCodeBlock)
                continue;
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
            $html = "<div class='ratio ratio-16x9'><iframe src='$url' class='border shadow' frameborder='0' allowfullscreen " .
                "allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'></iframe></div>";
            return "<div class='container my-5'>$html</div>";
        }

        // make images link to themselves
        if (
            endsWith($url, ".png") || endsWith($url, ".jpg") || endsWith($url, ".jpeg") ||
            endsWith($url, ".bmp") || endsWith($url, ".gif")
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
