<?php

// disable dynamic PHP caching so the markdown file is forced to parse every time
//header("Cache-Control: max-age=6000");
header("Cache-Control: no-cache");

require_once(__DIR__ . '/Page.php');
require_once(__DIR__ . '/../Article/Article.php');

class SingleMarkdownFilePage extends Page
{
    public function __construct(string $mdFilePath)
    {
        parent::__construct();

        // add a single article
        $article = new Article();
        $article->loadMarkdownFile($mdFilePath);
        $this->addArticle($article);

        // title and description of the page are that of the single article
        $this->title = $article->title ?? "";
        $this->description = $article->description ?? "";
        $this->date = $article->date;
    }
}
