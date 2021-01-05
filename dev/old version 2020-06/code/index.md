---
title: Code
---

# Syntax Highlighting

```php
class MarkdownPage extends Page
{
    function __construct($markdown_file_path, $allowAds = false;)
    {
        $post = new BlogPost($markdown_file_path, false, false);
        $this->title = $post->title;
        $this->footer = "<a href='?source'>view source</a>";
        $this->allowAds = $allowAds;

        if (isset($_GET["source"])) {
            $codeHtml = htmlentities($post->markdown);
            $this->content = "<article></article>";
        } else {
            $this->content =  $post->html;
        }
    }
}
```