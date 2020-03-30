# Markdown-to-HTML Converter for PHP
**[md2html.php](src/md2html.php) is a simple PHP script that converts markdown to HTML.** There are several similar converters available online, but this project aims to be simpler and easier to customize than the rest. 

### Features
* Full support for tables
* Support for embedded YouTube videos
* Automatic anchor links for headings
* Syntax highlighting with automatic language detection
* Styled to look like GitHub using an external CSS file

### Example
Copy this repository to your web server and request [/demo/demo.php](/demo/demo.php)
 
### Installation

The easiest way to get md2html is to download it with a script. If you want to be fancy, have your web server automatically run this script periodically.

```batch
:: download md2html into the current directory
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php -OutFile md2html.php"
powershell -Command "Invoke-WebRequest http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css -OutFile md2html.css"
```

```bash
#!/bin/bash
# download md2html into the current directory
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.php;
wget http://raw.githubusercontent.com/swharden/md2html-php/master/src/md2html.css;
```

### Resources
* [Parsedown](https://github.com/erusev/parsedown) is a similar project that is more performant and has more features, but is a lot more complex and intimidating to modify.
* [GitHub Markdown Guide](https://guides.github.com/pdfs/markdown-cheatsheet-online.pdf)
* [Markdown Cheatsheet](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet) by Adam P