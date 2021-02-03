<?

// .htaccess redirected a request to a folder containing index.md
require('../../../md2html/md2html.php');
$mdFilePath = realpath($_SERVER['DOCUMENT_ROOT']) . rtrim($_SERVER['REQUEST_URI'], '/') . '/index.md';
$pageTemplate = file_get_contents('page.html');
$articleTemplate = file_get_contents('article.html');
ServeSingleMarkdownFile($mdFilePath, $pageTemplate, $articleTemplate);