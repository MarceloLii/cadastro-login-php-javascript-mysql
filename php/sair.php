<?php
session_start();
session_destroy();
// dados da sessão apagados
?>
<script>
	javascript:alert('você saiu da sua conta com sucesso!');//alerta sobre sessão
	javascript:window.location='../acessar.php';// saindo da sessão e redirecionado para pagina de login
</script>
