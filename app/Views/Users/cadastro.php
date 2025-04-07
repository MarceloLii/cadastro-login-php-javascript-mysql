<form method="post" action="/cadastro">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="name">Nome de Usuário</label>
    <input type="text" id="name" name="name" placeholder="Nome" required>

    <br>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="exemplo@gmail.com" required>

    <br>

    <label for="senha">Senha</label>
    <input type="password" id="password" name="password" placeholder="Password" required>

    <br>

    <button type="submit">Cadastrar</button>
</form>