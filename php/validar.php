<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['tentativas']) && $_SESSION['tentativas'] > 5 ){
	?>
	<script>
	javascript:alert('Bloqueado por muitas tentativas.');
	javascript:window.location='../index.php';
	</script>
	<?php

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
	// a última solicitação foi há mais de 30 minutos
	session_unset();     // desabilita a variável $_SESSION para o tempo de execução
	session_destroy();   // destrói os dados da sessão no armazenamento
	header('Location: index.php');
  }
  $_SESSION['LAST_ACTIVITY'] = time(); // atualiza o timestamp da última atividade
}else{
	if ($consulta = $conn->prepare('SELECT id, senha FROM usuarios WHERE email = ?')) {
		$consulta->bind_param('s', $_POST['email']);
		$consulta->execute();
		$consulta->store_result();
	
		if ($consulta->num_rows > 0) {
			$consulta->bind_result($id, $senha);
			$consulta->fetch();
	
			if ($_POST['senha'] === $senha) {
				session_regenerate_id();
				$_SESSION['logado'] = TRUE;
				$_SESSION['nome'] = $_POST['email'];
				$_SESSION['id'] = $id;
				?>
				<script>
				javascript:alert('Acesso liberado..., acessando painel');
				javascript:window.location='../painel.php';
				</script>
				<?php
	
				
			} else {
				if(!isset($_SESSION['tentativas'])){
					$_SESSION['tentativas']=0;
				}
				$_SESSION['tentativas']++;
				// Incorrect password
				?>
				<script>
				javascript:alert('Senha errada!');
				javascript:window.location='../acessar.php?errosenha';
				</script>
				<?php
			}
		} else {
			if(!isset($_SESSION['tentativas'])){
				$_SESSION['tentativas']=0;
			}
			$_SESSION['tentativas']++;
			// Incorrect email
			?>
			<script>
			javascript:alert('Email errado!');
			javascript:window.location='../acessar.php?erroemail';
			</script>
			<?php
		}
		$consulta->close();
		}	
}
?>

