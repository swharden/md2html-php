<?php

error_reporting(E_ALL);

$pathHere = dirname(__file__);
require("$pathHere/../md2html/Page.php");
require("$pathHere/../md2html/ArticleList.php");

/** Serve the Nth page of blog posts (starting at 0) */
function echoBlogPage(string $blogPath, int $pageIndex)
{
    $articleList = new ArticleList($blogPath, 5);
    $articles = $articleList->getPageOfArticles($pageIndex);

    $page = new Page();
    $page->enablePermalink(true);
    $page->addArticles($articles);

    for ($i = 0; $i < $articleList->pageCount; $i++) {
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
