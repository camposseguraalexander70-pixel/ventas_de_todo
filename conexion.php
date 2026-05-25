<?php
    $host = "localhost";
    $User = "root";
    $Pass = "";
    $db = "ventas_de_todo";

    $conexion = mysqli_connect($host, $User, $Pass, $db);

    if (!$conexion) {
        die("conexion fallida: " . mysqli_connect_error());
    }
    
    $enlace = $conexion;
?>