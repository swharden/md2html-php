<?php

require_once('Article.php');

/** This object assembles and returns a complete HTML page.
 * Instantiate it, customize settings, add article(s), then getHtml().
 */
class Page
{
    private float $timeStart;
    private array $articles = array();
    private array $pagination = array();
    private array $replacements = array();
    private bool $showPermalink = false;

    function __construct()
    {
        $this->timeStart = microtime(true);
        $this->replacements = include('settings.php');
    }

    public function addArticle(string $markdownFilePath)
    {
        $this->articles[] = new Article($markdownFilePath);
    }

    public function addArticles(array $markdownFilePaths)
    {
        foreach ($markdownFilePaths as $mdPath)
            $this->articles[] = new Article($mdPath);
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

    public function addPagination(string $label, string $url, bool $active = false)
    {
        $this->pagination[] = [$label, $url, $active];
    }

    public function enablePermalink(bool $enabled)
    {
        $this->showPermalink = $enabled;
    }

    public function getHtml(): string
    {
        $html = $this->getPageHtml();
        $html = str_replace('{{articles}}', $this->getArticleHtml(), $html);
        $html = str_replace('{{pagination}}', $this->getPaginationHtml(), $html);
        $html = str_replace('{{elapsedMsec}}', round((microtime(true) - $this->timeStart) * 1000, 3), $html);
        return $html;
    }

    function getPermalinkHtml(Article $article)
    {
        if ($this->showPermalink == false)
            return "";

        $html = "";
        $html .= "<div><a href=''><small>" . $article->info->title . "</small></a></div>";
        $html .= "<div><small>" . $article->info->dateString . "</small></div>";
        $tagHtml = "";
        foreach ($article->info->tags as $tag) {
            $tagHtml .= "[<a href=''>$tag</a>] ";
        }
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
                $articleHtml = $articleTemplate;
                $articleHtml = str_replace("{{title}}", $article->info->title, $articleHtml);
                $articleHtml = str_replace("{{content}}", $article->html, $articleHtml);
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

    private function getPaginationHtml(): string
    {
        $html = "<!-- pagination -->";
        foreach ($this->pagination as $pageInfo) {
            $label = $pageInfo[0];
            $url = $pageInfo[1];
            $disabled = ($url == "") ? "disabled" : "";
            $active = $pageInfo[2] ? "active" : "";
            $html .= "<li class='page-item $disabled $active'><a class='page-link' href='$url'>$label</a></li>";
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
