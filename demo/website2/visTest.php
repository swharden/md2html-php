<html>

<head>
    <style>
        body {
        }

        article {
            background-color: white;
            margin: auto;
            max-width: 900px;
        }

        #md2html {
            background-color: white;
            font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji;
            font-size: 16;
            line-height: 150%;
            margin: 0px;
        }

        #md2html a {
            text-decoration: none;
            color: #0366d6;
        }

        #md2html a:hover {
            text-decoration: underline;
            opacity: 1 !important;
        }

        #md2html strong {
            font-weight: 600;
        }

        #md2html em {
            font-weight: 600;
        }


        #md2html h1 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: 2em;
            font-weight: 600;
            padding-bottom: .3em;
            border-bottom: 1px solid #eaecef;
        }

        #md2html h2 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: 1.5em;
            font-weight: 600;
            padding-bottom: .3em;
            border-bottom: 1px solid #eaecef;
        }

        #md2html h3 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: 1.25em;
            font-weight: 600;
        }

        #md2html h4 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: 1em;
            font-weight: 600;
        }

        #md2html h5 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: .875em;
            font-weight: 600;
        }

        #md2html h6 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-size: .85em;
            font-weight: 600;
            color: #6a737d;
        }

        .prettyprint {
            background: #f8f8f8;
            border-radius: 5px;
            border: 1px solid #ddd !important;
            font-size: 14px;
            line-height: 150%;
            overflow-x: auto;
        }
        
    </style>
    <script src='https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js'></script>
</head>

<body>
    <article>
        <?php
        include('md2html.php');
        md2html("ljpcalc.md");
        ?>
    </article>
</body>

</html>