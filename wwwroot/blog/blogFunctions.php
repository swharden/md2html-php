<?php

/* Code here is specific to complex multi-page websites. 
*  This code uses the primary md2html components, but the complexity is isolated to this subfolder.
*  Features supported include:
*    - index page of all articles (including metadata like title and tags)
*    - pagination
*    - tags/categories
*    - RSS feed showing metadata from latest N articles
*    - XML sitemap
*/

require_once(dirname(__file__) . "/../md2html/misc.php");
require_once(dirname(__file__) . "/../md2html/Page.php");
require_once(dirname(__file__) . "/../md2html/ArticleInfo.php");

/** Return an array of paths to markdown files in reverse lexicographical order */
function getBlogArticlePaths(string $tag = ""): array
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

/** Serve the Nth page of blog posts (starting at 0) */
function echoBlogPage(int $pageIndex, string $tag = "", int $articlesPerPage = 5)
{
    // inventory available articles
    $articlePaths = getBlogArticlePaths($tag);

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

    echo $page->getHtml();
}

/** Return a color unique to the text used as input */
function colorHash(string $text)
{
    $hex = md5($text);
    $hashValue = hexdec(substr($hex, 0, 6));
    return hexColorFromHSV($hashValue);
}

function hexColor(float $r, float $g, float $b)
{
    $new_hex = '#';
    $new_hex .= str_pad(dechex($r), 2, 0, STR_PAD_LEFT);
    $new_hex .= str_pad(dechex($g), 2, 0, STR_PAD_LEFT);
    $new_hex .= str_pad(dechex($b), 2, 0, STR_PAD_LEFT);
    return $new_hex;
}

function hexColorFromHSV(float $hue, float $saturation = .1, float $value = 1)
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
        return hexColor($v, $t, $p);
    else if ($hi == 1)
        return hexColor($q, $v, $p);
    else if ($hi == 2)
        return hexColor($p, $v, $t);
    else if ($hi == 3)
        return hexColor($p, $q, $v);
    else if ($hi == 4)
        return hexColor($t, $p, $v);
    else
        return hexColor($v, $p, $q);
}

/** Return <li>info</li> about the given article */
function getArticleLi(ArticleInfo $info)
{
    $html = "";
    $html .= "<li class='my-1'>";
    $html .= "$info->dateStringShort ";
    $url = "../" . basename(dirname($info->path));
    $html .= "<a href='$url'><strong>$info->title</strong></a>";
    foreach ($info->tags as $tag) {
        $bgColor = colorHash($tag);
        $tagUrl = "../category/" . sanitizeLinkUrl($tag);
        $html .= "<span class='badge rounded-pill border fw-normal ms-1' style='background-color: $bgColor'>" .
            "<a href='$tagUrl' style='color: #00000066'>$tag</a></span>";
    }
    $html .= "</li>";
    return $html;
}

/** Serve a page listing all blog posts */
function echoBlogIndex()
{
    $html = "<h1>All Blog Posts</h1>";
    $html .= "<ul>";
    foreach (getBlogArticlePaths() as $articlePath) {
        $info = new ArticleInfo($articlePath);
        $html .= getArticleLi($info);
    }
    $html .= "</ul>";

    $page = new Page();
    $page->addHtml($html);
    echo $page->getHtml();
}

/** Serve a page listing all blog posts grouped by category */
function echoTagPage()
{
    $infos = [];
    $tags = [];
    foreach (getBlogArticlePaths() as $articlePath) {
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
                $html .= getArticleLi($info);
            }
        }
        $html .= "</ul>";
    }
    $page = new Page();
    $page->addHtml($html);
    echo $page->getHtml();
}

/** Serve the latest N posts in RSS format */
function echoBlogFeed(int $postCount)
{
    $articlePaths = array_slice(getBlogArticlePaths(), 0, $postCount);
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

    header('Content-Type: application/rss+xml; charset=utf-8');
    echo $rss;
}
