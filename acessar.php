<?php
    $title="Acessar painel";
    include ("header.php");
?>
<body>
    <div class="login">
    <?php
        if(isset($_GET['erroemail'])) {
            echo '<div class="alert-false">Email incorreto. </div>';
        }
        if(isset($_GET['errosenha'])) {
            echo '<div class="alert-false">senha incorreta. </div>';
        }
    ?>
            <?php
                require_once './php/config.php';
                require_once './php/config.pdo.php';
                session_start();
                if (!isset($_SESSION['logado'])) {}
                    else {
                        $email=$_SESSION['nome'];
                            $sql = "SELECT id, nome, senha, funcao FROM usuarios WHERE email='$email'";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                // dados de saída de cada linha
                                while($row = mysqli_fetch_assoc($result)) {
                                $nome = $row['nome'];
                                }
                            }
                            echo '
                            <nav class="cad">
                                <ul>
                                    Logado como: <strong><u>' ?><?php echo $nome,'</u></strong>
                                    <li><a href="/painel.php">Acessar painel</a></li>
                                </ul>
                            </nav>
                            ';
                    }
            ?>
        <div class="box">
        <form action="./php/validar.php" method="post">

            <div class="row">

                <div class="cols-1">
                    <h1>Entrar </h1>
                    <div class="small">Por favor complete o formulário para entrar em sua conta. </div>
                </div>
                <div class="cols-1">
                    <label for="email" class="form-label">Endereço de Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="E-mail">
                </div>

                <div class="cols-1">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" id="senha" placeholder="senha">
                </div>

            </div>
            <div class='center'>
                    <input type="submit" value="Acessar" class="btn">
            </div>

        </form>

            <div class="boxbottom">
                <h6>ainda não tem conta? <a href="/cadastrar.php">cadastre-se</a></h6>
            </div>

        </div>
        
    </div>
</body>
</html>