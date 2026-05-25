<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>INICIO DE SESION DEL USUARIO</title>
    <style>
        h1{
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="iniciarsesion.php" method="POST">
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } 
        ?>
        <hr>
        <h1>USUARIO</h1>
        <hr>
        <i class="fa-solid fa-user"></i>
        <label>USUARIO</label>
        <input type="text" name="nombre" placeholder="Nombre de usuario" required>
        
        <i class="fa-solid fa-unlock"></i>
        <label>Clave</label>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        
        <button type="submit">INICIAR SESION</button>
        <a href="crearcuenta.php">CREAR CUENTA</a>
    </form>
</body>
</html>