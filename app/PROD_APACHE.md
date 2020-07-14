```apacheconfig
define ROOT "/home/intracloud/intracloud/app"
define SITE "intracloud.loicnogier.fr"

Listen *:2727

<VirtualHost *:2727>
    ServerName      ${SITE}
    ServerAdmin     webmaster@email.fr

    DocumentRoot    "${ROOT}/dist"
    DirectoryIndex  /index.html


    <Directory "${ROOT}/dist">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted

        FallbackResource /index.html
    </Directory>
</VirtualHost>
```