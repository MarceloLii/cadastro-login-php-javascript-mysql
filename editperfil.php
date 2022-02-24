<?php
require_once './php/config.pdo.php';
require_once './php/config.php';
session_start();
if (!isset($_SESSION['logado'])) {
	header('Location: index.php');	
    ?>
    <script>
    javascript:alert('Você não está logado! Faça login.');
    javascript:window.location='../acessar.php';
    </script>
    <?php
}
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
  // a última solicitação foi há mais de 30 minutos
  session_unset();     // desabilita a variável $_SESSION para o tempo de execução
  session_destroy();   // destrói os dados da sessão no armazenamento
  header('Location: index.php');
}
$_SESSION['LAST_ACTIVITY'] = time(); // atualiza o timestamp da última atividade

$email=$_SESSION['nome'];
$sql = "SELECT id, nome, senha, funcao FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // dados de saída de cada linha
    while($row = mysqli_fetch_assoc($result)) {
    $id = $row['id']; 
    $nome = $row['nome'];
    $qfuncao = $row['funcao'];
    $qsenha = $row['senha'];
    }
} else { 
}
?>
<?php
    $title="Perfil";
    include ("header.php");
?>
<!-- creditos https://github.com/MarceloLii -->
<body>
            <?php
                if(isset($_GET['erro'])) {
                    echo '<div class="alert-false">Erro ao tentar alterar</div>';
                }
                if(isset($_GET['errosenha'])) {
                    echo '<div class="alert-false">Digite uma senha maior ou igual a 8 caracteres</div>';
                }
                if(isset($_GET['sucesso'])) {
                    echo '<div class="alert-true">Alterado com sucesso</div>';
                }
                if(isset($_GET['erronome'])) {
                    echo '<div class="alert-false">Não e permitido usar caracteres especiais no nome</div>';
                }
                if(isset($_GET['senhanaoconfere'])) {
                    echo '<div class="alert-false">Senha anterior digitada não confere.</div>';
                }
            ?>
<!-- container -->
<div class="container">
        <!-- nav -->
        <?php
            include ('nav.php');
        ?>
        <section class='nav'>
            <ul>
                Você estar em: <li>Painel</li> . <li><?php echo $title ?></li>
            </ul>
        </section>
        <!-- /nav -->
    <!-- box -->
    <div class="box">
        <?php
            if(isset($_GET['editar'])){
            $id = (int) $_GET['editar']; 
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $sql->execute([$id]);
            $sql = $sql->fetch();
            $efuncao = $sql['funcao'];
        }
            if(isset($_POST['alterar'])){
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                $senhanterior = $_POST['senhanterior'];
                $senha = $_POST['senha'];
                $funcao = $_POST['funcao'];
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $nome)){ //mostra erro ao usar caracteres especiais
                    header('location: perfil.php?erronome');
                } elseif($senhanterior !== $qsenha) {
                    header('location: perfil.php?senhanaoconfere'); //erro ao autenticar senha anteriro
                }else {
                        if(strlen($senha)<= 8) {
                            header('location: perfil.php?errosenha'); //senha menor do que 8 caracteres
                        } elseif(strlen($senha)>= 8) {
                                    $editar = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ?, funcao = ? WHERE id = ?");
                                    $editar->execute([$nome, $email, $senha, $funcao, $id]);
                                    if($editar == true) {
                                        header('location: perfil.php?sucesso'); //alteracao feita com sucesso
                                    } else {
                                        header('location: perfil.php?erro'); //alteracao nao feita
                                    }
                                }
                die();
                }
            }
        ?>
        <h2>Usuario : <?php echo $sql['nome']; ?></h2>
            <script>
                function clickemail() {
                    javascript:alert('Email não editavel!');
                }
            </script>
        <form method="post">
            <input type="text" name="id" class="hide" id="id" placeholder="<?php echo $id ?>" readonly>

            <div class="cols-1">
                <label for="nome" class="form-label">Nome: <?php echo $sql['nome']; ?></label>
                <input type="text" name="nome" class="form-control" id="nome" value="<?php echo $sql['nome']; ?>" placeholder="<?php echo $sql['nome']; ?>">
            </div>

            <div class="cols-1">
                    <label for="funcao" class="form-label">Função: <?php echo $sql['funcao']; ?></label>
                    <select class="form-control" id="funcao" name="funcao">
                        <?php if($qfuncao === 'administrador'){ ?>
                        <option value="administrador"> Administrador </option>
                        <?php } ?>
                        <option value="visitante"> Visitante </option>
                    </select>
                </div>

            <div class="cols-1">
                <label for="email" class="form-label">Email: <?php echo $sql['email']; ?></label>
                <input type="email" name="email" onclick="clickemail()" class="form-control" id="email" value="<?php echo $sql['email']; ?>" placeholder="<?php echo $sql['email']; ?>" readonly/>
            </div>

            <div class="cols-1">
                <label for="password" class="form-label">Senha anterior</label>
                <input type="password" name="senhanterior" class="form-control" id="senhanterior">
            </div>

            <div class="cols-1">
                <label for="password" class="form-label">Nova Senha</label>
                <input type="password" name="senha" class="form-control" id="senha">
            </div>

        <div class='center'>
            <input type="submit" name="alterar" value="Alterar" class="btn">
        </div>

        </form>
    </div>
    <!-- /box -->
</div>
<!-- /container -->

</body>
<!-- creditos https://github.com/MarceloLii -->
</html>