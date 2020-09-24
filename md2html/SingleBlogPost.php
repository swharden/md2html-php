<?php

require_once __DIR__ . "/Page.php";
require_once __DIR__ . "/models/BlogPost.php";

class SingleBlogPost extends Page
{
    function __construct($blog_post_folder)
    {
        $post = new BlogPost($blog_post_folder . "/index.md", false, true, true);
        $this->title = $post->title;
        $this->footer = "<a href='?source'>view source</a>";

        if (isset($_GET["source"])) {
			$codeHtml = htmlentities($post->markdown);
            $this->content = "<article><div id='md2html'>" .
                "<div><strong>Source code for <a href='$post->url_folder'>$post->title</a></strong></div>" .
                "<pre style='white-space: pre-wrap; font-size: 80%; line-height: 1.4em; background-color: #f9f9f9; " .
                "border: 1px solid #eee; padding: 1em;'>$codeHtml</pre>" .
                "</div></article>";
        } else {
            $this->content =  $post->html;
        }

        $nav = "";
        if (isset($post->neighborFolderNewer)) {
            $postNewer = new BlogPost($post->neighborFolderNewer . "/index.md");
            $nav.= "<div>Newer: <a href='$postNewer->url_folder'>$postNewer->title</a></div>";
        }
        if (isset($post->neighborFolderOlder)) {
            $postOlder = new BlogPost($post->neighborFolderOlder . "/index.md");
            $nav.= "<div>Older: <a href='$postOlder->url_folder'>$postOlder->title</a></div>";
        }
        $nav.= "<div><a href='/blog/posts'>All Blog Posts</a></div>";
        $this->lowerNav = $nav;
    }
}
