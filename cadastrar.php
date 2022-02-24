<?php
    $title = "Cadastrar-se";
    include ("header.php");
?>
<body>
            <?php
                if(isset($_GET['errosenha'])) {
                    echo '<div class="alert-false">senha menor do que 8 caracteres!</div>';
                }
            ?>
            
<!-- login -->
<div class="login">
    <!-- box -->
    <div class="box">
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
                        <nav>
                            <ul>
                                Logado como: <strong><u>' ?><?php echo $nome,'</u></strong>
                                <li><a href="/painel.php">Acessar painel</a></li>
                            </ul>
                        </nav>
                        ';
                }
            ?>
        <!-- form -->
        <form action="./php/validaregistro.php" method="post">
            
            <!-- row -->
            <div class="row">
                <div class="cols-1">
                    <h1>Cadastre-se </h1>
                    <div class="small">Por favor complete este formulário para criar a sua conta. </div>
                </div>

                <div class="cols-1">
                    <label for="email" class="form-label">Endereço de Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="@email" required>
                </div>

                <div class="cols-1">
                    <label for="nome" class="form-label">Nome completo</label>
                    <input type="nome" name="nome" class="form-control" id="nome" placeholder="Nome" required>
                </div>

                <div class="cols-1">
                    <label for="funcao" class="form-label">Função</label>
                    <select class="form-control" id="funcao" name="funcao">
                        <option value="visitante" select> Visitante </option>
                    </select>
                </div>

                <div class="cols-1">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" id="senha" placeholder="senha" required>
                </div>

            </div>
            <!-- /row -->
            <!-- registra -->
            <div class='center'>
                <input type="submit" value="Registre-se" class="btn">
            </div>
            <!-- /registra -->
        </form>
        <!-- /form -->
            <!-- boxbottom -->
            <div class="boxbottom">
                <h6>Já possui conta? faça <a href="/acessar.php">login</a></h6>
            </div>
            <!-- /boxbottom -->
    </div>
    <!-- /box -->
</div>
<!-- /login -->
</body>
</html>