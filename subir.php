<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicacion Nueva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            min-height: 100vh;
            background: #f3f4f6;
        }

        .card-form {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .section-title {
            font-size: 0.8rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        .btn-submit {
            border-radius: 0.75rem;
            font-weight: 600;
            padding: 0.65rem 1rem;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.25);
            filter: brightness(1.03);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            filter: brightness(0.98);
        }
    </style>
</head>
<body class="d-flex align-items-center py-5">
    <div class="container" style="max-width: 520px;">
        <div class="card card-form">
            <div class="card-body p-4 p-md-5">
                <h1 class="h4 mb-1">Nuevo producto</h1>
                <p class="text-muted small mb-4">Completa la información y sube solamente una imagen.</p>

                <form action="imagenes.php" method="post" enctype="multipart/form-data">
                    <p class="section-title mb-0">Datos del producto</p>
                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre</label>
                        <input id="nombre_producto" type="text" class="form-control" name="nombre_producto"
                            placeholder="Introduce el nombre de tu producto" required>
                    </div>
                    <div class="mb-4">
                        <label for="precio" class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="precio" type="number" class="form-control" name="precio" min="0" step="0.01"
                                inputmode="decimal" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="imagen" class="form-label">Archivo</label>
                        <input id="imagen" class="form-control" type="file" name="imagen"
                        accept="image/jpeg,image/png,.jpg,.jpeg,.png" required>
                        <p class="text-muted small mt-1">Solo JPG o PNG.</p>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-submit">SUBIR</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>