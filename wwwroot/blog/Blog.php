<?php

require_once(dirname(__file__) . "/../md2html/misc.php");
require_once(dirname(__file__) . "/../md2html/Page.php");
require_once(dirname(__file__) . "/../md2html/ArticleInfo.php");

/** Tools for serving a complex multi-page website */
class Blog
{

    /** Serve the Nth page of blog posts (starting at 0) */
    public function getPageHTML(int $pageIndex, string $tag = "", int $articlesPerPage = 5): string
    {
        // inventory available articles
        $articlePaths = $this->getBlogArticlePaths($tag);

        // determine which articles to show
        $pageIndex = max(0, $pageIndex);
        $pageCount = count($articlePaths) / $articlesPerPage;
        $firstIndex = $articlesPerPage * $pageIndex;
        $isValidPageIndex = ($pageIndex >= 0);
        $articlesToShow = $isValidPageIndex ? array_slice($articlePaths, $firstIndex, $articlesPerPage) : [];

        // add the articles to the page
        $page = new Page();
        $page->enablePermalink(true);
        $page->addArticles($articlesToShow);

        // add pagination links
        for ($i = 0; $i < $pageCount; $i++) {
            $pageNumber = $i + 1;
            $pageIsActive = ($i == $pageIndex);
            $page->addPagination("$pageNumber", "?page=$pageNumber", $pageIsActive);
        }

        return $page->getHtml();
    }

    /** Serve a page listing all blog posts */
    public function getPostIndexHTML(): string
    {
        $html = "<h1>All Blog Posts</h1>";
        $html .= "<ul>";
        foreach ($this->getBlogArticlePaths() as $articlePath) {
            $info = new ArticleInfo($articlePath);
            $html .= $this->getArticleLi($info);
        }
        $html .= "</ul>";

        $page = new Page();
        $page->addHtml($html);
        return $page->getHtml();
    }

    /** Serve a page listing all blog posts grouped by category */
    function getCategoryIndexHTML(): string
    {
        $infos = [];
        $tags = [];
        foreach ($this->getBlogArticlePaths() as $articlePath) {
            $info = new ArticleInfo($articlePath);
            $infos[] = $info;
            foreach ($info->tags as $tag)
                $tags[] = $tag;
        }
        $tags = array_unique($tags);
        sort($tags);

        $html = "<h1>Categories</h1>";
        foreach ($tags as $tag) {
            $html .= "<h2>$tag</h2>";
            $sanTag = sanitizeLinkUrl($tag);
            $html .= "<ul>";
            foreach ($infos as $info) {
                if (in_array($sanTag, $info->tagsSanitized)) {
                    $html .= $this->getArticleLi($info);
                }
            }
            $html .= "</ul>";
        }
        $page = new Page();
        $page->addHtml($html);
        return $page->getHtml();
    }

    /** Serve the latest N posts in RSS format */
    public function getRSS(int $postCount): string
    {
        $articlePaths = array_slice($this->getBlogArticlePaths(), 0, $postCount);
        $rss = "<?xml version=\"1.0\"?>\n<rss version=\"2.0\">\n    <channel>\n";
        $rss .= "        <title>SWHarden.com</title>\n";
        $rss .= "        <link>https://swharden.com/blog</link>\n";
        $rss .= "        <description>The personal website of Scott W Harden</description>\n";
        foreach ($articlePaths as $articlePath) {
            $info = new ArticleInfo($articlePath);
            $url = "https://swharden.com/blog/" . basename(dirname($info->path));
            $date = date("r", $info->dateTime);
            $rss .= "\n";
            $rss .= "        <item>\n";
            $rss .= "            <title>$info->title</title>\n";
            $rss .= "            <description>$info->description</description>\n";
            $rss .= "            <link>$url</link>\n";
            $rss .= "            <pubDate>$date</pubDate>\n";
            foreach ($info->tags as $tag) {
                $rss .= "            <category>$tag</category>\n";
            }
            $rss .= "        </item>\n";
        }
        $rss .= "    </channel>\n</rss>";
        return $rss;
    }

    /** Return an array of paths to markdown files in reverse lexicographical order */
    private function getBlogArticlePaths(string $tag = ""): array
    {
        $blogPath = realpath(dirname(__file__));
        $mdPaths = [];
        $dir = new DirectoryIterator($blogPath);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot())
                continue;
            $mdPath =  $blogPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . "index.md";
            if (file_exists($mdPath)) {
                if ($tag == "") {
                    $mdPaths[] = $mdPath;
                } else {
                    $info = new ArticleInfo($mdPath);
                    if (in_array($tag, $info->tagsSanitized))
                        $mdPaths[] = $mdPath;
                }
            }
        }
        rsort($mdPaths);
        return $mdPaths;
    }

    /** Create a hex color code from a hue value (0-360) */
    private function hexColorFromHSV(float $hue, float $saturation = .1, float $value = 1): string
    {
        $hue %= 360;
        $hi = (floor($hue / 60)) % 6;
        $f = $hue / 60 - floor($hue / 60);

        $value *= 255;
        $v = $value;
        $p = $value * (1 - $saturation);
        $q = $value * (1 - $f * $saturation);
        $t = $value * (1 - (1 - $f) * $saturation);

        if ($hi == 0)
            return $this->hexColor($v, $t, $p);
        else if ($hi == 1)
            return $this->hexColor($q, $v, $p);
        else if ($hi == 2)
            return $this->hexColor($p, $v, $t);
        else if ($hi == 3)
            return $this->hexColor($p, $q, $v);
        else if ($hi == 4)
            return $this->hexColor($t, $p, $v);
        else
            return $this->hexColor($v, $p, $q);
    }

    /** Return a color unique to the text used as input */
    private function colorHash(string $text): string
    {
        $hex = md5($text);
        $hashValue = hexdec(substr($hex, 0, 6));
        return $this->hexColorFromHSV($hashValue);
    }

    /** Return a color unique to the text used as input */
    private function hexColor(float $r, float $g, float $b): string
    {
        $new_hex = '#';
        $new_hex .= str_pad(dechex($r), 2, 0, STR_PAD_LEFT);
        $new_hex .= str_pad(dechex($g), 2, 0, STR_PAD_LEFT);
        $new_hex .= str_pad(dechex($b), 2, 0, STR_PAD_LEFT);
        return $new_hex;
    }

    /** Return <li>info</li> about the given article */
    private function getArticleLi(ArticleInfo $info): string
    {
        $html = "";
        $html .= "<li class='my-1'>";
        $html .= "$info->dateStringShort ";
        $url = "../" . basename(dirname($info->path));
        $html .= "<a href='$url'><strong>$info->title</strong></a>";
        foreach ($info->tags as $tag) {
            $bgColor = $this->colorHash($tag);
            $tagUrl = "../category/" . sanitizeLinkUrl($tag);
            $html .= "<span class='badge rounded-pill border fw-normal ms-1' style='background-color: $bgColor'>" .
                "<a href='$tagUrl' style='color: #00000066'>$tag</a></span>";
        }
        $html .= "</li>";
        return $html;
    }
}
