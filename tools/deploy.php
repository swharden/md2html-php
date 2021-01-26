<?php

// point to a non-web-accessible folder
$apiKeyPath = '../../api.key';

if (file_exists($apiKeyPath) == false) {
    echo "ERROR: api.key does not exist";
    die();
}

// Get authorization header from Apache
$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    echo "ERROR: Authorization Required";
    die();
}

// Compare given token vs one on disk
$givenToken = substr($headers['Authorization'], 7);
$realToken = trim(file_get_contents($apiKeyPath));
if ($givenToken == $realToken) {
    system('../../deploy.sh');
} else {
    echo "ERROR: Authorization Failed";
}
