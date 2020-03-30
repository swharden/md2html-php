<html>

<head>
    <title>md2html.php DEMO</title>
    <link rel="stylesheet" type="text/css" href="../src/md2html.css">
    <style>
        body {
            background-color: #f6f8fa;
            padding: 1px 2em 0em 2em;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji;
            font-size: 16;
            line-height: 150%;
            margin: 0px;
        }

        article {
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 0;
            margin-top: 0;
            border-left: 1px solid #dfe2e5;
            border-right: 1px solid #dfe2e5;
            border-bottom: 1px solid #dfe2e5;
            box-shadow: 0px 0px 50px rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body>
    <article>
        <?php
        require("../src/md2html.php");
        includeMarkdown("test.md");
        ?>
    </article>
</body>

</html>