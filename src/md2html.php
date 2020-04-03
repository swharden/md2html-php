<?
/* md2html - A simple markdown-to-HTML converter for PHP by Scott Harden
   Project page: https://github.com/swharden/md2html-php
*/

function sanitizeLinkUrl($str)
{
    // TODO: improve sanitization - lowercase, strip special
    $str = strtolower(trim($str));
    $str = str_replace(" ", "-", $str);
    $str = str_replace("/", "-", $str);
    return $str;
}

function getImageHtml($line)
{
    // this function replaces ![](url) with a custom element
    $url = substr($line, 4, strlen($line) - 5);

    if ((substr($url, -4) === ".php")) {
        return include($url);
    }

    if ((substr($url, -3) === ".md")) {
        $html = md2html(file_get_contents($url));
        return $html;
    }

    if (strstr(strtolower($url), "youtube.com/") !== false) {
        $allows = "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";
        return "<iframe width='560' height='315' src='$url' frameborder='0' allow='$allows' allowfullscreen></iframe>";
    }

    return "<a href='$url'><img src='$url'></img></a>\n\n";
}

function replaceSpecialChars($line)
{
    $line = str_replace("<", "&lt;", $line);
    $line = str_replace(">", "&gt;", $line);
    return $line;
}

function formatEmphasis($line, $mdSymbol, $htmlElement)
{
    $line = " $line ";
    $line = str_replace(" $mdSymbol ", " %%%ignore[$mdSymbol]ignore%%% ", $line);
    $line = str_replace("\\$mdSymbol", "%%%ignore[$mdSymbol]ignore%%%", $line);
    $line = str_replace(">$mdSymbol", "><$htmlElement>", $line);
    $line = str_replace("$mdSymbol<", "</$htmlElement><", $line);
    $line = str_replace(" $mdSymbol", " <$htmlElement>", $line);
    $line = str_replace("$mdSymbol ", "</$htmlElement> ", $line);
    return trim($line);
}

function getFormattedHtml($line)
{
    $lineBefore = "";
    while ($line != $lineBefore) {
        $lineBefore = $line;
        $line = formatEmphasis($line, "_", "i");
        $line = formatEmphasis($line, "`", "code");
        $line = formatEmphasis($line, "***", "em");
        $line = formatEmphasis($line, "**", "b");
        $line = formatEmphasis($line, "*", "i");
        $line = formatEmphasis($line, "~~", "strike");
    }
    $line = str_replace("%%%ignore[", "", $line);
    $line = str_replace("]ignore%%%", "", $line);

    // format links
    while (strrpos($line, "](")) {
        $parts = explode("](", $line . " ");
        $title = substr($parts[0], strrpos($parts[0], "[") + 1);
        $nextSpaceIndex = strpos($parts[1], " ");
        $urlToNextSpace = substr($parts[1], 0, $nextSpaceIndex);
        $lastEndParenIndex = strrpos($urlToNextSpace, ")");
        $url = substr($parts[1], 0, $lastEndParenIndex);
        $urlEndsWithEndParen = (substr($url, -1, 1) == ")");
        $urlContainsOpenParen = (strpos($url, "(") !== false);
        if ($urlEndsWithEndParen && !$urlContainsOpenParen)
            $url = substr($url, 0, -1); // accommodate (wrapped [sites](url))
        $mdLink = "[$title]($url)";
        $line = str_replace($mdLink, "<a href='$url'>$title</a>", $line);
    }

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
    $bulletSymbols = array("&bull;", "&#9702;", "&#8259;");
    for ($j = 0; $j < 3; $j++)
        $bulletSymbols = array_merge($bulletSymbols, $bulletSymbols);

    $html = "";
    $prettyprintJsURL = "https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js";
    $html .= "\n<script src='$prettyprintJsURL'></script>\n";
    $markdownText = str_replace("\r", "", $markdownText);
    $lines = explode("\n", $markdownText);
    $toc = "";

    // iterate through each line of the markdown file
    for ($i = 0; $i < count($lines); $i++) {
        $line = trim($lines[$i]);

        if ($line == "" || substr($line, 0, 4) === "<!--")
            continue;

        if ($line == "![](TOC)") {
            $html .= "%%%TOC%%%";
            continue;
        }

        // headers
        $lineIsHeader = false;
        for ($headingLevel = 1; $headingLevel <= 6; $headingLevel++) {
            $lineStart = str_repeat("#", $headingLevel) . " ";
            if (substr($line, 0, strlen($lineStart)) === $lineStart) {
                $line = trim(substr($line, $headingLevel));
                $url = sanitizeLinkUrl($line);
                $toc .= "\n<li><a href='#$url'>$line</a></li>\n";
                $line = getFormattedHtml($line);
                $line = "<a class='anchorLink' href='#$url'>&para;</a><div class='headerText'>$line</div>";
                $html .= "<h$headingLevel id='$url'>$line</h$headingLevel>\n\n";
                $lineIsHeader = true;
                break;
            }
        }
        if ($lineIsHeader)
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
            $line = $lines[$i];
            for ($j = count($bulletSymbols); $j >= 0; $j -= 2)
                if (substr($line, 0, $j + 2) === str_repeat(" ", $j) . "* ")
                    $line = str_repeat("&nbsp;", $j) . $bulletSymbols[$j / 2] . " " . getFormattedHtml(substr($line, $j + 2));
            $html .= "<div>$line</div>";
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
            $language = ($language) ? "lang-$language" : "prettyprinted";
            $html .= "<pre class='prettyprint $language' id='prettyprint'>";
            $i += 1;
            while ($i < count($lines)) {
                if (substr($lines[$i], 0, 3) === "```")
                    break;
                $html .= "\n" . replaceSpecialChars($lines[$i]);
                $i += 1;
            }
            $html .= "</pre>";
            continue;
        }

        $html .= "<p>" . getFormattedHtml($line) . "</p>\n\n";
    }

    $html = str_replace("%%%TOC%%%", $toc, $html);

    return "\n<div class='md2html'>\n$html\n</div>\n";
}

function includeMarkdown($filePath)
{
    $rawMarkdown = file_get_contents($filePath);
    $html = md2html($rawMarkdown);
    echo $html;
}
