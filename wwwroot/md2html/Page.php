<?php

require_once('Article.php');
require_once('Pagination.php');
require_once('misc.php');

/** This object assembles and returns a complete HTML page.
 * Instantiate it, customize settings, add article(s), then getHtml().
 */
class Page
{
    private float $timeStart;
    private array $articles = array();
    private array $replacements = array();
    private bool $showPermalink = false;
    private string $baseUrl = "";

    public Pagination $pagination;

    function __construct()
    {
        $this->timeStart = microtime(true);
        $this->replacements = include('settings.php');
        $this->pagination = new Pagination();
    }

    public function addArticle(string $markdownFilePath, string $baseUrl = "")
    {
        $article = new Article($markdownFilePath, $baseUrl);
        $this->articles[] = $article;
        $this->setTitle($article->info->title);
        $this->setDescription($article->info->description);
    }

    public function addArticles(array $markdownFilePaths, string $baseUrl = "")
    {
        foreach ($markdownFilePaths as $mdPath)
            $this->articles[] = new Article($mdPath, $baseUrl);
    }

    public function addHtml(string $html)
    {
        $this->articles[] = $html;
    }

    public function setTitle(string $title)
    {
        $this->replacements["{{title}}"] = $title;
    }

    public function setDescription(string $description)
    {
        $this->replacements["{{description}}"] = $description;
    }

    public function disableAds()
    {
        $this->replacements["{{adsHtml}}"] = "<!-- ads disabled for this page -->";
    }

    public function disableIndexing()
    {
        $this->replacements["{{robotsContent}}"] = "noindex";
    }

    public function enablePermalink(bool $enabled, string $baseUrl)
    {
        $this->showPermalink = $enabled;
        $this->baseUrl = $baseUrl;
    }

    public function getHtml(): string
    {
        $html = $this->getPageHtml();
        $html = str_replace('{{articles}}', $this->getArticleHtml(), $html);
        $html = str_replace('{{pagination}}', $this->pagination->getHtml(), $html);
        $html = str_replace('{{elapsedMsec}}', round((microtime(true) - $this->timeStart) * 1000, 3), $html);
        return $html;
    }

    /** when serving articles from another folder, local URLs need to be fixed */
    private function addBaseUrlToLinksAndImages(string $html, string $baseUrl): string
    {
        if ($baseUrl == "")
            return $html;
        $html = str_replace("<img src='", "<img src='{{baseUrl}}", $html);
        $html = str_replace("<img src=\"", "<img src=\"{{baseUrl}}", $html);
        $html = str_replace("<a href='", "<a href='{{baseUrl}}", $html);
        $html = str_replace("<a href=\"", "<a href=\"{{baseUrl}}", $html);
        $html = str_replace('{{baseUrl}}http', 'http', $html);
        $html = str_replace('{{baseUrl}}', $baseUrl . '/', $html);
        return $html;
    }

    function getPermalinkHtml(Article $article)
    {
        if ($this->showPermalink == false)
            return "";

        $html = "";
        $url = $this->baseUrl . "/" . $article->info->folderName;
        $html .= "<div><a href='$url'><small>" . $article->info->title . "</small></a></div>";
        $html .= "<div><small>" . $article->info->dateString . "</small></div>";
        $tagParts = [];
        foreach ($article->info->tags as $tag) {
            $tagUrl = $this->baseUrl . "/category/" . sanitizeLinkUrl($tag);
            $tagParts[] = "<a href='$tagUrl'>$tag</a>";
        }
        $tagHtml = implode(', ', $tagParts);
        $html .= "<div><small>$tagHtml</small></div>";
        return $html;
    }

    private function getArticleHtml(): string
    {
        $html = "";
        $templateFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'template_article.html';
        $articleTemplate = file_get_contents($templateFilePath);
        for ($i = 0; $i < count($this->articles); $i++) {
            $article = $this->articles[$i];

            if (is_a($article, 'Article')) {
                // this element of the array holds a markdown file article
                $contentHtml = $article->html;
                if ($this->baseUrl != "") {
                    $baseUrl = $this->baseUrl . "/" . $article->info->folderName;
                    $contentHtml = $this->addBaseUrlToLinksAndImages($contentHtml, $baseUrl);
                }
                $articleHtml = $articleTemplate;
                $articleHtml = str_replace("{{title}}", $article->info->title, $articleHtml);
                $articleHtml = str_replace("{{content}}", $contentHtml, $articleHtml);
                $articleHtml = str_replace("{{permalink}}", $this->getPermalinkHtml($article), $articleHtml);
                $articleHtml = str_replace("{{source}}", $article->sourceHtml, $articleHtml);
                $articleHtml = str_replace("{{id}}", $i, $articleHtml);
                $articleHtml = str_replace('{{modifiedDate}}', gmdate("F jS, Y", $article->info->modified), $articleHtml);
                $articleHtml = str_replace('{{modifiedTime}}', gmdate("H:i:s", $article->info->modified), $articleHtml);
                $articleHtml = str_replace('{{viewSourceMessage}}', "view page source", $articleHtml);
                $html .= $articleHtml;
            } else {
                // this element of the array holds a string of HTML
                $articleHtml = $articleTemplate;
                $articleHtml = str_replace("{{content}}", $article, $articleHtml);
                $articleHtml = str_replace("{{permalink}}", "", $articleHtml);
                $articleHtml = str_replace("{{source}}", "", $articleHtml);
                $articleHtml = str_replace('{{viewSourceMessage}}', "", $articleHtml);
                $html .= $articleHtml;
            }
        }
        return $html;
    }

    private function getPageHtml(): string
    {
        $templateFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'template_page.html';
        $html = file_get_contents($templateFilePath);

        // fix relative file paths in header
        $http = isset($_SERVER['HTTPS']) ? "https://" : "http://";
        $baseUrl = $http . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__DIR__));
        $html = str_replace('href="resources/', 'href="{{baseUrl}}/md2html/resources/', $html);
        $html = str_replace('src="resources/', 'src="{{baseUrl}}/md2html/resources/', $html);

        // hard-coded replacements
        $html = str_replace('{{baseUrl}}', $baseUrl, $html);
        $html = str_replace('{{year}}', gmdate("Y", date("Z") + time()), $html);
        $html = str_replace('{{date}}', gmdate("F jS, Y", date("Z") + time()), $html);
        $html = str_replace('{{time}}', gmdate("H:i:s", time() + time()), $html);

        // custom replacements
        foreach ($this->replacements as $key => $value)
            $html = str_replace($key, $value, $html);

        return $html;
    }
}
