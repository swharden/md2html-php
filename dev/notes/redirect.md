### Redirect old paths to new folders

An old version of md2html-php used pages ending in `.md.html`.

To forward these old URLs to the new paths, add lines like this to `.htaccess`

```
# send old URLs to new one
RewriteRule ^(abf-file-format.md.html)$ /pyabf/abf2-file-format [R=301,NC,L]
RewriteRule ^(abf1-file-format.md.html)$ /pyabf/abf1-file-format [R=301,NC,L]
RewriteRule ^(abf2-file-format.md.html)$ /pyabf/abf2-file-format [R=301,NC,L]
RewriteRule ^(advanced.md.html)$ /pyabf/advanced [R=301,NC,L]
RewriteRule ^(tutorial.md.html)$ /pyabf/tutorial [R=301,NC,L]
RewriteRule ^(readme.md.html)$ /pyabf/ [R=301,NC,L]
```
