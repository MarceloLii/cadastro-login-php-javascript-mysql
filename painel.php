<?php
require_once './php/config.php';
require_once './php/config.pdo.php';
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
    $nome = $row['nome'];
    $qfuncao = $row['funcao'];
    }
}
?>
<?php
    $title="Painel";
    include ("header.php");
?>
<body>
                    <?php
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
                            echo "<div class='alert-what gradiente'>Quer deletar o usuario? ", $dnome; 
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
                Você estar em: <li><?php echo $title ?></li> . <li>inicio</li>
            </ul>
        </section>
        <!-- /nav -->
    <!-- box -->
    <div class="box">
        <h2><?php echo $title ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Função</th>
                    <?php if($qfuncao ==='administrador'){ ?>
                    <th>Ação</th>
                    <?php } ?>
                <tr>
            </thead>
            <tbody>
                <?php
                if($qfuncao === 'administrador') {
                    $usuarios = "SELECT id, nome, email, funcao FROM usuarios ORDER BY nome ASC";
                    $res_usuarios = $pdo->prepare($usuarios);
                    $res_usuarios->execute();
                        while($row_usuario = $res_usuarios->fetch(PDO::FETCH_ASSOC)){
                                extract($row_usuario);
                                    echo "
                                            <tr>
                                                <td>$nome</td>
                                                <td>$email</td>
                                                <td>$funcao</td>
                                                " ?>
                                                <form method='post' action='?deletar=<?php echo $id ?>'><td><button type='submit' name='deletar' class='deletar'>X</button><a href='editperfil.php?editar=<?php echo $id ?>' class='editar'><img class='editar' src="css/edit.svg"/></a></td></form>
                                            <?php echo "</tr>
                                        ";
                        }
                } else{
                        echo "
                                <tr>
                                    <td>$nome</td>
                                    <td>$email</td>
                                    <td>$qfuncao</td>
                                </tr>
                            ";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- /box -->
</div>
<!-- /container -->
<!-- creditos https://github.com/MarceloLii -->
</body>
</html>