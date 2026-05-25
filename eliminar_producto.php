<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: misproductos.php');
    exit();
}

include('conexion.php');

$id_producto = (int) ($_POST['id_producto'] ?? 0);
$id_prov = (int) $_SESSION['id'];

if ($id_producto <= 0) {
    header('Location: misproductos.php?error=1');
    exit();
}

$stmt = mysqli_prepare(
    $conexion,
    'SELECT imagen FROM producto WHERE id_producto = ? AND id_proveedor = ? LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'ii', $id_producto, $id_prov);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$fila = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$fila) {
    header('Location: misproductos.php?error=1');
    exit();
}

$imagen = trim((string) ($fila['imagen'] ?? ''));
if ($imagen !== '' && strncmp($imagen, 'imagenes/', 9) === 0 && strpos($imagen, '..') === false) {
    $ruta = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imagen);
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

$del = mysqli_prepare(
    $conexion,
    'DELETE FROM producto WHERE id_producto = ? AND id_proveedor = ?'
);
mysqli_stmt_bind_param($del, 'ii', $id_producto, $id_prov);
$ok = mysqli_stmt_execute($del);
mysqli_stmt_close($del);

header('Location: misproductos.php?' . ($ok ? 'ok=del' : 'error=1'));
exit;