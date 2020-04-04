<?
// this script responds to .md.html requests by serving the .md as HTML
error_reporting(-1);
require "md2html/md2html.php";

// this file should only get requests ending in .md.html
$reqFile = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
if (substr($reqFile, -8) != ".md.html")
    return http_response_code(500);

// ensure the file exists
$filePath = substr($reqFile, 0, -5);
if (!file_exists($filePath))
    return http_response_code(404);

// serve it
include("templates/top.php");

/*
$relUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
echo "<div style='background-color: yellow;'><code>" . __DIR__  . "</code></div>";
echo "<div style='background-color: yellow;'><code>" . $_SERVER['DOCUMENT_ROOT']  . "</code></div>";
echo "<div style='background-color: yellow;'><code>" . $relPath  . "</code></div>";
*/

md2html($filePath);
include("templates/bot.php");
