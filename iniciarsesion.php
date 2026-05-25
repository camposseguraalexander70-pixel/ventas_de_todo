<?php
    session_start();
    include('conexion.php');  

    if (isset($_POST['nombre']) && isset($_POST['contrasena'])) {
        $nombre = $_POST['nombre'];
        $contrasena = $_POST['contrasena'];

        if (empty($nombre)) {
            header("Location: index.php?error=El usuario es requerido");
            exit();
        } elseif (empty($contrasena)) {
            header("Location: index.php?error=La clave es requerida");
            exit();
        } else {
            
            $Sql = "SELECT * FROM proveedor WHERE nombre = '$nombre' AND contrasena = '$contrasena'";
            $result = mysqli_query($conexion, $Sql);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['id'] = $row['id_proveedor'];
                $_SESSION['tipo'] = 'proveedor';
                header("Location: inicio.php"); 
                exit();
            } else {
                header("Location: index.php?error=El usuario o la clave son incorrectas");
                exit();
            }
        }
    } else {
        header("Location: index.php");  
        exit();
    }
?>