<?php

require_once(__DIR__ . '/../Article/Article.php');

abstract class Page
{
    public string $title;
    public string $description;
    private array $articles = [];

    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
    }

    public function getHtml(string $pageTemplate, string $articleTemplate)
    {
        $html = $pageTemplate;

        // META
        $html = str_replace("{{title}}", $this->title, $html);
        $html = str_replace("{{description}}", $this->description, $html);

        // CONTENT
        $content = "";
        foreach ($this->articles as $article) {
            $content .= $article->getHtml($articleTemplate);
        }
        $html = str_replace("{{content}}", $content, $html);

        return $html;
    }
}
