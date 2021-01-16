<?php

/** This represents a list of articles, like a master index of blog posts.
 * Logic for retrieving page N of articles is contained here.
 */
class ArticleList
{
    private array $mdPaths = array();
    private int $articlesPerPage;
    public int $pageCount;

    /** blog path is the path to the folder containing sub-folders each with an index.md inside */
    function __construct(string $blogPath, int $articlesPerPage)
    {
        if (!is_dir($blogPath))
            throw new ErrorException("invalid blog folder: $blogPath");
        $blogPath = realpath($blogPath);
        
        $this->articlesPerPage = $articlesPerPage;
        $dir = new DirectoryIterator($blogPath);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot()) continue;
            $mdPath =  $blogPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . "index.md";
            if (file_exists($mdPath))
                $this->mdPaths[] = $mdPath;
        }
        $this->mdPaths = array_reverse($this->mdPaths);
        $this->pageCount = count($this->mdPaths) / $this->articlesPerPage + 1;
    }

    /** returns paths to markdown files for all articles */
    public function getAllArticles(): array
    {
        return $this->mdPaths;
    }

    /** returns paths to markdown files on the Nth page (considering page count defined at the class-level) */
    public function getPageOfArticles(int $pageIndex): array
    {
        if ($pageIndex < 0)
            return [];

        $firstIndex = $this->articlesPerPage * $pageIndex;
        return array_slice($this->mdPaths, $firstIndex, $this->articlesPerPage);
    }
}
