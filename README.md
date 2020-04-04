# Markdown-to-HTML Converter for PHP
**[md2html.php](src/md2html.php) is a simple PHP script that converts markdown to HTML.** There are several markdown converters on the internet, but this one aims to be simpler and easier to modify than the rest.

> ⚠️ **WARNING:** This project is being actively developed and is changing rapidly. \
\
My goal is for this project to become a folder that I can copy as needed to create themed websites from collections of markdown files.

### Features
* Automatic anchor links for headings
* Syntax highlighting of code blocks using [prettyprint](https://github.com/google/code-prettify)
* Styling achieved with an external CSS file (styled to resemble GitHub)
* Add a table of contents using `![](TOC)`
* Embed YouTube videos using `![](YouTubeURL)`
* Dynamically include PHP files using `![](file2.php)`
* Dynamically include markdown files using `![](file2.md)`

### Quickstart

```php
<html>
<head>
    <link rel="stylesheet" type="text/css" href="md2html.css">
</head>
<body>
<?php
    require("md2html.php");
    includeMarkdown("demo.md");
?>
</body>
</html>
```

### Automatic Conversion using .md.html URLs

An advanced demo in [/demo/website](/demo/website) shows how to create a customizable multi-page website built from markdown pages. Requests for `yourFile.md` return Markdown, but requests for `yourFile.md.html` return HTML-formatted content wrapped in a custom web page template.

### Download

To download md2html, simply copy these files to your web server:

* [md2html.php](http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php)
* [md2html.css](http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css)

Using the Linux console:

```bash
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php;
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css;
```

Using the Windows command prompt:

```batch
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php -OutFile md2html.php"
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css -OutFile md2html.css"
```

### What makes md2html so simple?
Code for this project is greatly simplified by making a few assumptions about the format of your markdown document, taking a few shortcuts, and intentionally not supporting obscure Markdown edge cases. This project favors simplicity and and easy hackability over strict adherence to the full Markdown standard.

### Resources
* [Parsedown](https://github.com/erusev/parsedown) is a similar project that more fully supports the Markdown standard, with a much more complex and intimidating code base.
* [GitHub Markdown Guide](https://guides.github.com/pdfs/markdown-cheatsheet-online.pdf)
* [Markdown Cheatsheet](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet) by Adam P
