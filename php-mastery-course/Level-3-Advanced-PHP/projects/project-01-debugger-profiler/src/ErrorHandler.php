<?php

declare(strict_types=1);

namespace DebuggerProfiler;

class ErrorHandler
{
    private static ?ErrorHandler $instance = null;
    private bool $registered = false;
    private array $exceptions = [];

    final public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    final protected function __construct() {}

    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        set_error_handler(function (
            int $severity,
            string $message,
            string $file,
            int $line
        ): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(function (\Throwable $e): void {
            $this->exceptions[] = $e;
            $this->renderDebugPage($e);
        });

        register_shutdown_function(function (): void {
            $error = error_get_last();
            if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
                $this->renderDebugPage(
                    new \ErrorException(
                        $error['message'],
                        0,
                        $error['type'],
                        $error['file'],
                        $error['line']
                    )
                );
            }
        });

        $this->registered = true;
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    private function renderDebugPage(\Throwable $e): void
    {
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $renderer = new Renderer\HtmlRenderer();
        echo $renderer->renderError($e);

        if (defined('PHPUNIT_COMPOSER_INSTALL') === false) {
            exit(1);
        }
    }

    private function __clone(): void {}
    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }
}
