<?php

$rootFolder = dirname($_SERVER['DOCUMENT_ROOT'] . $_SERVER["REQUEST_URI"]);

function getDirContents($dir, &$results = array())
{
    foreach (scandir($dir) as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        $path = str_replace('\\', '/', $path);
        if (!is_dir($path)) {
            if (substr(basename($path), 0, 1) == ".")
                continue;
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            if (basename($path) == "md2html")
                continue;
            getDirContents($path, $results);
            $results[] = $path . "/";
        }
    }
    return $results;
}

function endsWith($haystack, $needle, $ignoreCase = true)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    if ($ignoreCase)
        return (strtoupper(substr($haystack, -$length)) === strtoupper($needle));
    else
        return (substr($haystack, -$length) === $needle);
}

function echoUrl($filePath)
{
    // https://www.sitemaps.org/protocol.html
    $httpPrefix = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $urlHere = "$httpPrefix://$_SERVER[HTTP_HOST]";

    $loc = endswith($filePath, ".md") ? $filePath . ".html" : $filePath;
    $loc = str_replace($_SERVER['DOCUMENT_ROOT'], $urlHere, $loc);
    $lastmod = date("Y-m-d", filemtime($filePath));
    echo "<url>";
    echo "<loc>$loc</loc>";
    echo "<lastmod>$lastmod</lastmod>";
    echo "</url>";
}

header("Content-type: text/xml");
echo "<?xml version='1.0' encoding='UTF-8'?>";
echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
foreach (getDirContents($rootFolder) as $filePath) {

    if (endsWith($filePath, '/')) {
        echoUrl($filePath);
        continue;
    }

    $fileNameParts = explode('.', basename($filePath));
    if (count($fileNameParts) > 1) {
        $ext = $fileNameParts[count($fileNameParts) - 1];
        if ($ext === "php" || $ext === "htm" || $ext === "html" || $ext == "md") {
            echoUrl($filePath);
        }
    }
}
echo '</urlset>';
