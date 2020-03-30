<html>

<head>
    <title>md2html.php displaying demo.md</title>
    <link rel="stylesheet" type="text/css" href="../src/md2html.css">
</head>

<body>
    <article>
        <div class="content">
            <?php
            require("../src/md2html.php");
            includeMarkdown("demo.md");
            ?>
        </div>
    </article>
</body>

</html>