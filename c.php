<?php
$conexion = mysqli_connect('localhost', 'root', '', 'ventas_de_todo');

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>