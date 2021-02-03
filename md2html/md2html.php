<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function ServeSingleMarkdownFile(string $mdFilePath, string  $pageTemplate, string $articleTemplate)
{
    // create a HTML page around the file
    require('Page/SingleMarkdownFilePage.php');
    $page = new SingleMarkdownFilePage($mdFilePath);
    $html = $page->getHtml($pageTemplate, $articleTemplate);

    // tweak the page based on context
    require('Configuration.php');
    $config = new Configuration();
    $html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_ADS);
    $html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_ANALYTICS);
    //$html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_NOINDEX);

    echo $html;
}
