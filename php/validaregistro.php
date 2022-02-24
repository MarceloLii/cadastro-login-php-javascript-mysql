<?php
require_once 'config.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$funcao = $_POST['funcao'];

if (preg_match('/[\'^£$%&*()}{@#?><>,|=_+¬-]/', $nome)){
    #Nome não existente
    ?>
    <script>
    javascript:alert('Seu nome tem caracteres invalidos!');
    javascript:window.location='../cadastrar.php';
    </script>
    <?php
    
}else{

    if (strlen($senha)>=8){
        //Todas etapas feitas

        //Verificar se a conta ja existe
        if ($consulta = $conn->prepare('SELECT id, senha FROM usuarios WHERE email = ?')) {
            $consulta->bind_param('s', $email);
            $consulta->execute();
            $consulta->store_result();
            
            
            if ($consulta->num_rows == 0) {

                //nao existe esse record, pode gravar
                $consulta->close();
                $consulta = $conn->prepare("INSERT INTO usuarios (nome, email, senha, funcao) values (?, ?, ?, ?)");
                $consulta->bind_param('ssss', $nome, $email, $senha, $funcao);
                $consulta->execute();
                $consulta->close();
                ?>
                <script>
                javascript:alert('Conta criada com sucesso!');
                javascript:window.location='../acessar.php';
                </script>
                <?php
                
            } else {
                //existe o record
                $consulta->close();
                ?>
                <script>
                javascript:alert('Ja existe esse usuario!');
                javascript:window.location='../cadastrar.php';
                </script>
                <?php
            }
        }

   }else{
        #Senha fraca
        ?>
        <script>
        javascript:alert('Campo senha não pode ser menor do que 8 caracteres!');
        javascript:window.location='../cadastrar.php?errosenha';
        </script>
        <?php
   }

}