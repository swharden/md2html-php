<?php

require_once('../md2html/Sitemap.php');
$baseUrl = dirname("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$sitemap = new Sitemap($baseUrl);
$sitemap->serve();