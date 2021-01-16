<?php

// this script serves the Nth page of blog posts (where N is defined by the URL)

require('../blogFunctions.php');
$finalFolderName = basename(strtok($_SERVER["REQUEST_URI"], '?'));
$pageIndex = intval($finalFolderName) - 1;
echoBlogPage($pageIndex);