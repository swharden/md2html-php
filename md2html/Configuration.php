<?php

/* These variables can optionally be used to modify the pages before they're served */
class Configuration
{
    public string $TEMPLATE_ADS =
    <<<EOD
        <!-- md2html ads -->
        <script data-ad-client='ca-pub-6687695838902989' async 
        src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
    EOD;

    public string $TEMPLATE_ANALYTICS =
    <<<EOD
        <!-- md2html analytics -->
        <script async src='https://www.googletagmanager.com/gtag/js?id=UA-560719-1'></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'UA-560719-1');
        </script>
    EOD;

    public string $TEMPLATE_NOINDEX =
    <<<EOD
        <!-- md2html indexing disabled -->
        <meta name="robots" content="noindex">
    EOD;
}
