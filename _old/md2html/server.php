<?
// this script responds to .md.html requests by serving the .md as HTML
error_reporting(-1);

// generate a sitemap if we are asked for one appropriate
if (basename($_SERVER["REQUEST_URI"]) == "sitemap.xml"){
    include("sitemap.php");
    die;
}

// this file should only get requests ending in .md.html
$reqFile = $_SERVER['DOCUMENT_ROOT'] . strtok($_SERVER["REQUEST_URI"], '?');
$reqPath = strtok($_SERVER["REQUEST_URI"], '?');

// ensure the request ends in .md.html
if (substr($reqFile, -1) == "/")
    $reqFile .= "readme.md.html";
if (substr($reqFile, -8) != ".md.html")
    $reqFile .= ".md.html";

// ensure the underlying markdown file exists
$filePath = substr($reqFile, 0, -5);
if (!file_exists($filePath))
    return http_response_code(404);

// serve the raw file or the HTML version
chdir("../");
if (isset($_GET['source'])) {
    // if the URL ends in "?source" render the markdown file
    echo '<html><head><meta name="robots" content="noindex"></head>';
    echo "<body style='margin: 0px;'>";
    echo "<div style=\"background-color: #333; color: #EEE; padding: 10px; font-family: sans-serif;\" >";
    echo "Markdown source for <a href='$reqPath' style='color: #FFF;'>$reqPath</a>";
    echo "</div><pre style='padding: 0px 20px;'>\n";
    echo htmlentities(file_get_contents($filePath));
    echo "\n</pre></body></html>";
    exit;
} else {
    // convert markdown to HTML and serve using the template
    require "md2html/md2html.php";
    $md2html = new md2html($filePath);
    include("md2html/template.php");
}