<?php $siteRoot = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__))); ?>

<html>

<head>
    <title><?php echo ($md2html->title) ? $md2html->title : "md2html-php - " . basename($filePath); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/md2html/template.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/md2html/md2html.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src='https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js'></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</head>

<body>

    <div id="pageAboveArticle">
        <div id="headerBlock">
            <div id="headerMenu">
                <div class='menuButton'><a href='<?php echo $siteRoot; ?>/'>Home</a></div>
                <div class='menuButton'><a href='<?php echo $siteRoot; ?>/demo.md.html'>Demo</a></div>
                <div class='menuButton'><a href='<?php echo $siteRoot; ?>/folder'>Folder</a></div>
                <div class='menuButton'><a href='https://github.com/swharden/md2html-php'>GitHub</a></div>
            </div>
            <div id="githubButtonWrapper">
                <a class="github-button" href="https://github.com/swharden/md2html-php" data-size="large" data-show-count="true" aria-label="Star on GitHub" data-text="Star on GitHub"></a>
            </div>
            <div class='title'><a href='<?php echo $siteRoot; ?>'>md2html-PHP</a></div>
            <div class='subtitle'>A simple markdown-to-HTML converter using .md.html requests</div>
        </div>
        <div id="pageUnderArticle"></div>
    </div>

    <article>

        <?php echo $md2html->html; ?>

    </article>

    <footer>
        <div id="footerBlock">
            This page
            <!--<a href='<?php echo basename($reqFile); ?>'><?php echo basename($reqFile); ?></a>-->
            was converted from
            <a href='<?php echo basename($filePath); ?>'><?php echo basename($filePath); ?></a>
            to HTML by
            <a href='https://github.com/swharden/md2html-php'>md2html</a>
            <!-- <?php echo $md2html->version; ?> -->
            in
            <?php echo number_format($md2html->benchmarkMsec, 2); ?>
            milliseconds.
        </div>
    </footer>
</body>

</html>