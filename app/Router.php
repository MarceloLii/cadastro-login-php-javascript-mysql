<?php

namespace Core;

/**
 * Classe Router - Gerenciador de rotas para a aplicação.
 *
 * Esta classe permite a criação de rotas GET, POST, PUT e DELETE e
 * direciona requisições para os controladores apropriados.
 */
class Router
{
    private array $routes = [];

    /**
     * Define uma rota GET.
     */
    public function get(string $pattern, $controller): void
    {
        $this->addRoute('GET', $pattern, $controller);
    }

    /**
     * Define uma rota POST.
     */
    public function post(string $pattern, $controller): void
    {
        $this->addRoute('POST', $pattern, $controller);
    }

    /**
     * Define uma rota PUT.
     */
    public function put(string $pattern, $controller): void
    {
        $this->addRoute('PUT', $pattern, $controller);
    }

    /**
     * Define uma rota DELETE.
     */
    public function delete(string $pattern, $controller): void
    {
        $this->addRoute('DELETE', $pattern, $controller);
    }

    /**
     * Adiciona uma nova rota ao array de rotas.
     */
    private function addRoute(string $method, string $pattern, $controller): void
    {
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'controller' => $controller,
        ];
    }

    /**
     * Executa o roteamento com base na requisição atual.
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        // Verifica se há rotas definidas para o método HTTP solicitado
        if (!isset($this->routes[$method])) {
            $this->notFound();
            return;
        }

        // Percorre as rotas definidas e verifica se há correspondência
        foreach ($this->routes[$method] as $route) {
            $pattern = '#^' . $route['pattern'] . '$#';
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove o primeiro item que contém a URL completa
                $this->dispatch($route['controller'], $matches);
                return;
            }
        }

        // Caso nenhuma rota corresponda, exibe a página 404
        $this->notFound();
    }

    /**
     * Executa o controlador associado à rota correspondente.
     */
    private function dispatch($controller, array $params): void
    {
        try {
            if (is_callable($controller)) {
                $this->sendResponse(call_user_func_array($controller, $params));
            } elseif (is_string($controller)) {
                [$className, $method] = explode('@', $controller);
                $controllerName = "Controllers\\" . $className;
                
                if (!class_exists($controllerName)) {
                    throw new \Exception("Controlador '$controllerName' não encontrado.");
                }
                
                $controllerInstance = new $controllerName();
                if (!method_exists($controllerInstance, $method)) {
                    throw new \Exception("Método '$method' não encontrado no controlador '$controllerName'.");
                }
                
                $this->sendResponse(call_user_func_array([$controllerInstance, $method], $params));
            } else {
                throw new \Exception("Tipo de controlador inválido.");
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Envia a resposta ao cliente, formatando conforme o tipo de retorno.
     */
    private function sendResponse($result): void
    {
        if (is_array($result) || is_object($result)) {
            header('Content-Type: application/json');
            echo json_encode($result);
        } elseif (is_string($result)) {
            echo $result;
        }
    }

    /**
     * Exibe uma página de erro 404 personalizada.
     */
    private function notFound(): void
    {
        http_response_code(404);

        // Define o título da página
        $titulo_da_pagina = 'Error: 404';

        // Define o caminho da view
        $viewPath = 'app/Views/error.php';

        // Carrega a view dentro do template
        include 'app/template.php';
    }

    /**
     * Exibe uma resposta de erro com mensagem personalizada.
     */
    private function error(string $message, int $statusCode = 500): void
    {
        http_response_code($statusCode);

        // Define o título da página
        $titulo_da_pagina = 'Error: ' . $statusCode;

        // Define o caminho da view
        $viewPath = 'app/Views/error.php';

        // Carrega a view dentro do template
        include 'app/template.php';
    }
}
