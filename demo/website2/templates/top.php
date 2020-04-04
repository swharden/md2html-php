<?php $urlHere = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__)); ?>

<html>

<head>
	<link rel="stylesheet" type="text/css" href="<?php echo $urlHere; ?>/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $urlHere; ?>/../md2html/md2html.css">
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</head>

<body>

    <div id="pageAboveArticle">
        <div id="headerBlock">
            <div id="headerMenu">
                <a class='menuButton' href='#'>Home</a>
                <a class='menuButton' href='#'>Demo</a>
                <a class='menuButton' href='#'>Theory</a>
                <a class='menuButton' href='#'>Download</a>
                <a class='menuButton' href='#'>GitHub</a>
            </div>
            <div style="float: right; margin-top: 2em;">
                <a class="github-button" href="https://github.com/swharden/scottplot" data-size="large" data-show-count="true" aria-label="Star on GitHub" data-text="Star on GitHub"></a>
            </div>
            <div class='title'>LJPcalc</div>
            <div class='subtitle'>Liquid Junction Potential Calculator</div>
        </div>
        <!--<div id='lineAboveArticle'></div>-->
        <div id="pageUnderArticle"></div>
    </div>

    <article>