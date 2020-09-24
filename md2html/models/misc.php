<?php

function startsWith(string $string, string $startString)
{
    return (substr($string, 0, strlen($startString)) === $startString);
}

function endsWith(string $string, string $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}