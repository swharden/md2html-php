<?
// this script responds to .md.html requests by serving the .md as HTML
error_reporting(-1);
$benchmarkStartTime = microtime(true);
require "md2html/md2html.php";
$md2html = new md2html();

// this file should only get requests ending in .md.html
$reqFile = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
//echo "<div style='background-color: yellow;'>$reqFile</div>";
if (substr($reqFile, -1) == "/")
    $reqFile .= "index.md.html";
if (substr($reqFile, -8) != ".md.html")
    return http_response_code(500);

// ensure the file exists
$filePath = substr($reqFile, 0, -5);
if (!file_exists($filePath))
    return http_response_code(404);

// serve it
include("templates/top.php");
echo $md2html->fromFile($filePath);
include("templates/bot.php");
