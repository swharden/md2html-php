<?php
error_reporting(-1);

class md2html
{
    public $version = '1.1.0';
    public $conversionTimeMsec = 0;

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

    public function fromFile($filePath)
    {
        $markdown = file_get_contents($filePath);
        return $this->fromMarkdown($markdown);
    }

    public function fromMarkdown($markdown)
    {
        require "Parsedown.php";
        $Parsedown = new Parsedown();

        // apply special editing to the markdown
        $lines = explode("\n", $markdown);
        for ($i = 0; $i < count($lines); $i++) {
            if (trim($lines[$i]) == "![](TOC)") {
                $lines[$i] = $this->getTOC($lines);
            }
        }

        // convert the markdown to HTML
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
                $lines[$i] = str_replace("<h$headerLevel>", "<h$headerLevel id='$url'>", $line);
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
