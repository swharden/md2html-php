<?php
error_reporting(-1);

class md2html
{
    public $version = '1.1.0';
    public $conversionTimeMsec = 0;
    public $title = "";
    public $html = "";
    public $markdown = "";
    public $benchmarkMsec = 0;

    function __construct($filePath)
    {
        $benchmarkStartTime = microtime(true);
        $this->markdown = file_get_contents($filePath);
        $this->html = $this->convert($this->markdown);
        $this->benchmarkMsec = (microtime(true) - $benchmarkStartTime) * 1000;
    }

    // create a browser-friendly anchor URL
    private function sanitizeLinkUrl($url)
    {
        $valid = "";
        foreach (str_split(strtolower(trim($url))) as $char)
            $valid .= (ctype_alnum($char)) ? $char : "-";
        while (strpos($valid, "--"))
            $valid = str_replace("--", "-", $valid);
        return trim($valid, '-');
    }

    // return a markdown table of contents (TOC) made from headings
    private function getTOC($markdownLines)
    {
        $toc = "";
        foreach ($markdownLines as $line) {
            if (trim($line) == "![](TOC)")
                $toc .= "<!-- TOC -->\n";
            if ($toc && substr($line, 0, 1) == "#") {
                for ($headingLevel = 1; $headingLevel <= 6; $headingLevel++) {
                    $lineStart = str_repeat("#", $headingLevel) . " ";
                    $lineIsHeader = substr($line, 0, strlen($lineStart)) === $lineStart;
                    if ($lineIsHeader) {
                        $line = trim(substr($line, $headingLevel));
                        $url = $this->sanitizeLinkUrl($line);
                        $toc .= "* [$line](#$url) \n";
                    }
                }
            }
        }
        return $toc;
    }

    private function debug($message)
    {
        echo "<div style='background-color: yellow;'><code>$message</code></div>";
    }

    private function convert($markdown)
    {
        // apply special editing to the markdown
        $lines = explode("\n", $markdown);
        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ((substr($line, 0, 4) != "![](") || (substr($line, -1, 1) != ")"))
                continue;
            $url = substr($line, 4, strlen($line) - 5);

            // table of contents
            if ($url == "TOC")
                $lines[$i] = $this->getTOC($lines);

            // dynamic inclusion of PHP file
            if (substr($url, -4, 4) == ".php") {
                $phpPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $url;
                if (file_exists($phpPath))
                    $lines[$i] = include($phpPath);
                else
                    $lines[$i] = "> ⚠️ **md2html error:** PHP script not found `$phpPath`";
            }
        }

        // convert the markdown to HTML using Parsedown
        require "Parsedown.php";
        $Parsedown = new Parsedown();
        $bodyHtml = $Parsedown->text(implode("\n", $lines));

        // apply special formatting to the HTML
        $lines = explode("\n", $bodyHtml);
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            // add anchors to headers
            $isHeaderLine = (substr($line, 0, 2) == "<h" && substr($line, 3, 1) == ">");
            if ($isHeaderLine) {
                $headerLevel = substr($line, 2, 1);
                $headerLabel = substr($line, 4, strlen($line) - 9);
                $url = $this->sanitizeLinkUrl($headerLabel);
                $anchor = "<a class='anchorLink' href='#$url'>&para;</a>";
                $text = "<span class='anchorText'>$headerLabel</span>";
                $lines[$i] = "<h$headerLevel id='$url'>$anchor$text</h$headerLevel>";
                if ($this->title == "")
                    $this->title = $headerLabel;
            }
        }
        $bodyHtml = implode("\n", $lines);

        // syntax highlighting
        $bodyHtml = str_replace("<pre><code class", "<pre class='prettyprint'><code class", $bodyHtml);
        $bodyHtml = str_replace("<pre><code>", "<pre class='prettyprint'><code class='nocode'>", $bodyHtml);

        // serve the content in a special div
        $bodyHtml = "\n<div id='md2html'>\n$bodyHtml\n</div>\n";

        return $bodyHtml;
    }
}
