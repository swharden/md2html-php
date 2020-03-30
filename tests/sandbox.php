<?

function htmlFormat($line, $match = "***", $element = "em")
{
    $line = " " . $line;
    while (true) {
        $parts = explode(" " . $match, $line, 2);
        if (count($parts) == 1)
            return trim($line);
        $parts[0] = $parts[0] . " <$element>";
        $parts[1] = str_replace($match, "</$element>", $parts[1]);
        $line = $parts[0] . $parts[1];
    }
}

$line = "Text can be displayed using _italics_, *italics*, **bold**, ***emphasis***, or as `code`.";

$line = htmlFormat($line, "_", "i");
$line = htmlFormat($line, "`", "code");
$line = htmlFormat($line, "***", "em");
$line = htmlFormat($line, "**", "b");
$line = htmlFormat($line, "*", "i");
echo "$line";
