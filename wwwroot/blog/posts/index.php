<?php

// this script lists all blog articles, dates, and tags
require('../Blog.php');
$blog = new Blog();
echo $blog->getPostIndexHTML(0, "");
