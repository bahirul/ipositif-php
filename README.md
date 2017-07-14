IPOSITIF
=======

Internet postif domain list generator.

Web untuk generate domain blacklist atau whitelist.
Implementasi untuk [pi-hole](https://pi-hole.net/) dan bind9.

### Stuktur Direktori ###

-------------------

      base/               contains base and init class
      commands/           contains console commands
      config/             contains application configurations
      controllers/        contains Web controller classes
      data/               contains generated data
      public/             contains the entry script and Web resources
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application

### Fitur ###

 - Validasi domain otomatis
 - Blacklist dan Whitelist dari Kominfo Server
 - Bind RPZ Format (Upcoming)

### Instalasi ###

 1. git clone https://github.com/bahirul/ipositif.git
 2. composer install
 3. Setup web server
 4. Setup cron

### Konfigurasi apache2 ###

    <Virtualhost *:80>
    ServerName ipositif.domain
    DocumentRoot "/var/www/html/ipositif/public"
    <Directory "/var/www/html/ipositif/public">
        RewriteEngine on
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . index.php
    </Directory>

</Virtualhost>

### Konfigurasi cron ###

path-applikasi : root folder ipositif (contoh: /var/www/html/ipositif)
update cron kominfo list dieksekusi setiap hari senin jam 10 pagi.

    * 10 * * mon php path-applikasi/ipositif kominfo:fetch blacklist
    * 10 * * mon php path-applikasi/ipositif kominfo:fetch whitelist
    
### Url Download list ###

 1. kominfo blacklist : http://ipositif.domain/kominfo/blacklist
 2. kominfo whitelist : http://ipositif.domain/kominfo/whitelist

### Screenshot ###

![ipositif](http://i.imgur.com/kbovf3C.png)

### License ###

**ipositif** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.