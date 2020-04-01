<?php

/* 
    This script will serve PHP or MD files in the ./views/ folder.

    STEP 1/2: Add this to ./.htaccess:

        Options +Indexes
        IndexOptions +FancyIndexing
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.+)$ index.php [QSA,L]

        <Files ~ "views">  
        Order Allow,Deny
        Deny from All
        </Files>

    STEP 2/2: Ensure these files are present:

        ./index.php (this file)
        ./scripts/top.php
        ./scripts/bot.php
        ./scripts/md2html.php
        ./scripts/md2html.css
        ./views/index.md
        ./views/yourPage.md

*/

require 'scripts/md2html.php';

function getTitlesFromFirstLine($filePath)
{
    // header title and page title can be defined in a HTML comment in the first 
    //line of your markdown file. Format it like: <-- header title, page title -->

    $firstLine = trim(fgets(fopen($filePath, 'r')));
    if (!substr($firstLine, 0, 4) === "<!--")
        return;
    $firstLine = str_replace("<!--", "", $firstLine);
    $firstLine = str_replace("-->", "", $firstLine);
    $parts = explode(",", $firstLine);
    if (count($parts) != 2)
        return;
    return array(trim($parts[0]), trim($parts[1]));
}

function endsWith($haystack, $needle)
{
    return (substr($haystack, -strlen($needle)) === $needle);
}

function serve($fileName, $headerTitle = '', $pageTitle = '', $title = 'LJPcalc ⚡')
{
    // append .txt to a .md URL to display the raw markdown file
    if (endsWith($fileName, ".md.txt")) {
        $fileName = substr($fileName, 0, strlen($fileName) - 4);
        $filePath = __DIR__ . "/views/$fileName";
        if (file_exists($filePath)) {
            echo "<pre>" . file_get_contents($filePath) . "</pre>";
        } else {
            http_response_code(404);
        }
        return;
    }

    $fileName = ($fileName) ? $fileName : "index.md";
    $filePath = __DIR__ . "/views/$fileName";
    if (!file_exists($filePath)) {
        http_response_code(404);
        return;
    }

    list($headerTitle, $pageTitle) = getTitlesFromFirstLine($filePath);
    $headerTitle = ($headerTitle) ? "$title - $headerTitle" : $title;
    $pageTitle = ($pageTitle) ? "$title $pageTitle" : $title;

    require __DIR__ . '/scripts/top.php';
    if (endsWith($filePath, ".php"))
        require $filePath;
    else if (endsWith($filePath, ".md")) {
        $html = md2html(file_get_contents($filePath));
        $html = str_replace("../graphics/", "graphics/", $html); // custom markdown replacements
        echo $html;
    }
    require __DIR__ . '/scripts/bot.php';
}

$requestedFileName = str_replace(dirname($_SERVER['PHP_SELF']) . "/", '', $_SERVER['REQUEST_URI']);
serve($requestedFileName);
