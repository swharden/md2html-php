<?php

require_once __DIR__ . "/Page.php";
require_once __DIR__ . "/models/BlogPost.php";
require_once __DIR__ . "/models/BlogPosts.php";

class PageOfPosts extends Page
{
    function __construct(string $blog_post_folder, int $page_number, int $posts_per_page = 5, string $tag = "all")
    {
        if ($page_number == 1) {
            $this->allowAds = false;
        }

        $articles = [];
        $allPosts = new BlogPosts($blog_post_folder);
        foreach ($allPosts->newestFirst as $article) {
            if ($tag == "all" || in_array(str_replace("-", " ", $tag), $article->tags)) {
                $articles[] = $article;
            }
        }

        $totalPages = count($articles) / $posts_per_page;
        $page_index = $page_number - 1;
        $articles = array_slice($articles, $page_index * $posts_per_page, $posts_per_page);

        $html = "";
        foreach ($articles as $article) {
            $post = new BlogPost($article->markdown_file_path, false, true, true);
            $html .= $post->html;
        }

        $pageLinks = [];
        for ($i = 1; $i < $totalPages + 1; $i++) {
            $pageUrl = ($tag == "all") ? "/blog/page" : "/blog/category/" . str_replace(" ", "-", $tag);
            $link = "<a href='$pageUrl/$i'>page $i</a>";
            if ($i == $page_number)
                $link = "<b>$link</b>";
            $pageLinks[] = $link;
        }
        $nav = "<div>" . join(", ", $pageLinks) . "</div>";
        $nav .= "<div><a href='/blog/posts'>All Blog Posts</a></div>";
        $this->lowerNav = $nav;
        $this->title = ($tag == "all") ? "All Posts - Page {$page_number}" : "Posts Tagged '" . ucwords($tag) . "' - Page {$page_number}";
        $this->content = $html;
    }
}
