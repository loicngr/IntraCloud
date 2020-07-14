# Exemple de configuration Apache : 
```apacheconfig
define ROOT "/home/intracloud/intracloud/api"
define SITE "localhost"

Listen *:2728

<VirtualHost *:2728>
    DocumentRoot "${ROOT}/public"
    ServerName ${SITE}
    ServerAlias *.${SITE}
    DirectoryIndex /index.php

    <Directory "${ROOT}/public">
        AllowOverride All
        Require all granted
        Allow from All

        FallbackResource /index.php
    </Directory>

    <Directory "${ROOT}/public/bundles">
        FallbackResource disabled
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/intracloud_error.log
    CustomLog ${APACHE_LOG_DIR}/intracloud_error_access.log combined
</VirtualHost>
```