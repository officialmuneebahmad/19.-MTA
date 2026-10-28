<?php
/**
 * Multan Tech Accessories — View Engine
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private static string $viewsDir = MTA_APP . '/Views';

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $templateFile = self::$viewsDir . '/' . ltrim($template, '/') . '.php';
        if (!file_exists($templateFile)) {
            throw new RuntimeException("View template not found: {$templateFile}");
        }

        // Extract variables to local scope
        extract($data, EXTR_SKIP);

        // Render template content
        ob_start();
        require $templateFile;
        $content = ob_get_clean();

        // If layout specified, wrap template content
        if ($layout !== null) {
            $layoutFile = self::$viewsDir . '/' . ltrim($layout, '/') . '.php';
            if (!file_exists($layoutFile)) {
                throw new RuntimeException("Layout template not found: {$layoutFile}");
            }
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }

    public static function partial(string $partial, array $data = []): string
    {
        return self::render('partials/' . ltrim($partial, '/'), $data, null);
    }
}
