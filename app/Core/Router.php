<?php
/**
 * Multan Tech Accessories — Router Engine
 */

declare(strict_types=1);

namespace App\Core;

use ReflectionMethod;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function get(string $path, string|array|callable $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string|array|callable $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, string|array|callable $handler, array $middleware = []): self
    {
        // Normalize path
        $cleanPath = '/' . trim($path, '/');
        if ($cleanPath === '') {
            $cleanPath = '/';
        }

        // Convert {param} placeholders to named regex groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $cleanPath);
        $regex = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $cleanPath,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        // 1. Check Redirects Table (301 Permanent Redirects)
        try {
            $db = Database::getInstance();
            $redirect = $db->fetch("SELECT target_url, status_code FROM redirects WHERE source_url = ? LIMIT 1", [$path]);
            if ($redirect) {
                $db->execute("UPDATE redirects SET hits = hits + 1, last_hit_at = CURRENT_TIMESTAMP WHERE source_url = ?", [$path]);
                $resp = new Response();
                return $resp->redirect($redirect['target_url'], (int)$redirect['status_code']);
            }
        } catch (\Throwable) {}

        // 2. Match Defined Routes
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Run Middleware Stack
                foreach ($route['middleware'] as $mw) {
                    $mwInstance = new $mw();
                    $result = $mwInstance->handle($request);
                    if ($result instanceof Response) {
                        return $result;
                    }
                }

                return $this->executeHandler($route['handler'], $params, $request);
            }
        }

        // 3. 404 Not Found Handling & Logging
        try {
            $db = Database::getInstance();
            $ip = $request->ip();
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            $ua = $request->userAgent();
            $existing = $db->fetch("SELECT id FROM log_404 WHERE url = ? LIMIT 1", [$path]);
            if ($existing) {
                $db->execute("UPDATE log_404 SET hits = hits + 1, last_seen_at = CURRENT_TIMESTAMP WHERE id = ?", [$existing['id']]);
            } else {
                $db->execute("INSERT INTO log_404 (url, referrer, ip_address, user_agent) VALUES (?, ?, ?, ?)", [$path, $referrer, $ip, $ua]);
            }
        } catch (\Throwable) {}

        $resp = new Response();
        $resp->status(404);
        $resp->html(View::render('storefront/errors/404', [
            'meta_title' => 'Page Not Found — Multan Tech Accessories',
            'meta_description' => 'The requested page was not found.',
        ]), 404);
        return $resp;
    }

    private function executeHandler(string|array|callable $handler, array $params, Request $request): Response
    {
        if (is_callable($handler)) {
            $res = call_user_func_array($handler, array_values($params));
        } elseif (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $res = $this->callMethodWithParams($controller, $method, $params, $request);
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            if (!str_contains($class, '\\')) {
                $class = "App\\Controllers\\{$class}";
            }
            $controller = new $class();
            $res = $this->callMethodWithParams($controller, $method, $params, $request);
        } else {
            throw new \RuntimeException('Invalid route handler configuration');
        }

        if ($res instanceof Response) {
            return $res;
        }

        if (is_string($res)) {
            $resp = new Response();
            return $resp->html($res);
        }

        if (is_array($res)) {
            $resp = new Response();
            return $resp->json($res);
        }

        return new Response();
    }

    private function callMethodWithParams(object $controller, string $method, array $params, Request $request): mixed
    {
        $ref = new ReflectionMethod($controller, $method);
        $args = [];
        foreach ($ref->getParameters() as $param) {
            $name = $param->getName();
            if (isset($params[$name])) {
                $args[] = $params[$name];
            } elseif ($param->getType() && $param->getType()->getName() === Request::class) {
                $args[] = $request;
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }
        return $ref->invokeArgs($controller, $args);
    }
}
