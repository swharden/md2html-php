<?php

// this script serves the first page of blog posts

header('Content-type: text/xml');
require('Blog.php');
$blog = new Blog();
echo $blog->getSitemap();