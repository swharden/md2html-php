<?php

class Sitemap
{
    public string $xml;

    /* Create a sitemap by identifying folders with index.md
    *   basePath is the absolute folder path you want to search for the sitemap
    *   baseUrl is the URL that matches that base folder (not ending with slash)
    */
    public function __construct(string $baseUrl, int $maxDepth = 2)
    {
        $benchmarkStart = microtime(true);
        $baseUrl = str_replace("http://", "https://", $baseUrl);
        $basePath = "./";
        $urls = $this->getIndexUrls($basePath, $baseUrl, $maxDepth);
        $this->xml = $this->getSitemapXml($urls);

        $benchmarkEnd = microtime(true);
        $benchmarkMilliseconds = round(1000 * ($benchmarkEnd - $benchmarkStart), 3);
        $this->xml .= "<!-- sitemap generated in $benchmarkMilliseconds ms -->";
    }

    /* Serve XML headers and echo XML the sitemap */
    public function serve()
    {
        header('Content-type: application/xml');
        echo $this->xml;
    }

    private function getIndexUrls(string $basePath, string $baseUrl, int $maxDepth)
    {
        $directoryIterator = new RecursiveDirectoryIterator($basePath, FilesystemIterator::FOLLOW_SYMLINKS);
        $fileIterator = new RecursiveIteratorIterator($directoryIterator);
        $fileIterator->setMaxDepth($maxDepth);
        $urls = [$baseUrl];
        foreach ($fileIterator as $filePath) {
            if (basename($filePath) == "index.md") {
                $urls[] = $baseUrl . dirname(substr($filePath, 1));
            }
        }
        return $urls;
    }

    private function getSitemapXml(array $urls)
    {
        $xml = "";
        $xml .= "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>";
        foreach ($urls as $url) {
            $xml .= "<url><loc>$url</loc></url>";
        }
        $xml .= "</urlset>";
        return $xml;
    }
}
