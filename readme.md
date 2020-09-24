# Markdown-to-HTML Converter for PHP
**The md2html-php project makes it easy to create a small website from a collection of Markdown files.** There are several similar projects on the internet, but this one aims to be simpler and easier to modify than the rest.

## Features
* Automatic anchor links for headings
* Syntax highlighting in code blocks
* Page headers to customize title and date
* Automatic insertion of thumbnails
* Styled to resemble GitHub
* Add a table of contents using `![](TOC)`
* Embed YouTube videos using `![](YouTubeURL)`

## Installation
**Step 1:** Copy this folder to your website

(that's it)

## Develop with Docker

Run on http://localhost:8081

```
docker-compose up -d
```

## Philosophy
* If `index.md` exists in a folder it is converted to HTML and served
* If `index.html` or `index.php` exist, those files are served instead
* Folder names are URLs
* When `![]()` shows an image where `_thumb.jpg` exists, show the thumbnail but link to the original
* Sites can be backed-up and copied by zipping or unzipping them
* This system can be easily extended. For example [code for my blog](https://github.com/swharden/swharden.com) supports tags, pagination, and more.
* Static sites are possible with this system
  * Make your site static by generating `index.html` for every folder
  * Make your site dynamic again by deleting every index.html
