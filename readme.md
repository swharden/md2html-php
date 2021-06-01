# Markdown Site with PHP

**This project is a flat-file website that dynamically serves Markdown files using PHP.** There are many options for managing website content, but I prefer this method for my personal websites because:
* **No build step:** unlike static site generators, HTML is generated dynamically from markdown files when pages are requested, so there is no need for a build step.
* **No database:** This is a flat-file site, so no database is required to store content.
* **Easy Installation:** Use git to clone this repo
* **Easy Backup:** Just zip the web folder
* **Easy Authoring:** Just create a folder with `index.md`
* **Clean URLs:** Page URLs are folder paths

### Features
* Automatic anchor links for headings
* Syntax highlighting in code blocks
* Add a table of contents using `![](TOC)`
* Embed YouTube videos using `![](YouTubeURL)`
* Template files are HTML and easy to customize
* Markdown headings and tables are styled to resemble GitHub
* ~~Dynamic generation of sitemap~~
* ~~Dynamic generation of RSS feed~~
* Custom functionality is easy to add

### Example Websites
* https://swharden.com
* https://swharden.com/pyabf
* https://swharden.com/CsharpDataVis
* https://swharden.com/software/LJPcalc

## Developer Notes

### Installation

**Step 1:** Use `git` to clone this repo _outside_ your web folder. I suggest `/var/www/md2html`

**Step 2:** Link the resources folder to a web-accessible URL

```
ln -ls /var/www/md2html/resources /var/www/html/md2html-resources
```

**Step 3:** Copy the quickstart demo folder to your web folder and your site will be live!

**Step 4:** To create new pages, create sub-folders with an `index.md`

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

This repository uses `.htaccess` to tell Apache (with mod_rewrite) to route requests to a folder containing an `index.md` to a local PHP script to handle them. If you don't use Apache or mod_rewrite, use whatever system you do have to route directory index requests similarly.

### Continuous Deployment with GitHub Actions

A flat-file website can be cloned onto a webserver using `git`, then PHP can execute `git pull` to update the content. By configuring GitHub Actions to make a HTTP request that executes the PHP update script every time new commits are pushed to the repository, it is possible to keep a website continuously and automatically in sync with a GitHub repository.

* Clone this repo in a folder outside the web root
* Symbolically link `wwwroot` to a web-accessible path
* Create a secret `API_KEY` in the GitHub project
* Store the key in `api.key` in the root folder (chmod `400`)
* Configure GitHub Actions to HTTP request [`deploy.php`](tools/deploy.php) when new commits are pushed, using the `API_KEY` as a bearer token (see [deploy.yml](tools/deploy.yml))

## Alternative Pure-Python Static Site Generator

After using this PHP-based system for several months I grew to enjoy it for large sites with 100s of pages, but for very small sites with just a few pages it felt cumbersome to fire-up a Docker instance just to do a little page editing.

I created [Palila](https://github.com/swharden/Palila) to meet this need - a small Python script to convert index.md to index.html that can be run locally (when editing) or remotely (when deploying).
