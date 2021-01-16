<?php

error_reporting(E_ALL);

$pathHere = dirname(__file__);
require("$pathHere/../md2html/Page.php");
require("$pathHere/../md2html/ArticleList.php");

function echoBlogPage(int $pageIndex)
{
    $articleList = new ArticleList(dirname(__FILE__));
    $page = new Page();
    $page->enablePermalink(true);
    $page->addArticles($articleList->getPageOfArticles($pageIndex));

    for ($i = 0; $i < $articleList->pageCount; $i++) {
        $pageNumber = $i + 1;
        $pageIsActive = ($i == $pageIndex);
        $page->addPagination("$pageNumber", "./page/$pageNumber", $pageIsActive);
    }

    echo $page->getHtml();
}