<?php

require_once __DIR__ . "/BlogPost.php";

class BlogPosts
{
    public array $newestFirst;
    public array $oldestFirst;

    function __construct($blog_folder)
    {
        $articles = [];
        $blog_folder = realpath($blog_folder);
        if (!is_dir($blog_folder))
            throw new Exception('blog folder not found');

        foreach (scandir($blog_folder) as $folder) {
            if ($folder == "." || $folder == "..")
                continue;

            $markdown_file_path = "$blog_folder/$folder/index.md";
            if (file_exists($markdown_file_path)) {
                $articles[] = new BlogPost($markdown_file_path, true);
            }
        }

        usort($articles, array("BlogPost", "compareDate"));
        $this->oldestFirst = $articles;
        rsort($articles);
        $this->newestFirst = $articles;
    }
}
