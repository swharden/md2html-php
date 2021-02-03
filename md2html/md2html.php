<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function ServeSingleMarkdownFile(string $mdFilePath, string  $pageTemplate, string $articleTemplate)
{
    require('Page/SingleMarkdownFilePage.php');
    $page = new SingleMarkdownFilePage($mdFilePath);
    echo $page->getHtml($pageTemplate, $articleTemplate);
}
