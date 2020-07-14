<?php
    require_once 'Encryption.php';


    // Exemple : "eyJjaXBoZXJ0...ZXMiOjk5OX0="
    $encryptedString = "";

    // Exemple : "myPrivateKey"
    $encryptedKey = "";

    $encryption = new Encryption();

    var_dump($encryption->decrypt($encryptedString, $encryptedKey));