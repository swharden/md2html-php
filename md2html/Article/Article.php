<?php

require_once(__DIR__ . '/../Markdown/ParsedMarkdown.php');

class Article
{
    // meta
    public string $title;
    public string $description;
    public array $tags = [];
    public string $date;
    public string $modified;

    // content
    private string $html;
    private string $sourceHtml;

    public function __construct()
    {
    }

    public function loadMarkdownFile(string $mdFilePath)
    {
        $mdRaw = file_get_contents($mdFilePath);
        $this->sourceHtml = htmlspecialchars($mdRaw);

        $md = new ParsedMarkdown($mdRaw);
        $this->html = $md->html;

        // update fields that may have been defined in frontmatter
        if (isset($md->title))
            $this->title = $md->title;
        if (isset($md->description))
            $this->description = $md->description;
        if (isset($md->date))
            $this->date = $md->date;
        $this->tags[] = $md->tags;
    }

    public function setHtml(string $html)
    {
        $this->sourceHtml = htmlspecialchars($html);
        $this->html = $html;
    }

    public function getHtml(string $template): string
    {
        $html = $template;
        $html = str_replace("{{content}}", $this->html, $html);
        $html = str_replace("{{source}}", $this->sourceHtml, $html);
        return $html;
    }
}
