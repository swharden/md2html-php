<?php

error_reporting(E_ALL);

// determine paths and URLs based on the request and the location of this file
$requestedFolder = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
$markdownFilePath = $_SERVER['DOCUMENT_ROOT'] . $requestedFolder . 'index.md';

// build the page from multiple articles
require('Page.php');
$page = new Page();
$page->addArticle($markdownFilePath);
echo $page->getHtml();
