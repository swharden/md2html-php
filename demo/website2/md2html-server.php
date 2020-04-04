<?
// this script responds to .md.html requests by serving the .md as HTML
error_reporting(-1);
require "md2html.php";

// this file should only get requests ending in .md.html
$reqFile = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
if (substr($reqFile, -8) != ".md.html")
    return http_response_code(500);

// ensure the file exists
$filePath = substr($reqFile, 0, -5);
if (!file_exists($filePath))
    return http_response_code(404);

// serve it
md2html($filePath);
