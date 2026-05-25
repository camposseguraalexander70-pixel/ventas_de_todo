<?php
    include('Conexion.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registro Vendedor</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 300px;
        }
        .input input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .error { color: red; text-align: center; }
        .success { color: green; text-align: center; }
    </style>
</head>
<body>
    <form action="" method="post">
        <div class="input">
            <input type="text" name="nombre" placeholder="Nombre de usuario" minlength="5" maxlength="20" required>
            <input type="email" name="correo_electronico" placeholder="Correo electronico" required>
            <input type="number" name="numero" placeholder="Numero de whatsapp" maxlength="15"required>
            <input type="text" name="salon" placeholder="Salon y grupo" minlength="3" maxlength="15" required>
            <input type="password" name="contrasena" placeholder="Contraseña" minlength="4" maxlength="10" required>
            <input type="number" name="matricula" placeholder="Matrícula" required>
        </div>
        <input type="submit" name="registro" value="Registrarse">
        <a href="index.php">Volver al inicio</a>
    </form>
</body>

<?php
    if(isset($_POST['registro'])){
        $nombre = $_POST['nombre'];
        $correo_electronico = $_POST['correo_electronico'];
        $numero = $_POST['numero'];
        $salon = $_POST['salon'];
        $contrasena = $_POST['contrasena'];
        $matricula = $_POST['matricula'];

        $hay_error = false;


        $verificar_nombre = "SELECT * FROM proveedor WHERE nombre = '$nombre'";
        $result_nombre = mysqli_query($enlace, $verificar_nombre);
        if(mysqli_num_rows($result_nombre) > 0){
            echo '<p class="error">El nombre de usuario ya está registrado</p>';
            $hay_error = true;
        }


        $verificar_correo = "SELECT * FROM proveedor WHERE correo_electronico = '$correo_electronico'";
        $result_correo = mysqli_query($enlace, $verificar_correo);
        if(mysqli_num_rows($result_correo) > 0){
            echo '<p class="error">El correo electrónico ya está registrado</p>';
            $hay_error = true;
        }


        $verificar_telefono = "SELECT * FROM proveedor WHERE numero = '$numero'";
        $result_telefono = mysqli_query($enlace, $verificar_telefono);
        if(mysqli_num_rows($result_telefono) > 0){
            echo '<p class="error">El número de teléfono ya está registrado</p>';
            $hay_error = true;
        }

        $verificar_matricula = "SELECT * FROM proveedor WHERE matricula = '$matricula'";
        $result_matricula = mysqli_query($enlace, $verificar_telefono);
        if(mysqli_num_rows($result_matricula) > 0){
            echo '<p class="error">La matricula ya está registrado</p>';
            $hay_error = true;
        }

        if(!$hay_error){
            $insertardatos = "INSERT INTO proveedor VALUES ('', '$nombre', '$matricula', '$numero', '$correo_electronico', '$salon', '$contrasena')";
            $ejecutarinsertar = mysqli_query($enlace, $insertardatos);

            if($ejecutarinsertar){
                echo '<p class="success">¡Registro exitoso! </p>';
            } else {
                echo '<p class="error">Error al registrar: ' . mysqli_error($enlace) . '</p>';
            }
        }
    }
?>