<?php

    $password = "test";
    $hash = password_hash ($password , PASSWORD_DEFAULT);

    if(password_verify ($password, $hash)){
        echo "c'est bon, clé : $hash";
    }
    else{
        echo 'non';
    }