</article>

<footer>
    <div id="footerBlock">
        This page was converted from
        <a href='<?php echo basename($filePath); ?>'><?php echo basename($filePath); ?></a>
        to HTML by
        <a href='https://github.com/swharden/md2html-php'>md2html</a>
        <!-- <?php echo $md2html->version; ?> -->
        in
        <?php echo number_format((microtime(true) - $benchmarkStartTime) * 1000, 2); ?>
        milliseconds.
    </div>
</footer>
</body>

</html>