<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

include('conexion.php');

$nombre_usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');

$sql = "
    SELECT
        p.id_producto,
        p.nombre_producto,
        p.precio,
        IFNULL(NULLIF(TRIM(p.imagen), ''), 'bolsa.svg') AS imagen,
        pr.numero AS telefono_vendedor
    FROM producto p
    INNER JOIN proveedor pr ON pr.id_proveedor = p.id_proveedor
    ORDER BY p.id_producto DESC
";

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INICIO — Ventas de todo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --mint: #00ffc3;
            --mint-dark: #00d4a3;
            --bg: #141414;
            --bg-card: #1e1e1e;
            --surface: #ffffff;
            --text: #f4f4f5;
            --muted: #a1a1aa;
            --accent: #25d366;
            --shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
            --radius: 18px;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .bg-glow {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-glow span {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 14s ease-in-out infinite;
        }

        .bg-glow .g1 {
            width: 420px;
            height: 420px;
            background: var(--mint);
            top: -120px;
            left: -80px;
        }

        .bg-glow .g2 {
            width: 360px;
            height: 360px;
            background: #6366f1;
            bottom: 10%;
            right: -100px;
            animation-delay: -5s;
        }

        .bg-glow .g3 {
            width: 280px;
            height: 280px;
            background: #f472b6;
            top: 40%;
            left: 35%;
            animation-delay: -9s;
            opacity: 0.2;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -25px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(0, 255, 195, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 24px;
            max-width: 1280px;
            margin: 0 auto;
            gap: 16px;
            flex-wrap: wrap;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #111;
        }

        .nav-brand img {
            width: 48px;
            height: 48px;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-brand:hover img {
            transform: rotate(-8deg) scale(1.08);
        }

        .nav-brand span {
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a,
        .btn-perfil {
            position: relative;
            font-size: 0.95rem;
            text-decoration: none;
            color: #111;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 10px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.25s ease, transform 0.25s ease;
        }

        .nav-links a::after,
        .btn-perfil::after {
            content: '';
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 6px;
            height: 2px;
            background: #111;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.35s ease;
        }

        .nav-links a:hover,
        .btn-perfil:hover {
            background: rgba(0, 0, 0, 0.06);
        }

        .nav-links a:hover::after,
        .btn-perfil:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .perfil-wrap {
            position: relative;
        }

        .perfil-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 240px;
            background: var(--surface);
            color: #111;
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 16px;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.96);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
        }

        .perfil-menu.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .perfil-menu .usuario {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            word-break: break-word;
        }

        .perfil-menu .logout {
            display: block;
            text-align: center;
            padding: 10px 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .perfil-menu .logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(153, 27, 27, 0.2);
        }

        .hero {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 3.5rem 1.5rem 2.5rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(0, 255, 195, 0.15);
            border: 1px solid rgba(0, 255, 195, 0.35);
            color: var(--mint);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1rem;
            animation: fadeUp 0.7s ease-out 0.1s both;
        }

        .titulo-carrusel {
            font-size: clamp(2rem, 6vw, 3.2rem);
            font-weight: 800;
            line-height: 1.15;
            background: linear-gradient(135deg, #fff 0%, var(--mint) 50%, #a5f3fc 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: fadeUp 0.7s ease-out 0.2s both, shimmer 6s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .subtitulo {
            margin-top: 1rem;
            color: var(--muted);
            font-size: clamp(0.95rem, 2.5vw, 1.15rem);
            line-height: 1.6;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeUp 0.7s ease-out 0.35s both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .catalogo {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
            padding: 0 1.5rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.55s ease,
                        transform 0.45s cubic-bezier(0.34, 1.2, 0.64, 1),
                        box-shadow 0.45s ease,
                        border-color 0.45s ease;
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 28px 60px rgba(0, 255, 195, 0.12);
            border-color: rgba(0, 255, 195, 0.25);
        }

        .card-img-wrap {
            position: relative;
            aspect-ratio: 4 / 3;
            background: linear-gradient(145deg, #2a2a2a, #1a1a1a);
            overflow: hidden;
        }

        .card-img-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.5), transparent 50%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .card:hover .card-img-wrap::after {
            opacity: 1;
        }

        .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.55s cubic-bezier(0.34, 1.2, 0.64, 1);
        }

        .card:hover .card-img-wrap img {
            transform: scale(1.08);
        }

        .card-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.35;
            min-height: 2.8rem;
        }

        .card-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--mint);
            letter-spacing: -0.02em;
        }

        .btn-wa {
            margin-top: auto;
            align-self: flex-start;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 10px 16px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #25d366, #128c7e);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.35);
        }

        .btn-wa::before {
            content: '💬';
            font-size: 0.9rem;
        }

        .btn-wa:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 22px rgba(37, 211, 102, 0.45);
        }

        .btn-wa.disabled {
            background: #3f3f46;
            box-shadow: none;
            cursor: default;
        }

        .btn-wa.disabled::before {
            content: none;
        }

        .btn-wa.disabled:hover {
            transform: none;
        }

        .vacio {
            grid-column: 1 / -1;
            text-align: center;
            color: var(--muted);
            padding: 3rem 2rem;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius);
            border: 1px dashed rgba(255, 255, 255, 0.1);
            animation: fadeUp 0.6s ease-out both;
        }

        .fab-subir {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 40;
            text-decoration: none;
            animation: fabIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 0.5s both;
        }

        @keyframes fabIn {
            from {
                opacity: 0;
                transform: scale(0.5) rotate(-20deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0);
            }
        }

        .fab-subir .button {
            font-size: 1rem;
            font-weight: 700;
            position: relative;
            border: none;
            background: linear-gradient(135deg, var(--mint), var(--mint-dark));
            color: #111;
            padding: 14px 22px;
            border-radius: 999px;
            cursor: pointer;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(0, 255, 195, 0.4);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .fab-subir .button::before {
            content: '+';
            margin-right: 6px;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .fab-subir .button:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 14px 36px rgba(0, 255, 195, 0.5);
        }

        .fab-subir .pulse {
            position: absolute;
            inset: -4px;
            border-radius: 999px;
            border: 2px solid var(--mint);
            animation: pulse 2s ease-out infinite;
            pointer-events: none;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.6;
            }
            100% {
                transform: scale(1.35);
                opacity: 0;
            }
        }

        @media (max-width: 640px) {
            nav {
                justify-content: center;
            }

            .nav-links {
                justify-content: center;
                width: 100%;
            }

            .fab-subir {
                bottom: 20px;
                right: 20px;
                left: 20px;
            }

            .fab-subir .button {
                width: 100%;
                text-align: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .card {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>
<body>
    <div class="bg-glow" aria-hidden="true">
        <span class="g1"></span>
        <span class="g2"></span>
        <span class="g3"></span>
    </div>

    <header>
        <nav>
            <a href="inicio.php" class="nav-brand">
                <img src="bolsa.svg" alt="Ventas de todo">
                <span>VENTAS DE TODO</span>
            </a>
            <motion class="nav-links">
                <a href="inicio.php">INICIO</a>
                <a href="misproductos.php">MIS PRODUCTOS</a>
                <div class="perfil-wrap">
                    <button type="button" class="btn-perfil" id="btnPerfil" aria-expanded="false" aria-haspopup="true">PERFIL</button>
                    <div class="perfil-menu" id="menuPerfil" role="menu">
                        <p class="usuario"><?php echo $nombre_usuario; ?></p>
                        <a class="logout" href="cerrarsesion.php" role="menuitem">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <span class="hero-badge">CONALEP Veracruz II</span>
        <h1 class="titulo-carrusel">VENTAS DE TODO</h1>
        <p class="subtitulo">Página web realizada por alumnos del CONALEP para la comercialización de productos dentro del plantel.</p>
    </section>

    <main class="catalogo" id="catalogo">
        <?php
        if ($resultado && mysqli_num_rows($resultado) > 0):
            $i = 0;
            while ($row = mysqli_fetch_assoc($resultado)):
                $nombre = htmlspecialchars($row['nombre_producto'], ENT_QUOTES, 'UTF-8');
                $precio = htmlspecialchars(number_format((float) $row['precio'], 2, '.', ','), ENT_QUOTES, 'UTF-8');
                $img = htmlspecialchars($row['imagen'], ENT_QUOTES, 'UTF-8');

                $soloDigitos = preg_replace('/\D/', '', (string) $row['telefono_vendedor']);
                $wa = $soloDigitos !== '' ? 'https://wa.me/' . $soloDigitos : '#';
                $delay = min($i * 80, 400);
                $i++;
        ?>
                <article class="card" style="transition-delay: <?php echo $delay; ?>ms">
                    <div class="card-img-wrap">
                        <img src="<?php echo $img; ?>" alt="<?php echo $nombre; ?>" loading="lazy">
                    </div>
                    <motion class="card-body">
                        <h2 class="card-title"><?php echo $nombre; ?></h2>
                        <p class="card-price">$<?php echo $precio; ?></p>
                        <?php if ($soloDigitos !== ''): ?>
                            <a class="btn-wa" href="<?php echo htmlspecialchars($wa, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Contactar vendedor</a>
                        <?php else: ?>
                            <span class="btn-wa disabled">Sin teléfono</span>
                        <?php endif; ?>
                    </div>
                </article>
        <?php
            endwhile;
        else:
        ?>
            <p class="vacio">Aún no hay productos publicados. ¡Sé el primero en subir uno!</p>
        <?php endif; ?>
    </main>

    <a href="subir.php" class="fab-subir" title="Subir producto">
        <span class="pulse" aria-hidden="true"></span>
        <button type="button" class="button">Subir producto</button>
    </a>

    <script>
    (function () {
        var btn = document.getElementById('btnPerfil');
        var menu = document.getElementById('menuPerfil');
        if (btn && menu) {
            function cerrar() {
                menu.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var abierto = menu.classList.toggle('open');
                btn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            });

            document.addEventListener('click', cerrar);
            menu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        var cards = document.querySelectorAll('.card');
        if (!cards.length) return;

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            cards.forEach(function (card) {
                observer.observe(card);
            });
        } else {
            cards.forEach(function (card) {
                card.classList.add('visible');
            });
        }
    })();
    </script>
</body>
</html>