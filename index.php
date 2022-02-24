<?php
    $title="Acessar painel";
    include ("header.php");
?>
<body>

    <div class="login">
        <div class="box">
        <form action="./php/validar.php" method="post">

            <div class="row">

                <div class="cols-1">
                    <h1>Entrar </h1>
                    <h6>Por favor complete o formulário para entrar em sua conta. </h6>
                </div>
                <div class="cols-1">
                    <label for="email" class="form-label">Endereço de Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="E-mail">
                </div>

                <div class="cols-1">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" id="senha" placeholder="senha">
                </div>

            </div>
            <div class='center'>
                    <input type="submit" value="Acessar" class="btn">
            </div>

        </form>

        </div>

    </div>

</body>
</html>