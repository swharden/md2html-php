<html>

<head>
    <title>md2html.php displaying demo.md</title>
    <link rel="stylesheet" type="text/css" href="../src/md2html.css">
</head>

<body style="background-color: #f6f8fa; margin: 0px;">
    <div style="max-width: 900px; margin: auto; border: 1px solid #dfe2e5;">
        <?php
        require("../src/md2html.php");
        includeMarkdown("demo.md");
        ?>
    </div>
</body>

</html>