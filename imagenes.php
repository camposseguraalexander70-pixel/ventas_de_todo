<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'proveedor') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: subir.php');
    exit;
}

$nombre_producto = trim($_POST['nombre_producto'] ?? '');
$precio = $_POST['precio'] ?? '';

if ($nombre_producto === '' || $precio === '' || !isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    header('Location: subir.php?error=datos');
    exit;
}

$id_proveedor = (int) $_SESSION['id'];

$dir = __DIR__ . DIRECTORY_SEPARATOR . 'imagenes';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$tmp = $_FILES['imagen']['tmp_name'];
$original = basename($_FILES['imagen']['name']);
$ext = pathinfo($original, PATHINFO_EXTENSION);
$ext = $ext ? ('.' . strtolower($ext)) : '';
$nombreArchivo = uniqid('prod_', true) . $ext;
$rutaFs = $dir . DIRECTORY_SEPARATOR . $nombreArchivo;
$rutaDb = 'imagenes/' . $nombreArchivo;

if (!move_uploaded_file($tmp, $rutaFs)) {
    header('Location: subir.php?error=subida');
    exit;
}

$stmt = mysqli_prepare(
    $conexion,
    'INSERT INTO producto (nombre_producto, precio, id_proveedor, imagen) VALUES (?, ?, ?, ?)'
);

if (!$stmt) {
    header('Location: subir.php?error=sql');
    exit;
}

mysqli_stmt_bind_param($stmt, 'sdis', $nombre_producto, $precio, $id_proveedor, $rutaDb);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: inicio.php?ok=1');
    exit;
}

mysqli_stmt_close($stmt);
header('Location: subir.php?error=bd');
exit;