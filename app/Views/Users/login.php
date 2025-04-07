<h2>Login</h2>

<form method="POST" action="/login">
    <!-- Token CSRF (se estiver implementando proteção contra CSRF) -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" placeholder="Seu e-mail" required>

        <br>

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password" placeholder="Sua senha" required>


    <button type="submit">Entrar</button>
</form>

<div>
    <a href="/cadastro">Não tem cadastro? cadastrar-se</a>
</div>