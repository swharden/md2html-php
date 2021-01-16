<?php

/** This represents a list of articles, like a master index of blog posts.
 * Logic for retrieving page N of articles is contained here.
 */
class ArticleList
{
    private array $mdPaths = array();
    private int $articlesPerPage;
    public int $pageCount;

    function __construct(string $folder, int $articlesPerPage = 5)
    {
        $this->articlesPerPage = $articlesPerPage;

        $dir = new DirectoryIterator($folder);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot()) continue;
            $mdPath =  $folder . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . "index.md";
            if (file_exists($mdPath))
                $this->mdPaths[] = $mdPath;
        }
        $this->mdPaths = array_reverse($this->mdPaths);
        $this->pageCount = count($this->mdPaths) / $this->articlesPerPage + 1;
    }

    public function getAllArticles(): array
    {
        return $this->mdPaths;
    }

    public function getPageOfArticles(int $pageIndex): array
    {
        $firstIndex = $this->articlesPerPage * $pageIndex;
        return array_slice($this->mdPaths, $firstIndex, $this->articlesPerPage);
    }
}
