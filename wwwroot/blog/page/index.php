<?php
require('../blogPage.php');

$finalFolderName = basename(strtok($_SERVER["REQUEST_URI"], '?'));
$pageIndex = intval($finalFolderName) - 1;
$pageIndex = max($pageIndex, 1);
echoBlogPage($pageIndex);