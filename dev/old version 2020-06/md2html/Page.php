<?php

class Page
{
    public string $title = "NEED TITLE";
    public string $lowerNav = "";
    public string $content = "CONTENT NOT SET";
    public string $footer = "";
    public bool $allowAds = true;

    public function __toString()
    {
        $template_path = realpath(__DIR__ . "/templates");
        $template_url = str_replace($_SERVER['DOCUMENT_ROOT'], "", $template_path);
        $copyright = "<div>Copyright &#169; " . date("Y") . " Scott W Harden</div>";
        $ads = $this->allowAds ? '<script data-ad-client="ca-pub-6687695838902989" async ' .
            'src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' : "";

        $html = file_get_contents(__DIR__ . "/templates/main.html");
        $html = str_replace("{{ads}}", $ads, $html);
        $html = str_replace("{{templateUrl}}", $template_url, $html);
        $html = str_replace("{{title}}", $this->title, $html);
        $html = str_replace("{{content}}", $this->content, $html);
        $html = str_replace("{{footer}}", $copyright . $this->footer, $html);
        return $html;
    }
}
