<?php

require(dirname(__file__) . "/../md2html/Page.php");

/** Return an array of paths to markdown files in reverse lexicographical order */
function getBlogArticlePaths(bool $newestFirst = true): array
{
    $blogPath = realpath(dirname(__file__));
    $mdPaths = [];
    $dir = new DirectoryIterator($blogPath);
    foreach ($dir as $fileinfo) {
        if ($fileinfo->isDot())
            continue;
        $mdPath =  $blogPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . "index.md";
        if (file_exists($mdPath))
            $mdPaths[] = $mdPath;
    }

    if ($newestFirst)
        rsort($mdPaths);
    else
        sort($mdPaths);

    return $mdPaths;
}

/** Serve the Nth page of blog posts (starting at 0) */
function echoBlogPage(int $pageIndex, int $articlesPerPage = 5)
{
    // inventory available articles
    $articlePaths = getBlogArticlePaths();

    // determine which articles to show
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
        $page->addPagination("$pageNumber", "./page/$pageNumber", $pageIsActive);
    }

    echo $page->getHtml();
}

/** Serve the latest N posts in RSS format */
function echoBlogFeed(string $blogPath, int $count)
{
}

/** Serve a page listing all blog posts */
function echoBlogIndex()
{
    $articlePaths = getBlogArticlePaths();
    $html = "";
    $html .= "<ul>";
    foreach ($articlePaths as $articlePath) {
        $html .= "<li>$articlePath</li>";
    }
    $html .= "</ul>";

    $page = new Page();
    $page->addHtml($html);
    echo $page->getHtml();
}
