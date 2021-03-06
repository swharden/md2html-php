<?php

// disable dynamic PHP caching so the markdown file is forced to parse every time
//header("Cache-Control: max-age=6000");
header("Cache-Control: no-cache");

require_once('../md2html/Sitemap.php');
$baseUrl = dirname("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$sitemap = new Sitemap($baseUrl);
$sitemap->serve();