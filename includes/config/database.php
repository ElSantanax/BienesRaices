<?php

function conectarDB (): mysqli {

    $db = new mysqli('localhost', 'root', 'demon1307', 'bienesraices_crud');
    
    if (!$db) {
        echo "Error de conexión";
        exit;
    } 

    return $db;
}
