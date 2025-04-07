<table>
    <thead>
        <th>#</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Status</th>
        <th>Tipo de conta</th>
        <th>Ultimo Login</th>
        <th>Ação</th>
    </thead>
    <tbody>
        <?php if (!empty($usuarios)): ?>
            <?php foreach ($usuarios as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user->id); ?></td>
                    <td><?php echo htmlspecialchars($user->name); ?></td>
                    <td><?php echo htmlspecialchars($user->email ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($user->status); ?></td>
                    <td><?php echo htmlspecialchars($user->user_type ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($user->last_login))->format('d/m/Y')); ?></td>
                    <td>
                        <?php if (
                            isset($_SESSION['usuario']) &&
                            (
                                $_SESSION['usuario']['user_type'] === 'admin' ||
                                $_SESSION['usuario']['id'] == $user->id
                            )
                        ): ?>
                            <a href="/update/<?php echo htmlspecialchars($user->id); ?>">Editar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Nenhum Usuário encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>