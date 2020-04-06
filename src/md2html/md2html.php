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
                $lines[$i] = "<!-- md2html-TOC -->";

            // dynamic inclusion of PHP file
            if (substr($url, -4, 4) == ".php") {
                $includePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $url;
                if (file_exists($includePath))
                    $lines[$i] = include($includePath);
                else
                    $lines[$i] = "> ⚠️ **md2html error:** PHP script not found `$includePath`";
            }

            // dynamic inclusion of a Markdown file
            if (substr($url, -3, 3) == ".md") {
                $includePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $url;
                if (file_exists($includePath))
                    $lines[$i] = file_get_contents($includePath);
                else
                    $lines[$i] = "> ⚠️ **md2html error:** MD file not found `$includePath`";
            }
        }

        // if first line is a HTML comment make it the title
        if (substr($lines[0], 0, 4) == "<!--")
            $this->title = trim(substr($lines[0], 4, -4));

        // convert the markdown to HTML using Parsedown
        require "Parsedown.php";
        $Parsedown = new Parsedown();
        $bodyHtml = $Parsedown->text(implode("\n", $lines));

        // apply special formatting to the HTML
        $lines = explode("\n", $bodyHtml);
        $toc = "";
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            if ($line == "<!-- md2html-TOC -->")
                $toc = $line;

            // add anchors to headers
            $isHeaderLine = (substr($line, 0, 2) == "<h" && substr($line, 3, 1) == ">");
            if ($isHeaderLine) {
                $headerLevel = substr($line, 2, 1);
                $headerLabel = substr($line, 4, strlen($line) - 9);
                $url = $this->sanitizeLinkUrl($headerLabel);
                $anchor = "<a class='anchorLink' href='#$url'>&para;</a>";
                $text = "<span class='anchorText'>$headerLabel</span>";
                $lines[$i] = "<h$headerLevel id='$url'>$anchor$text</h$headerLevel>";
                if ($toc) {
                    $shift = (($headerLevel) * .5) . "em";
                    if (($headerLevel == '1') || ($headerLevel == '2')) {
                        $toc .= "<div style='position: relative; left: $shift; margin-top: 1em;'><strong><a href='#$url'>$headerLabel</a></strong></div>";
                    } else {
                        $toc .= "<div style='position: relative; left: $shift;'><a href='#$url'>$headerLabel</a></div>\n";
                    }
                }
                if ($this->title == "")
                    $this->title = $headerLabel;
            }
        }

        // special search/replace after HTML has been assembled
        $bodyHtml = implode("\n", $lines);
        $bodyHtml = str_replace("<!-- md2html-TOC -->", $toc, $bodyHtml);
        $bodyHtml = str_replace("<pre><code class", "<pre class='prettyprint'><code class", $bodyHtml);
        $bodyHtml = str_replace("<pre><code>", "<pre class='prettyprint'><code class='nocode'>", $bodyHtml);

        // special emoji
        $bodyHtml = str_replace(":warning:", json_decode('"\u26a0\ufe0f"'), $bodyHtml);
        $bodyHtml = str_replace(":floppy_disk:", json_decode('"\ud83d\udcbe"'), $bodyHtml);

        // serve the content in a special div
        $bodyHtml = "\n<div id='md2html'>\n$bodyHtml\n</div>\n";

        return $bodyHtml;
    }
}
