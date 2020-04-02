# Markdown-to-HTML Converter for PHP
**[md2html.php](src/md2html.php) is a simple PHP script that converts markdown to HTML.** There are several markdown converters on the internet, but this one aims to be simpler and easier to modify than the rest.

### Features
* Automatic anchor links for headings
* Syntax highlighting of code blocks using [prettyprint](https://github.com/google/code-prettify)
* Styling achieved with an external CSS file (styled to resemble GitHub)
* Embed YouTube videos using `![](YouTubeURL)`
* Dynamically include a PHP files using `![](file2.php)`
* Dynamically include a markdown files using `![](file2.md)`

### Quickstart

```html
<html>
<head>
    <link rel="stylesheet" type="text/css" href="path/to/md2html.css">
</head>
<body>
	<?php
        require("path/to/md2html.php");
        includeMarkdown("demo.md");
	?>
</body>
</html>
```

### Markdown Webpage Server

The demo in [/demo/website](/demo/website) shows how to create a multi-page website built from markdown pages. 

A custom `.htaccess` tells Apache to direct requests for files like `demo.md.html` to `index.php`, which automatically finds the source markdown file `./pages/demo.md`, translates it to HTML, and serves the HTML wrapped in a customizable page template in the `./templates` folder.

### Download / Install

To install md2html just copy these files to your web server:

* [md2html.php](http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php)
* [md2html.css](http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css)

...or download them remotely using console commands:

##### Linux/MacOS
```bash
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php;
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css;
```

##### Windows
```batch
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php -OutFile md2html.php"
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css -OutFile md2html.css"
```

### Resources
* [Parsedown](https://github.com/erusev/parsedown) is a similar project that is more performant and has more features, but is a lot more complex and intimidating to modify.
* [GitHub Markdown Guide](https://guides.github.com/pdfs/markdown-cheatsheet-online.pdf)
* [Markdown Cheatsheet](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet) by Adam P
