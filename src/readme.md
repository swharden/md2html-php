# Markdown-to-HTML Converter for PHP

**The md2html-php project makes it easy to create a small website from a collection of Markdown files.** There are several similar projects on the internet, but this one aims to be simpler and easier to modify than the rest.

### Example Websites
* [pyABF](https://swharden.com/pyabf)
* [ScottPlot](https://swharden.com/scottplot)

### How it Works

When a request like `page.md.html` comes in, `.htaccess` tells Apache to route the request to `md2html/server.php` which reads the markdown from `page.md`, converts it to HTML using [Parsedown](https://github.com/erusev/parsedown), then serves it in the `<article>` section of the `md2html/template.php` page.

### Features

Wile [Parsedown](https://github.com/erusev/parsedown) provides the majority of Markdown-to-HTML conversion, md2html steps in to provide a few advanced features including:

* Automatic anchor links for headings
* Syntax highlighting using [prettyprint](https://github.com/google/code-prettify)
* Styled to resemble GitHub using a [CSS file](templates/style.css)
* Include one markdown file in another with `![](file.md)`
* Dynamically include a PHP script using `![](file.php)`
* Add a table of contents using `![](TOC)`
* Embed YouTube videos using `![](YouTubeURL)`
* Define HTML title by making `<!--this-->` your first line
* Otherwise the title is the first heading in the markdown file

### Installation

* Copy the `/src` folder to your webserver and it will begin working immediately
* Add your own markdown files and request them with the `.md.html` extension
* Modify the page template by editing in `/md2html/template.php`