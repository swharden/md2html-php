# Markdown Site with PHP

**This project is a flat-file website that dynamically serves Markdown files using PHP.** There are many options for managing website content, but I prefer this method for my personal websites because:
* **No build step:** unlike static site generators, HTML is generated dynamically from markdown files when pages are requested, so there is no need for a build step.
* **No database:** This is a flat-file site, so no database is required to store content.
* **Easy Installation:** Copy `wwwroot` to your website
* **Easy Backup:** Just zip the md2html folder
* **Easy Authoring:** Just create a folder with `index.md`
* **Clean URLs:** Page URLs are folder paths

### Features
* Automatic anchor links for headings
* Syntax highlighting in code blocks
* Add a table of contents using `![](TOC)`
* Embed YouTube videos using `![](YouTubeURL)`
* Template files are HTML and easy to customize
* Bootstrap manages layout so it looks good everywhere
* Markdown headings and tables are styled to resemble GitHub
* Dynamic generation of sitemap
* Dynamic generation of RSS feed
* Custom functionality is easy to add

### Example Websites
* https://swharden.com
* https://swharden.com/pyabf
* https://swharden.com/CsharpDataVis
* https://swharden.com/software/LJPcalc

## Developer Notes

### Installation

**Step 1:** Copy the `wwwroot` folder to your website 

(that's it)

### Develop in Docker

This repository has a ready-to-run demo site. 

Run `docker-compose up -d` and go to http://localhost:8081

### Markdown Header

Markdown files can have an optional header containing _front matter_ to customize what `{{mustache}}` text is replaced with in the template. Default replacements are defined in `settings.php`, and any values defined in the header override those defined in the settings file.

```
---
title: this text becomes the title element in the header
description: this text becomes header metadata for search engines to display
---

# My Markdown Article

The rest of the ***Markdown*** text goes here...
```

### Routing Requests

This repository uses `.htaccess` to tell Apache (with mod_rewrite) to route requests to a folder containing an `index.md` to `route.php`. If you don't use Apache or mod_rewrite, use whatever system you do have to route directory index requests similarly.