<?php

// this script serves the first page of blog posts

require('blogFunctions.php');

$pageIndex = 0;
if (isset($_GET['page'])) {
    $pageIndex = intval($_GET['page']);
}

echoBlogPage($pageIndex);
