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
    public array $numberedPages = array();
    public PaginationPage $nextPage;
    public PaginationPage $previousPage;

    public function addNumberedPage(string $label, string $url, bool $isHighlighted, bool $isEnabled)
    {
        $this->numberedPages[] = new PaginationPage($label, $url, $isHighlighted, $isEnabled);
    }

    public function setNextPage(string $label, string $url)
    {
        $this->nextPage = new PaginationPage($label, $url, false, false);
    }

    public function setPreviousPage(string $label, string $url)
    {
        $this->previousPage = new PaginationPage($label, $url, false, false);
    }

    public function getHtmlNumbered()
    {
    }
}
