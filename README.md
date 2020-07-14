# IntraCloud project

A secure and fast way to upload and downlaod files on servers.

# Demo

![](https://i.imgur.com/Abpa6H1.gif)

#### V2 design

![v2](https://i.imgur.com/3Qn1HJR.gif)

[WebSocket Exemple](https://drive.google.com/uc?id=1LwU1K2x5RlzlT3Lt1xHuuotdtZdvkpeo)

# Requirement

### Prod server

- Php : `7.4.4` (min)
- Mysql : `v5.7` (min)
- Node : `v12` (min)
- `Zip` and `tree` package installed
- `@vue/cli` npm package installed
- A Dependency Manager for PHP `composer` installed
- Open `2727` port for VueJS
- Open `2728` port for Symfony API
- Open `2729` port for WebSocket
- Open `25` port for Email

### Servers that will be added to the interface

- `Zip` and `tree` package installed

#### You need to have a `/tmp` folder

#### Architecture exemple :

    - /tmp
    - /intracloud
        - /api
        - /app

#### You have a bash script for install and setup basics requirement (`setup_prod.sh`)

##### This script makes the `/tmp` folder, make a Vue dist version and install Symfony dependencies for you.
