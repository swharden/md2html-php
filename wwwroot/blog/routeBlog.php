<?php

// special routing rules for blog pages
error_reporting(E_ALL);

// determine paths and URLs based on the request and the location of this file
$requestedFolder = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
$markdownFilePath = $_SERVER['DOCUMENT_ROOT'] . $requestedFolder . 'index.md';

// build the page from multiple articles
require('../md2html/Page.php');
$page = new Page();
$page->addArticle($markdownFilePath);
$page->enablePermalink(true, 'http://localhost:8081/blog');
echo $page->getHtml();
