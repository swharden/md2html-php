<?php

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

class SingleArticlePage
{
    /* This class returns a ready-to-echo webpage given a Markdown file as input.
     * New functionality can be achieved by adding extra steps to this class that modify the Markdown or HTML.
     * 
     * This is a single class, but if different page types are desired in the future (e.g., multi-article or site map)
     * it is a good idea to rename this to "Page" and make it abstract, then let other page types inherit from it.
     */

    private string $templateHtml;
    private string $articleHtml;
    private string $articleSourceHtml;
    private float $timeStart;
    private string $baseUrl;
    private array $replacements;

    function __construct(string $markdownFilePath)
    {
        error_reporting(E_ALL);
        if (!file_exists($markdownFilePath))
            throw new Exception("Markdown file does not exist: " . $markdownFilePath);

        $this->timeStart = microtime(true);
        $this->replacements = include('settings.php');
        $http = isset($_SERVER['HTTPS']) ? "https://" : "http://";
        $this->baseUrl = $http . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__DIR__)) . "/";
        $this->templateHtml = file_get_contents('template.html');
        $mdSource = file_get_contents($markdownFilePath);
        $this->articleSourceHtml = str_replace("\n", "<br>", htmlspecialchars($mdSource));
        $mdSource = str_replace("\\\n", "<br>\n", $mdSource);
        $mdLines = $this->processFrontMatter($mdSource);
        $mdLines = $this->updateSpecialCodes($mdLines);

        require('Parsedown.php');
        $Parsedown = new Parsedown();
        $html = $Parsedown->text(implode("\n", $mdLines));
        $html = $this->addAnchorsToHeadingsAndUpdateTOC($html);
        $html = $this->prettyPrintCodeBlocks($html);
        $this->articleHtml = $html;
    }

    public function getHtml(): string
    {
        $html = $this->templateHtml;

        foreach ($this->replacements as $key => $value)
            $html = str_replace($key, $value, $html);

        $html = str_replace('{{baseUrl}}', $this->baseUrl, $html);
        $html = str_replace('{{date}}', gmdate("F jS, Y", date("Z") + time()), $html);
        $html = str_replace('{{time}}', gmdate("H:i:s", time() + time()), $html);
        $html = str_replace('{{articleSource}}', $this->articleSourceHtml, $html);
        $html = str_replace('{{article}}', $this->articleHtml, $html);
        $html = str_replace('{{elapsedMsec}}', round((microtime(true) - $this->timeStart) * 1000, 3), $html);
        return $html;
    }

    /*----------------------- TODO: move these modifiers to another class --------------------------*/

    private function processFrontMatter(string $mdRaw): array
    {
        $lines = explode("\n", $mdRaw);

        if (trim($lines[0]) != "---")
            return $lines;

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line == "---")
                break;

            $parts = explode(":", $line, 2);
            if (count($parts) == 2) {
                $key = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $this->replacements["{{" . $key . "}}"] = $value;
            }
        }

        return array_slice($lines, $i + 1);
    }

    private function sanitizeLinkUrl($url): string
    {
        $valid = "";
        foreach (str_split(strtolower(trim($url))) as $char)
            $valid .= (ctype_alnum($char)) ? $char : "-";
        while (strpos($valid, "--"))
            $valid = str_replace("--", "-", $valid);
        return trim($valid, '-');
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

    private function prettyPrintCodeBlocks(string $html): string
    {
        $html = str_replace("<pre><code class", "<pre class='prettyprint'><code class", $html);
        $html = str_replace("<pre><code>", "<pre class='prettyprint'><code class='nocode'>", $html);
        return $html;
    }
}
