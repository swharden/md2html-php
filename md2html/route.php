<?

// we only get here the URL is a folder with index.md but not index.php
require_once("SingleBlogPost.php");
$requested_folder_path = $_SERVER['DOCUMENT_ROOT'] . strtok($_SERVER["REQUEST_URI"], '?');
$post = new SingleBlogPost($requested_folder_path); 
echo $post;