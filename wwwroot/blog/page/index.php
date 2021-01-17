<?php
// redirect to the new query string URL format
$finalFolderName = basename(strtok($_SERVER["REQUEST_URI"], '?'));
header("Location: ../?page=$finalFolderName");
die();