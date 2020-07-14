## Setup Date Token (Reset Password)
- MAIL_RESET_PASS_MAX_TIME_PROCESS_HOUR=``1``
- MAIL_RESET_PASS_MAX_TIME_PROCESS_DAYS=``0``

## Adresse to VueJS server
- ADRESSE_UI="``http://localhost:8080``"

## Email
- Title Email
    - MAIL_RESET_PASS_SUBJECT=``"Mot de Passe PERDU"``
    - MAIL_ACCOUNT_CREATED_SUBJECT=``"Création d'un compte"``
- Email
    - MAILER_DSN="smtp://localhost:25"
    - MAILER_EMAIL="local@email.fr"

## Server
- OpenSSL
    - OPEN_SSL_KEY="``privateKey``"
    - OPEN_SSL_IV="``privateIv``"
    
- Encryption 
    - ENCRYPTION_KEY="``privateKey``"" (with no special char)