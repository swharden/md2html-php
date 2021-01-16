<?php

declare(strict_types=1);
error_reporting(E_ALL);

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0)
        return true;
    return (substr($string, -$len) === $endString);
}

function sanitizeLinkUrl($url): string
{
    $valid = "";
    foreach (str_split(strtolower(trim($url))) as $char)
        $valid .= (ctype_alnum($char)) ? $char : "-";
    while (strpos($valid, "--"))
        $valid = str_replace("--", "-", $valid);
    return trim($valid, '-');
}
