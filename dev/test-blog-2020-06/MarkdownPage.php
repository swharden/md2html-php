<?php

require_once __DIR__ . "/Page.php";
require_once __DIR__ . "/models/BlogPost.php";

class MarkdownPage extends Page
{
    function __construct($markdown_file_path, $allowAds = false;)
    {
        $post = new BlogPost($markdown_file_path, false, false);
        $this->title = $post->title;
        $this->footer = "<a href='?source'>view source</a>";
        $this->allowAds = $allowAds;

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
    }
}
