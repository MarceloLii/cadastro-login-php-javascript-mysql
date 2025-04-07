<?php

namespace Controllers;

/**
 * Classe base Controller
 * Fornece métodos utilitários reutilizáveis em todo o sistema.
 */
class Controller
{
    /**
     * Método estático para escapar dados antes de exibi-los no HTML, prevenindo ataques XSS.
     *
     * @param string $dado Texto a ser tratado.
     * @return string Texto seguro para exibição em HTML.
     */
    public static function escaparHtml($dado)
    {
        return htmlspecialchars($dado, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Adiciona uma nova mensagem à sessão para exibição posterior.
     *
     * @param string $tipo Tipo da mensagem ('sucesso', 'erro', 'aviso', 'info').
     * @param string $mensagem Conteúdo da mensagem.
     */
    public static function adicionarMensagem($tipo, $mensagem)
    {
        // Garante que a sessão esteja ativa
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Adiciona a mensagem à variável de sessão
        $_SESSION['mensagens'][] = [
            'tipo' => $tipo,
            'mensagem' => self::escaparHtml($mensagem) // Evita XSS
        ];
    }

    /**
     * Exibe todas as mensagens armazenadas na sessão, aplicando classes CSS para estilização.
     * Após a exibição, as mensagens são removidas da sessão para evitar duplicação.
     */
    public static function exibirMensagens()
    {
        // Garante que a sessão esteja ativa antes de acessar $_SESSION
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Verifica se há mensagens para exibir
        if (!empty($_SESSION['mensagens'])) {
            foreach ($_SESSION['mensagens'] as $mensagem) {
                $tipo = $mensagem['tipo'];
                $conteudo = $mensagem['mensagem']; // Já escapado em adicionarMensagem()

                // Mapeia os tipos para classes CSS padronizadas
                $mapaClasses = [
                    'sucesso' => 'alert-success',
                    'erro'    => 'alert-danger',
                    'aviso'   => 'alert-warning',
                    'info'    => 'alert-info'
                ];

                // Define a classe CSS com base no tipo ou usa um padrão
                $classeMsg = $mapaClasses[$tipo] ?? 'alert-secondary';

                // Exibe a mensagem formatada
                echo "<div class='alert {$classeMsg}'>{$conteudo}</div>";
            }

            // Remove as mensagens da sessão após exibição
            unset($_SESSION['mensagens']);
        }
    }
}
