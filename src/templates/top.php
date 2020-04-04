<?php $siteRoot = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__))); ?>

<html>

<head>
    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/templates/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/md2html/md2html.css">
    <script src='https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js'></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</head>

<body>

    <div id="pageAboveArticle">
        <div id="headerBlock">
            <div id="headerMenu">
                <a class='menuButton' href='<?php echo $siteRoot; ?>/'>Home</a>
                <a class='menuButton' href='<?php echo $siteRoot; ?>/demo.md.html'>Demo</a>
                <a class='menuButton' href='<?php echo $siteRoot; ?>/folder'>Folder</a>
                <a class='menuButton' href='https://github.com/swharden/md2html-php'>GitHub</a>
            </div>
            <div style="float: right; margin-top: 2.2em;">
                <a class="github-button" href="https://github.com/swharden/md2html-php" data-size="large" data-show-count="true" aria-label="Star on GitHub" data-text="Star on GitHub"></a>
            </div>
            <div class='title'>md2html-PHP</div>
            <div class='subtitle'>A simple markdown-to-HTML converter using .md.html requests</div>
        </div>
        <div id="pageUnderArticle"></div>
    </div>

    <article>