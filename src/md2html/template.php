<?php
$siteRoot = dirname(str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__)));
$config = parse_ini_file("config.ini", true);
?>

<html>

<head>
    <title><?php
            echo ($md2html->title) ?
                $md2html->title :
                $config['project']['title'] . " - " . basename($filePath);
            ?></title>

    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/md2html/template.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $siteRoot; ?>/md2html/md2html.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src='https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js'></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script async src='https://www.googletagmanager.com/gtag/js?id=<?php echo $config['googleAnalytics']['id']; ?>'></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '<?php echo $config['googleAnalytics']['id']; ?>');
    </script>
</head>

<body>

    <div id="backsplash" style="background-color: <?php echo $config['template']['backgroundColor']; ?>"></div>

    <div id="content">

        <div id="pageAboveArticle">
            <div id="headerBlock">
                <div id="headerMenu">
                    <?php
                    foreach ($config['buttons']['menu'] as $button) {
                        $buttonParts = explode(';', $button, 2);
                        $buttonLabel = $buttonParts[0];
                        $buttonUrl = $siteRoot . $buttonParts[1];
                        echo "<div class='menuButton'><a href='$buttonUrl'>$buttonLabel</a></div>";
                    }
                    ?>
                </div>
                <div class='title'><a href='<?php echo $siteRoot; ?>'><?php echo $config['project']['title']; ?></a></div>
                <div class='subtitle'><?php echo $config['project']['subtitle']; ?></div>
                <div id="githubButtonWrapper" style="visibility: <?php echo $config['githubButton']['visibility']; ?>;">
                    <a class="github-button" href="<?php echo $config['githubButton']['url']; ?>" data-size="large" data-show-count="<?php echo $config['githubButton']['showStars']; ?>" aria-label="<?php echo $config['githubButton']['text']; ?>" data-text="<?php echo $config['githubButton']['text']; ?>"></a>
                </div>
            </div>
        </div>

        <article>
            <?php echo $md2html->html; ?>
        </article>

        <footer>
            <div id="footerBlock">
                This page was converted from
                <a href='<?php echo basename($filePath); ?>.html?source'><?php echo basename($filePath); ?></a>
                to HTML by <a href='https://github.com/swharden/md2html-php'>md2html</a>
                in <?php echo number_format($md2html->benchmarkMsec, 2); ?> milliseconds.
            </div>
        </footer>

    </div>

</body>

</html>