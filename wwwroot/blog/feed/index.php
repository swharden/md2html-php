<?php

// this script serves the latest 20 posts as an RSS feed

require('../Blog.php');
$blog = new Blog();
header('Content-Type: application/rss+xml; charset=utf-8');
echo $blog->getRSS(20);