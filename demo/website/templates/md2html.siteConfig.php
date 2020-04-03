<?php

// customize your path to md2html
require '../../src/md2html.php';

$configs = array();

// title that appears in the browser tab, bookmarks, and on Google
$configs['defaultHeaderTitle'] = 'Demo Website';

// large title at the top of the page
$configs['defaultPageTitle'] = 'Cool Website';

// default filename for index markdown file
$configs['markdownIndex'] = 'readme.md';

// custom search/replace in either the HTML output or markdown file
$configs['replaceInMarkdown'] = array(
    "../graphics/" => "graphics/",
);
$configs['replaceInHtml'] = array(
    "<b>" => "<strong>",
    "</b>" => "</strong>",
);

return $configs;
