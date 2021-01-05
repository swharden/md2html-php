<?php

require_once __DIR__ . "/models/BlogPost.php";
require_once __DIR__ . "/models/BlogPosts.php";

class RssFeed
{
    function __construct(string $blog_post_folder, int $postCount = 20)
    {
        $allPosts = new BlogPosts($blog_post_folder);
        $articles = array_slice($allPosts->newestFirst, 0, $postCount);

        $rss = "<?xml version=\"1.0\"?>\n<rss version=\"2.0\">\n    <channel>\n";
        $rss .= "        <title>SWHarden.com</title>\n";
        $rss .= "        <link>https://swharden.com/blog</link>\n";
        $rss .= "        <description>The personal website of Scott W Harden</description>\n";
        foreach ($articles as $article) {
            $post = new BlogPost($article->markdown_file_path, false, true, true);
            $rss .= "\n";
            $rss .= "        <item>\n";
            $rss .= "            <title>$post->title</title>\n";
            $rss .= "            <link>https://swharden.com$post->url_folder</link>\n";
            //$rss .= "            <description>$post->html</description>\n";
            //$rss .= "            <content:encoded>$post->html</content:encoded>\n";
            $rss .= "            <pubDate>" . date("r", $post->epochTime) . "</pubDate>\n";
            $rss .= "        </item>\n";
        }
        $rss .= "    </channel>\n</rss>";

        header('Content-Type: application/rss+xml; charset=utf-8');
        echo $rss;
    }
}
