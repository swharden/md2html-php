<html>

<head>
    <title>md2html.php displaying demo.md</title>
    <link rel="stylesheet" type="text/css" href="../src/style.css">
</head>

<body>
    <div class="content">&nbsp;
        <?php
        require("../src/md2html.php");
        includeMarkdown("test.md");
        ?>
    </div>
</body>

</html>