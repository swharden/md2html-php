<?php

require_once __DIR__ . "/misc.php";

class BlogPostHeader
{
    public string $title = "TITLE NOT SET";
    public array $tags = [];
    public bool $hasHeader = false;
    public int $headerLength = 0;
    public int $epochTime;

    function __construct($raw_text)
    {
        if (startsWith($raw_text, "---\n") == false) {
            return;
        }

        $header = substr($raw_text, 4);
        $header = substr($header, 0, strpos($header, "---"));
        $this->headerLength = strlen($header) + 4 + 4;

        $lines = explode("\n", $header);
        foreach ($lines as $line) {

            // this line is a tag
            if (startsWith($line, "  - ")) {
                $this->tags[] = trim(substr($line, 4));
                continue;
            }

            // this line is a single variable definition
            if (strpos($line, ":")) {
                $parts = explode(":", $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                if ($key == "date") {
                    $this->epochTime = strtotime($value);
                }

                if ($key == "title") {
                    $this->title = $value;
                }

                continue;
            }
        }
    }
}
