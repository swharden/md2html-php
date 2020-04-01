# Markdown-to-HTML Converter for PHP
**[md2html.php](src/md2html.php) is a simple PHP script that converts markdown to HTML.** There are several similar converters available online, but this project aims to be simpler and easier to customize than the rest. 

### Features
* Support for embedded YouTube videos
* Automatic anchor links for headings
* Syntax highlighting with automatic language detection
* Styled to look like GitHub using an external CSS file
* Dynamically include one file in another using `![](file2.md)`

### Demo
Copy this repository to your web server and request [/demo/demo.php](/demo/demo.php)
 
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
