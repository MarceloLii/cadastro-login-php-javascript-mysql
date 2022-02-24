<?php
require_once './php/config.pdo.php';
require_once './php/config.php';
session_start();
if (!isset($_SESSION['logado'])) {
	header('Location: index.php');
    //creditos https://github.com/MarceloLii	
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
  //creditos https://github.com/MarceloLii
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
    $title="Meu Perfil";
    include ("header.php");
?>
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
                    echo '<div class="alert-false">Senha anterior digitada não confere!</div>';
                }
                if(isset($_GET['cancel'])) {
                    echo '<div class="alert-true">Exclusão cancelada!</div>';
                }
            ?>
                    <?php
                        if(isset($_POST['deletar'])){
                            $id = (int) $_GET['deletar']; 
                            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                            $sql->execute([$id]);
                            $sql = $sql->fetch();
                            $dnome = $sql['nome'];
                            //creditos https://github.com/MarceloLii
                            echo "<div class='alert-what gradiente'>Quer deletar sua conta? ", $dnome; 
                            echo "<form method='post'><button type='submit' name='deletarok'>sim</button><button type='submit' name='deletarcancel'>não</button></form>
                            </div>
                            ";
                        }
                    ?>
                    <?php if(isset($_POST['deletarok'])){ 
                            $id = (int) $_GET['deletar']; 
                            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                            $sql->execute([$id]);
                            $sql = $sql->fetch();
                            $delete = $pdo->query("DELETE FROM `usuarios` WHERE `usuarios`.`id` = $id");
                                if($delete == TRUE){
                                    echo "<div class='alert-what'>USUARIO DELETADO!</div>";
                                    ?>
                                    <script>
                                        setTimeout(function() {
                                            window.location.href = "/php/sair.php";
                                        }, 2000);
                                    </script>
                                    <?php
                                }
                            } 
                    ?>
                    <?php if(isset($_POST['deletarcancel'])){ 
                            header('Location: /painel.php?cancel');
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
        <h2><?php echo $title ?></h2>

            <input type="text" name="id" class="hide" id="id" placeholder="<?php echo $id ?>" readonly>

            <div class="cols-1">
                <label for="nome" class="form-label">Nome: <?php echo $nome ?></label>
            </div>

            <div class="cols-1">
                    <label for="funcao" class="form-label">Função: <?php echo $qfuncao ?></label>
            </div>

            <div class="cols-1">
                <label for="email" class="form-label">Email: <?php echo $email ?></label>
            </div>

            <div class="cols-1">
                <a href='editperfil.php?editar=<?php echo $id ?>' class='editar'><img class='editar' src="css/edit.svg"/> editar perfil</a>
            </div>

            <div class="cols-1">
            <form method='post' action='?deletar=<?php echo $id ?>'><button type='submit' name='deletar' class='deletar'>X EXCLUIR MINHA CONTA</button></form>
            </div>
    </div>
    <!-- /box -->
</div>
<!-- /container -->
<!-- creditos https://github.com/MarceloLii -->
</body>
</html>