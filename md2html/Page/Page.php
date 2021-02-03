<?php

require_once(__DIR__ . '/../Article/Article.php');

abstract class Page
{
    public string $title;
    public string $description;
    public int $date;
    private array $articles = [];
    private float $timeStart;

    public function __construct()
    {
        $this->timeStart = microtime(true);
    }

    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
    }

    public function getHtml(string $pageTemplate, string $articleTemplate)
    {

        // TEMPLATE REPLACEMENTS
        $html = $pageTemplate;
        $html = str_replace("{{title}}", $this->title, $html);
        $html = str_replace("{{description}}", $this->description, $html);
        $html = str_replace('{{year}}', gmdate("Y", date("Z") + time()), $html);

        // CONTENT REPLACEMENTS
        $content = "";
        foreach ($this->articles as $article) {
            $content .= $article->getHtml($articleTemplate);
        }
        $html = str_replace("{{content}}", $content, $html);

        // FINAL REPLACEMENTS
        $elapsedMilliseconds = round((microtime(true) - $this->timeStart) * 1000, 3);
        $html = str_replace('{{benchmark}}', $elapsedMilliseconds, $html);

        return $html;
    }

    public function addBeforeClosingHeader(string $html, $headerText)
    {
        $pos = strpos($html, "</head>");
        return substr($html, 0, $pos) . "\n$headerText\n" . substr($html, $pos);
    }
}
