<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('Page/SingleMarkdownFilePage.php');
require('Configuration.php');

function ServeSingleMarkdownFile(
    string $mdFilePath,
    string  $pageTemplate,
    string $articleTemplate,
    bool $ads = true,
    bool $analytics = true,
    bool $robotsIndex = true
) {
    $page = new SingleMarkdownFilePage($mdFilePath);
    $html = $page->getHtml($pageTemplate, $articleTemplate);

    $config = new Configuration();
    if ($ads)
        $html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_ADS);
    if ($analytics)
        $html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_ANALYTICS);
    if ($robotsIndex == false)
        $html = $page->addBeforeClosingHeader($html, $config->TEMPLATE_NOINDEX);

    echo $html;
}
