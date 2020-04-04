<?
// this script responds to .md.html requests by serving the .md as HTML
error_reporting(-1);

// create a browser-friendly anchor URL
function sanitizeLinkUrl($url)
{
    $valid = "";
    foreach (str_split(strtolower(trim($url))) as $char)
        $valid .= (ctype_alnum($char)) ? $char : "-";
    while (strpos($valid, "--"))
        $valid = str_replace("--", "-", $valid);
    return trim($valid, '-');
}

// parse markdown lines, find headers, and generate a table of contents (TOC)
function getTOC($lines)
{
    $toc = "";
    foreach ($lines as $line) {
        if (trim($line) == "![](TOC)")
            $toc .= "<!-- TOC -->\n";
        if ($toc && substr($line, 0, 1) == "#") {
            for ($headingLevel = 1; $headingLevel <= 6; $headingLevel++) {
                $lineStart = str_repeat("#", $headingLevel) . " ";
                $lineIsHeader = substr($line, 0, strlen($lineStart)) === $lineStart;
                if ($lineIsHeader) {
                    $line = trim(substr($line, $headingLevel));
                    $url = sanitizeLinkUrl($line);
                    $toc .= "* [$line](#$url) \n";
                }
            }
        }
    }
    return $toc;
}

// https://raw.githubusercontent.com/erusev/parsedown/master/Parsedown.php
require "Parsedown.php";
$Parsedown = new Parsedown();

// determine important paths
$reqFile = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
$filePath = substr($reqFile, 0, -5);
if (!file_exists($filePath))
    return http_response_code(404);

// apply special editing to the markdown
$lines = explode("\n", file_get_contents($filePath));
for ($i = 0; $i < count($lines); $i++) {
    if (trim($lines[$i]) == "![](TOC)") {
        $lines[$i] = getTOC($lines);
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
        $url = sanitizeLinkUrl($headerLabel);
        $lines[$i] = str_replace("<h$headerLevel>", "<h$headerLevel id='$url'>", $line);
    }
}
$bodyHtml = implode("\n", $lines);

// syntax highlighting
$bodyHtml = str_replace("<pre><code>", "<pre class='prettyprinted'><code>", $bodyHtml);
$bodyHtml = str_replace("<pre>", "<pre class='prettyprint'>", $bodyHtml);

// serve the content
include("templates/top.php");
echo ($bodyHtml);
include("templates/bot.php");
