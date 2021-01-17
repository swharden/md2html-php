<?php

// this script serves the Nth page of blog posts (where N is defined by the URL)
// restricted to a specific category

require('../blogFunctions.php');
$finalFolderName = basename(strtok($_SERVER["REQUEST_URI"], '?'));

$pageIndex = 0;
if (isset($_GET['page'])) {
    $pageIndex = intval($_GET['page']) - 1;
}

if ($finalFolderName == "category") {
    echo "TODO: category list";
} else {
    $category = $finalFolderName;
    echoBlogPage($pageIndex, $category);
}
