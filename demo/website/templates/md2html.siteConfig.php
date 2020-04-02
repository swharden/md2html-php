<?php

$configs = array();

// title that appears in the browser tab, bookmarks, and on Google
$configs['defaultHeaderTitle'] = 'Demo Website';

// large title at the top of the page
$configs['defaultPageTitle'] = 'Cool Website';

// default filename for index markdown file
$configs['markdownIndex'] = 'index.md';

// custom search/replace in either the HTML output or markdown file
$configs['replaceInMarkdown'] = array(
    "../graphics/" => "graphics/",
);
$configs['replaceInHtml'] = array(
    "<b>" => "<strong>",
    "</b>" => "</strong>",
);

return $configs;
