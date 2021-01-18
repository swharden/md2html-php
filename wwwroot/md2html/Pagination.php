<?php

class PaginationPage
{
    public string $label;
    public string $url;
    public bool $isHighlighted;
    public bool $isEnabled;

    function __construct(string $label, string $url, bool $isHighlighted, bool $isEnabled)
    {
        $this->label = $label;
        $this->url = $url;
        $this->isHighlighted = $isHighlighted;
        $this->isEnabled = $isEnabled;
    }
}

class Pagination
{
    private array $numberedPages = array();
    private PaginationPage $newerPage;
    private PaginationPage $olderPage;

    public function addNumberedPage(string $label, string $url, bool $isHighlighted, bool $isEnabled)
    {
        $this->numberedPages[] = new PaginationPage($label, $url, $isHighlighted, $isEnabled);
    }

    public function getPageFromMarkdownFile(string $mdPath): PaginationPage
    {
        $info = new ArticleInfo($mdPath);
        return new PaginationPage($info->title, "../" . $info->folderName, false, false);
    }

    public function getHtml(): string
    {
        if (count($this->numberedPages) > 1)
            return $this->getHtmlNumberedPages();
        else if (isset($this->newerPage) || isset($this->olderPage))
            return $this->getHtmlNextPrevious();
        return "<!-- no pagination -->";
    }

    public function getHtmlNumberedPages(): string
    {
        $html = "<!-- numbered pagination -->";
        $html .= "<div class='display-6 m-3 text-center'>Pages</div>";
        $html .= "<nav aria-label='Page navigation'>";
        $html .= "<ul class='pagination justify-content-center flex-wrap'>";
        foreach ($this->numberedPages as $page) {
            $disabled = $page->isEnabled ? "" : "disabled";
            $active = $page->isHighlighted ? "active" : "";
            $html .= "<li class='page-item my-1 text-center $disabled $active' style='width: 3em;'><a class='page-link' href='$page->url'>$page->label</a></li>";
        }
        $html .= "</ul>";
        $html .= "</nav>";
        return $html;
    }

    private function nextPreviousLi(string $category, string $title, string $url): string
    {
        $html = "";
        $html .= "<li class='page-item my-3'>";
        $html .= "<div class='display-6'>$category</div>";
        $html .= "<a class='page-link d-inline-block my-2' href='$url'>$title</a>";
        $html .= "</li>";
        return $html;
    }

    public function getHtmlNextPrevious(): string
    {
        $html = "<!-- next/previous pagination -->";

        $html .= "<nav aria-label='Adjacent page navigation'>";
        $html .= "<ul class='pagination flex-column'>";

        if (isset($this->newerPage)) {
            $html .= $this->nextPreviousLi("Newer", $this->newerPage->label, $this->newerPage->url);
        }

        if (isset($this->olderPage)) {
            $html .= $this->nextPreviousLi("Older", $this->olderPage->label, $this->olderPage->url);
        }

        $html .= $this->nextPreviousLi("All Posts", "Posts organized by date", "../posts");
        $html .= $this->nextPreviousLi("Categories", "Posts organized by category", "../category");

        $html .= "</ul>";
        $html .= "</nav>";
        return $html;
    }

    /** Given the path to a single article markdown file, set next/previous by looking at adjacent articles */
    public function setNextPrevious(string $mdPath)
    {
        // list all directories with an index.md
        $mdPath = realpath($mdPath);
        $parentFolder = dirname(dirname($mdPath));
        $adjacentMdPaths = glob("$parentFolder/*/index.md");
        $thisIndex = array_search($mdPath, $adjacentMdPaths);

        if ($thisIndex + 1 < count($adjacentMdPaths))
            $this->newerPage = $this->getPageFromMarkdownFile($adjacentMdPaths[$thisIndex + 1]);

        if ($thisIndex - 1 >= 0)
            $this->olderPage = $this->getPageFromMarkdownFile($adjacentMdPaths[$thisIndex - 1]);
    }
}
