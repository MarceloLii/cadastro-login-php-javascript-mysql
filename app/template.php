<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($titulo_da_pagina); ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <aside>
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>

                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li><a href="/logout">Sair</a></li>
                        <li>Usuário: <?= htmlspecialchars($_SESSION['usuario']['name']) ?></li>
                    <?php else: ?>
                        <li><a href="/cadastro">Cadastro</a></li>
                        <li><a href="/login">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
    </aside>
    <article>
        <?php include $viewPath; ?>
    </article>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alert = document.querySelector(".alert");
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = "0";
                    alert.style.transition = "opacity 0.5s ease";

                    // remove do DOM após o fade-out
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 3000); // 3 segundos antes de começar a desaparecer
            }
        });
    </script>

</body>

</html>