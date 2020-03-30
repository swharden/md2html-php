<html>

<head>
    <title>md2html.php displaying demo.md</title>
    <link rel="stylesheet" type="text/css" href="../src/md2html.css">
</head>

<body>
    <div class="content">
        <article>
            <?php
            require("../src/md2html.php");
            includeMarkdown("demo.md");
            ?>
        </article>
    </div>
</body>

</html>