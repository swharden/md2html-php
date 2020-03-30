<?
/* md2html - a minimal markdown to HML converter by Scott W Harden */

function sanitizeLinkUrl($str)
{
    // TODO: improve sanitization - lowercase, strip special
    $str = trim($str);
    $str = str_replace(" ", "-", $str);
    return $str;
}

function getImageHtml($line)
{
    $url = substr($line, 4, strlen($line) - 5);

    if (strstr($url, "youtube.com/") !== false) {
        $allows = "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";
        return "<iframe width='560' height='315' src='$url' frameborder='0' allow='$allows' allowfullscreen></iframe>";
    }

    return "<a href='$url'><img src='$url'></img></a>\n\n";
}

function getBulletHtml($line, $bulletLevel = 1)
{
    if (substr($line, 0, 6) === "    * ")
        $line = "&nbsp;&nbsp;&nbsp;&nbsp;" . "&#9642; " . substr($line, 6);
    if (substr($line, 0, 4) === "  * ")
        $line = "&nbsp;&nbsp;" . "&#9702; " . substr($line, 4);
    if (substr($line, 0, 2) === "* ")
        $line = "" . "&bull; " . substr($line, 2);
    $line = getFormattedHtml($line);
    return "<div>$line</div>";
}

function getParagraphHtml($line)
{
    $line = getFormattedHtml($line);
    return "<p>$line</p>\n\n";
}

function formatNextLink($line)
{
    // non-destructively attempt to convert the first URL
    $parts = explode("](", $line);
    $title = substr($parts[0], strrpos($parts[0], "[") + 1);
    $url = substr($parts[1], 0, strpos($parts[1], ")"));
    $mdLink = "[$title]($url)";
    return str_replace($mdLink, "<a href='$url'>$title</a>", $line);
}

function formatEmphasis($line, $mdSymbol, $htmlElement)
{
    // TODO: backslash escapes for characters listed on:
    // https://guides.github.com/pdfs/markdown-cheatsheet-online.pdf

    $line = " " . $line;
    while (true) {
        $parts = explode(" " . $mdSymbol, $line, 2);
        if (count($parts) == 1)
            return trim($line);
        $line = $parts[0] . " <$htmlElement>" . str_replace($mdSymbol, "</$htmlElement>", $parts[1]);
    }
}

function getFormattedHtml($line)
{
    $line = formatEmphasis($line, "_", "i");
    $line = formatEmphasis($line, "`", "code");
    $line = formatEmphasis($line, "***", "em");
    $line = formatEmphasis($line, "**", "b");
    $line = formatEmphasis($line, "*", "i");
    $line = formatEmphasis($line, "~~", "strike");

    while (strrpos($line, "]("))
        $line = formatNextLink($line);

    // TODO: replace links somehow
    return trim($line);
}

function isTableLine($line)
{
    if (strpos($line, "---|---") === false)
        return false;
    // TODO: assert line only contains - and | characters
    return true;
}

function md2html($markdownText)
{
    $html = "\n<script src='https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js'></script>\n";
    $markdownText = str_replace("\r", "", $markdownText);
    $lines = explode("\n", $markdownText);
    for ($i = 0; $i < count($lines); $i++) {
        $line = trim($lines[$i]);

        if ($line == "" || substr($line, 0, 4) === "<!--")
            continue;

        // headers
        $headerLine = false;
        for ($headingLevel = 1; $headingLevel <= 6; $headingLevel++) {
            $lineStart = str_repeat("#", $headingLevel) . " ";
            if (substr($line, 0, strlen($lineStart)) === $lineStart) {
                $line = trim(substr($line, $headingLevel));
                $url = sanitizeLinkUrl($line);
                $line = getFormattedHtml($line);
                $html .= "<h$headingLevel id='$url'><a  style='color: inherit;' href='#$url'>$line</a></h$headingLevel>\n\n";
                $headerLine = true;
                break;
            }
        }
        if ($headerLine)
            continue;

        // horizontal break
        // TODO: any number of - with no |
        if (substr($line, 0, 3) === "---") {
            $html .= "<hr>\n\n";
            continue;
        }

        // image
        if (substr($line, 0, 4) === "![](") {
            $html .= getImageHtml($line);
            continue;
        }

        // bullet
        if (substr($line, 0, 2) === "* ") {
            $html .= getBulletHtml($lines[$i]);
            continue;
        }

        // table
        if (($i < count($lines) - 2) && isTableLine($lines[$i + 1])) {
            $colCount = count(explode("|", $lines[$i + 1]));
            $headers = explode("|", $lines[$i]);
            $html .= "<table border='1'>";
            $html .= "<tr>";
            foreach ($headers as $header)
                $html .= "<th>$header</th>";
            $html .= "</tr>";
            $rowCount = 0;
            for ($j = $i + 2; $j < count($lines); $j++) {
                $cells = explode("|", $lines[$j]);
                if (count($cells) != $colCount)
                    break;
                $html .= "<tr>";
                foreach ($cells as $cell)
                    $html .= "<td>$cell</td>";
                $html .= "</tr>";
                $rowCount += 1;
            }
            $i += $rowCount + 1;
            $html .= "</table>";
            continue;
        }

        // blockquote
        if (substr($line, 0, 2) === "> ") {
            $html .= "<blockquote>";
            while ($i < count($lines)) {
                $line = $lines[$i];
                if (substr($line, 0, 1) === ">") {
                    $line = trim($line, "> ");
                    if ($line == "")
                        $line = "<br><br>";
                    $html .= getFormattedHtml($line);
                    $i += 1;
                } else {
                    break;
                }
            }
            $html .= "</blockquote>";
            continue;
        }

        // code block
        if (substr($line, 0, 3) === "```") {
            $language = trim(str_replace("```", "", $line));
            $html .= "<pre class='prettyprint $language' id='prettyprint'>";
            $i += 1;
            while ($i < count($lines)) {
                if (substr($lines[$i], 0, 3) === "```")
                    break;
                $html .= "\n" . $lines[$i];
                $i += 1;
            }
            $html .= "</pre>";
            continue;
        }

        // if all special cases fail, render it as a paragraph
        $html .= getParagraphHtml($line);
    }

    return $html;
}

function includeMarkdown($filePath)
{
    $rawMarkdown = file_get_contents($filePath);
    $html = md2html($rawMarkdown);
    echo $html;
}
