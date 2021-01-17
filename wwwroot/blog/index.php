<?php

// this script serves the first page of blog posts

require('Blog.php');

$pageIndex = 0;
if (isset($_GET['page'])) {
    $pageIndex = intval($_GET['page']) - 1;
}

$blog = new Blog();
echo $blog->getPageHTML($pageIndex, "");