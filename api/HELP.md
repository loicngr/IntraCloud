# Run tests
- > composer tests

# GET JWT
- Request :
    https://127.0.0.1:8000/login_check
- Method :
    POST
- Parameters :
    - > {"email":"admin@email.fr","password":"password"}

# Other Help
### Start server
- > symfony server:start
- > php bin/console server:run
- > php -S localhost:3000 -t public

### Generate new Controller 
- > php bin/console make:controller BrandNewController

### See all Routes
- > php bin/console debug:router

### More documentation in Routes
- > https://symfony.com/doc/4.4/routing.html#creating-routes-as-annotations

### Create Database
- > php bin/console doctrine:database:create

### Create Entity
- > php bin/console make:entity

### Create Fixtures
- > php bin/console make:fixtures

### Prepare Migration
- > php bin/console make:migration
  
### Push Migration
- >  php bin/console doctrine:migrations:migrate

### Make Fixture
- > php bin/console doctrine:fixtures:load

### Generate the SSH keys:
    $ mkdir -p config/jwt
    $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

### Install certificat
- > sudo apt install libnss3-tools
- > symfony server:ca:install

### Autres liens utiles :
- https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html#association-mapping
- https://symfony.com/doc/current/security/form_login_setup.html
- https://symfony.com/doc/current/security.html
- https://github.com/lexik/LexikJWTAuthenticationBundle