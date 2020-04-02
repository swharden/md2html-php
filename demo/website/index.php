<?php

/* 
    This script will serve PHP or MD files in the ./pages/ folder.

        yourSite/fileName.php => ./pages/fileName.php
        yourSite/fileName.md => ./pages/fileName.md (served as source code)
        yourSite/fileName.md.html => ./pages/fileName.md (converted to HTML)

    STEP 1/3: Add this to ./.htaccess:

        Options +Indexes
        IndexOptions +FancyIndexing
        
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.+)$ index.php [QSA,L]

    STEP 2/3: Ensure these files are present:

        ./index.php (this file)
        ./.htaccess
        ./scripts/top.php
        ./scripts/bot.php
        ./scripts/md2html.php
        ./scripts/md2html.css
        ./scripts/md2html.SiteConfig.php
        ./pages/index.md
        ./pages/customPage.md
        
    STEP 3/3: Edit the config file to customize your website:
    
        ./scripts/md2html.SiteConfig.php

*/

error_reporting(-1);
$startTime = microtime(true);
require 'scripts/md2html.php';
$configs = include('templates/md2html.siteConfig.php');
$fileName = str_replace(dirname($_SERVER['PHP_SELF']) . "/", '', $_SERVER['REQUEST_URI']);
$fileName = ($fileName) ? $fileName : $configs['markdownIndex'] . ".html";
$filePath = __DIR__ . "/pages/$fileName";

$isValidPhp = ((substr($filePath, -4) === ".php") && (file_exists($filePath)));
$isValidMdSource = ((substr($filePath, -3) === ".md") && (file_exists($filePath)));
$isValidMdHtml = ((substr($filePath, -8) === ".md.html") && (file_exists(substr($filePath, 0, -5))));
$filePath = ($isValidMdHtml) ? substr($filePath, 0, -5) : $filePath;

$headerTitle = $configs['defaultHeaderTitle'];
$pageTitle =  $configs['defaultPageTitle'];
if ($isValidMdHtml || $isValidPhp) {
    // if the first line is a comment, it can define the header and pages titles
    $firstLine = trim(fgets(fopen($filePath, 'r')));
    if (substr($firstLine, 0, 4) === "<!--") {
        $firstLine = str_replace("<!--", "", $firstLine);
        $firstLine = str_replace("-->", "", $firstLine);
        $parts = explode(",", $firstLine);
        if (count($parts) == 2) {
            $headerTitle .= " - " . trim($parts[0]);
            $pageTitle .= " " . trim($parts[1]);
        }
    }
}

if ($isValidMdSource) {
    header('Content-type: text/plain');
    echo file_get_contents($filePath);
} else if ($isValidPhp) {
    require __DIR__ . '/templates/top.php';
    require $filePath;
    require __DIR__ . '/templates/bot.php';
} else if ($isValidMdHtml) {
    require __DIR__ . '/templates/top.php';
    $markdown = file_get_contents($filePath);
    foreach ($configs['replaceInMarkdown'] as $search => $replace)
        $markdown = str_replace($search, $replace, $markdown);
    $html = md2html($markdown);
    foreach ($configs['replaceInHtml'] as $search => $replace)
        $html = str_replace($search, $replace, $html);
    echo $html;
    require __DIR__ . '/templates/bot.php';
} else {
    http_response_code(404);
}
