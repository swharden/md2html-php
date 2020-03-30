<html>

<head>
    <title>md2html.php displaying test.md</title>
    <link rel="stylesheet" type="text/css" href="../src/md2html.css">
</head>

<body style="background-color: #f6f8fa; margin: 0px;">
    <div style="max-width: 900px; margin: auto; border: 1px solid #dfe2e5;">
        <?php
        require("../src/md2html.php");
        includeMarkdown("test.md");
        ?>
    </div>

    <div>
        link <a href='#'>outside</a> md2html with <em>emphasis</em> text
        renders normally (unaffected by md2html css)
    </div>
</body>

</html>