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
    $imageUrl = substr($line, 4, strlen($line) - 5);
    $imageUrl = str_replace("../graphics/", "graphics/", $imageUrl);
    return "<a href='$imageUrl'><img src='$imageUrl'></img></a>\n\n";
}

function getBulletHtml($line, $bulletLevel = 1)
{
    if (substr($line, 0, 6) === "    * ")
        $line = "&nbsp;&nbsp;&nbsp;&nbsp;" . "&#9642;" . substr($line, 6);
    if (substr($line, 0, 4) === "  * ")
        $line = "&nbsp;&nbsp;" . "&#9702;" . substr($line, 4);
    if (substr($line, 0, 2) === "* ")
        $line = "" . "&bull;" . substr($line, 2);
    return "<div>$line</div>";

    //$line = str_replace("  * ", "&bull");
    //$line = str_replace("  * ", "&bull");
    //$line = trim(substr($line, $bulletLevel));
    //$line = getFormattedHtml($line);
    //return "<li>$line</li>\n\n";
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

function getFormattedHtml($line)
{
    // add strategic spaces so nested format elements don't get confused
    $line = " " . $line . " ";
    $line = str_replace(" * ", "&nbsp;*&nbsp;", $line);

    // TODO: backslash escapes for characters listed on:
    // https://guides.github.com/pdfs/markdown-cheatsheet-online.pdf

    $line = str_replace(" **", " <b> ", $line);
    $line = str_replace("** ", " </b> ", $line);

    $line = str_replace(" *", " <i>", $line);
    $line = str_replace("* ", " </i> ", $line);

    $line = str_replace(" _", " <i> ", $line);
    $line = str_replace("_ ", " </i> ", $line);

    $line = str_replace(" ~~", " <strikeout> ", $line);
    $line = str_replace("~~ ", " </strikeout> ", $line);

    $line = str_replace(" `", " <code style='background-color: magenta;'> ", $line);
    $line = str_replace("` ", " </code> ", $line);

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
                $html .= "<h$headingLevel id='$url'><a href='#$url'>$line</a></h$headingLevel>\n\n";
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
        // TODO: prettyprint
        if (substr($line, 0, 3) === "```") {
            $html .= "<pre style='background-color: lightblue;'>";
            $html .= str_replace("```", "", $line);
            while ($i < count($lines)) {
                $i += 1;
                $html .= $lines[$i];
                if (substr($line, 0, 3) === "```")
                    break;
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
