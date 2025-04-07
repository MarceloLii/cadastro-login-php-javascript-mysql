<h1>Editar Usuário</h1>

<form action="/update/<?= htmlspecialchars($usuario->id) ?>" method="POST">
    <!-- Campo oculto para o ID do usuário -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($usuario->id) ?>">

    <!-- Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    
        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($usuario->name) ?>" required>
        
        <br>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario->email) ?>" required>
        
        <br>
        
        <label for="password">Nova Senha:</label>
        <input type="password" id="password" name="password" placeholder="Deixe em branco para manter a senha atual">
        
        <br>
        
        <label for="user_type">Tipo de Usuário:</label>
        <select name="user_type" id="user_type">
            <option value="user" <?= $usuario->user_type === 'user' ? 'selected' : '' ?>>Usuário</option>
            <option value="editor" <?= $usuario->user_type === 'editor' ? 'selected' : '' ?>>Editor</option>
            <option value="admin" <?= $usuario->user_type === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
        
        <br>
        
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="active" <?= $usuario->status === 'active' ? 'selected' : '' ?>>Ativo</option>
            <option value="inactive" <?= $usuario->status === 'inactive' ? 'selected' : '' ?>>Inativo</option>
            <option value="banned" <?= $usuario->status === 'banned' ? 'selected' : '' ?>>Banido</option>
        </select>
        
        <br>

    <button type="submit">Atualizar</button>
</form>
