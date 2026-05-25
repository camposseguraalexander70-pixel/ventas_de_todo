<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

include('conexion.php');

$id_prov = (int) $_SESSION['id'];
$id_producto = (int) ($_GET['id'] ?? $_POST['id_producto'] ?? 0);

function ensure_imagenes_dir(): string
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'imagenes';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_producto'] ?? '');
    $precio_raw = $_POST['precio'] ?? '';

    if ($id_producto <= 0 || $nombre === '' || $precio_raw === '') {
        header('Location: misproductos.php?error=1');
        exit();
    }

    if (!is_numeric($precio_raw)) {
        header('Location: editar_producto.php?id=' . $id_producto . '&error=1');
        exit();
    }

    $precio = (float) $precio_raw;

    $stmt = mysqli_prepare(
        $conexion,
        'SELECT imagen FROM producto WHERE id_producto = ? AND id_proveedor = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 'ii', $id_producto, $id_prov);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $actual = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$actual) {
        header('Location: misproductos.php?error=1');
        exit();
    }

    $imagen_actual = trim((string) ($actual['imagen'] ?? ''));

    $nueva_ruta_db = $imagen_actual;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['imagen']['tmp_name'];
        $original = basename($_FILES['imagen']['name']);
        $ext = pathinfo($original, PATHINFO_EXTENSION);
        $ext = $ext ? ('.' . strtolower($ext)) : '';
        $nombreArchivo = uniqid('prod_', true) . $ext;

        $dirFs = ensure_imagenes_dir();
        $destinoFs = $dirFs . DIRECTORY_SEPARATOR . $nombreArchivo;

        if (move_uploaded_file($tmp, $destinoFs)) {
            if ($imagen_actual !== '' && strncmp($imagen_actual, 'imagenes/', 9) === 0 && strpos($imagen_actual, '..') === false) {
                $viejo = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imagen_actual);
                if (is_file($viejo)) {
                    @unlink($viejo);
                }
            }
            $nueva_ruta_db = 'imagenes/' . $nombreArchivo;
        }
    }

    $upd = mysqli_prepare(
        $conexion,
        'UPDATE producto SET nombre_producto = ?, precio = ?, imagen = ? WHERE id_producto = ? AND id_proveedor = ?'
    );
    mysqli_stmt_bind_param($upd, 'sdsii', $nombre, $precio, $nueva_ruta_db, $id_producto, $id_prov);
    $ok = mysqli_stmt_execute($upd);
    mysqli_stmt_close($upd);

    header('Location: misproductos.php?' . ($ok ? 'ok=edit' : 'error=1'));
    exit();
}

if ($id_producto <= 0) {
    header('Location: misproductos.php');
    exit();
}

$stmt = mysqli_prepare(
    $conexion,
    'SELECT nombre_producto, precio, IFNULL(NULLIF(TRIM(imagen), \'\'), \'bolsa.svg\') AS imagen FROM producto WHERE id_producto = ? AND id_proveedor = ? LIMIT 1'
);
mysqli_stmt_bind_param($stmt, 'ii', $id_producto, $id_prov);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$prod = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$prod) {
    header('Location: misproductos.php?error=1');
    exit();
}

$nombre_val = htmlspecialchars($prod['nombre_producto'], ENT_QUOTES, 'UTF-8');
$precio_val = htmlspecialchars(number_format((float) $prod['precio'], 2, '.', ''), ENT_QUOTES, 'UTF-8');
$img_val = htmlspecialchars($prod['imagen'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
    <style>
        * { box-sizing: border-box; 
        margin: 0; 
        padding: 0; 
        font-family: system-ui, sans-serif; }

        body { background: #232323; 
        color: #e5e7eb; 
        min-height: 100vh; 
        padding: 1.5rem; }

        .wrap { max-width: 480px; 
        margin: 0 auto; 
        background: #fff; 
        color: #111827; 
        border-radius: 14px; 
        padding: 1.25rem 1.5rem 1.5rem; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.25); }

        h1 { font-size: 1.25rem; 
        margin-bottom: 1rem; }

        label { display: block; 
        font-size: 0.85rem; 
        font-weight: 600; 
        margin: 10px 0 6px; 
        color: #374151; }

        input[type="text"], input[type="number"] {
        width: 100%; 
        padding: 10px 12px; 
        border-radius: 10px; 
        border: 1px solid #d1d5db; 
        font-size: 1rem;
        }
        input[type="file"] { 
        width: 100%; 
        font-size: 0.9rem; }

        .preview { margin-top: 10px; 
        border-radius: 12px; overflow: 
        hidden; background: #f3f4f6; 
        aspect-ratio: 4/3; }

        .preview img { width: 100%; 
        height: 100%; 
        object-fit: cover; 
        display: block; }

        .acciones { margin-top: 1rem; 
        display: flex; 
        gap: 10px; 
        flex-wrap: wrap; }

        button[type="submit"] {
        flex: 1; 
        min-width: 140px; 
        padding: 10px; 
        border: none; 
        border-radius: 999px;
        background: #2563eb; 
        color: #fff; 
        font-weight: 700; 
        cursor: pointer;
        }

        button[type="submit"]:hover { 
        filter: brightness(1.05); }

        a.volver {
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        padding: 10px 16px; 
        border-radius: 999px; 
        background: #e5e7eb; 
        color: #111827;
        font-weight: 700; 
        text-decoration: none;
        }

        .err { background: #fee2e2; 
        color: #991b1b; 
        padding: 10px; 
        border-radius: 10px; 
        margin-bottom: 12px; 
        font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Editar publicación</h1>
        <?php if (isset($_GET['error'])): ?>
            <p class="err">Revisa los datos e intenta de nuevo.</p>
        <?php endif; ?>

        <form action="editar_producto.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">

            <label for="nombre_producto">Nombre del producto</label>
            <input id="nombre_producto" name="nombre_producto" type="text" value="<?php echo $nombre_val; ?>" required maxlength="100">

            <label for="precio">Precio</label>
            <input id="precio" name="precio" type="number" min="0" step="0.01" value="<?php echo $precio_val; ?>" required>

            <label for="imagen">Nueva imagen (opcional)</label>
            <input id="imagen" name="imagen" type="file" accept="image/*">

            <div class="preview">
                <img src="<?php echo $img_val; ?>" alt="Vista previa">
            </div>

            <div class="acciones">
                <button type="submit">Guardar cambios</button>
                <a class="volver" href="misproductos.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>