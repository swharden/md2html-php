<?php

error_reporting(E_ALL);

// determine paths and URLs based on the request and the location of this file
$requestedFolder = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
$markdownFilePath = $_SERVER['DOCUMENT_ROOT'] . $requestedFolder . 'index.md';

// put markdown HTML inside the template


require('SingleArticlePage.php');
$page = new SingleArticlePage($markdownFilePath);
echo $page->getHtml();
