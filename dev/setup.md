# Setting up Apache/PHP on Windows 10

### Install Apache
* Install the [latest Visual C++ Redistributable](https://support.microsoft.com/en-us/help/2977003/the-latest-supported-visual-c-downloads)
* download latest [Apache Lounge Distro](https://www.apachelounge.com/download/)
* move the correct folder to `C:\Apache24\`
* disable the IPV6 protocol on the LAN network adapter
* test the server with `C:\Apache24\bin\httpd.exe`
  * this only runs the server while this command window is open
* Configure Apache as a Service
  * open a command prompt as administrator
  * `C:\Apache24\bin\httpd.exe -k install`
  * open windows services and ensure it is set to start automatically

### Install PHP
Download [64-bit thread-safe PHP](http://windows.php.net/download) and extract it to `C:\php\`. Edit `C:\Apache24\conf\httpd.conf` and add these lines at the bottom (you may have to change the .dll filename):
  
```
LoadModule php7_module "C:/php/php7apache2_4.dll"
AddHandler application/x-httpd-php .php
PHPIniDir "C:/php"
```
