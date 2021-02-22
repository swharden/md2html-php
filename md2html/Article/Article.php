<?php

require_once(__DIR__ . '/../Markdown/ParsedMarkdown.php');

class Article
{
    // meta
    public string $title;
    public string $description;
    public array $tags = [];
    public int $date;
    public int $modified;

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
        $this->modified = filemtime($mdFilePath);

        $md = new ParsedMarkdown($mdRaw, dirname($mdFilePath));
        $this->html = $md->html;

        // update fields that may have been defined in frontmatter
        if (isset($md->title))
            $this->title = $md->title;
        if (isset($md->description))
            $this->description = $md->description;
        $this->date = $md->date ?? $this->modified;
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
        $html = str_replace("{{articleGUID}}", uniqid(), $html);
        $html = str_replace("{{modified}}", date("F jS, Y", $this->modified), $html);
        return $html;
    }
}
