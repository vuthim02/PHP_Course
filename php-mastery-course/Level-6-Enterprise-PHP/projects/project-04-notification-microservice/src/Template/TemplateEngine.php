<?php

declare(strict_types=1);

namespace NotificationService\Template;

/**
 * TemplateEngine
 *
 * Simple template engine for notification content.
 * Renders notification content from template strings + data variables.
 * Uses PHP's native string interpolation with a custom variable syntax.
 *
 * In production, use Twig or another template engine.
 */
final class TemplateEngine
{
    /** @var array<string, string> */
    private array $templates = [];

    public function __construct()
    {
        $this->loadDefaults();
    }

    /**
     * Register a custom template.
     */
    public function register(string $name, string $template): void
    {
        $this->templates[$name] = $template;
    }

    /**
     * Render a template with the given data.
     */
    public function render(string $name, array $data = []): string
    {
        $template = $this->templates[$name] ?? throw new \RuntimeException("Template '{$name}' not found");

        // Replace {{ variable }} placeholders
        $rendered = preg_replace_callback(
            '/\{\{(\s*)(\w+)(\s*)\}\}/',
            function (array $matches) use ($data) {
                $key = trim($matches[2]);
                return $data[$key] ?? $matches[0];
            },
            $template
        );

        // Replace {{#if condition}}...{{/if}} blocks
        $rendered = preg_replace_callback(
            '/\{\{#if (\w+)\}\}(.*?)\{\{\/if\}\}/s',
            function (array $matches) use ($data) {
                $key = $matches[1];
                return !empty($data[$key]) ? $matches[2] : '';
            },
            $rendered
        );

        // Replace {{#each items}}...{{/each}} blocks
        $rendered = preg_replace_callback(
            '/\{\{#each (\w+)\}\}(.*?)\{\{\/each\}\}/s',
            function (array $matches) use ($data) {
                $key = $matches[1];
                $block = $matches[2];
                $items = $data[$key] ?? [];

                if (!is_array($items)) {
                    return '';
                }

                $result = '';
                foreach ($items as $item) {
                    $row = $block;
                    foreach ($item as $k => $v) {
                        $row = str_replace("{{{$k}}}", (string) $v, $row);
                    }
                    $result .= $row;
                }
                return $result;
            },
            $rendered
        );

        return $rendered;
    }

    /**
     * Render subject line (extracts first line or {{ subject }}).
     */
    public function renderSubject(string $name, array $data = []): string
    {
        $full = $this->render($name, $data);
        $lines = explode("\n", $full);
        $subject = trim($lines[0] ?? '');
        return str_replace('Subject: ', '', $subject);
    }

    /**
     * Render body (everything after the subject line).
     */
    public function renderBody(string $name, array $data = []): string
    {
        $full = $this->render($name, $data);
        $lines = explode("\n", $full);
        array_shift($lines); // Remove subject line
        return trim(implode("\n", $lines));
    }

    /**
     * Load default notification templates.
     */
    private function loadDefaults(): void
    {
        $this->templates['welcome_email'] = <<<'TEMPLATE'
Subject: Welcome to {{ app_name }}, {{ name }}!

Hi {{ name }},

Welcome to {{ app_name }}! We're excited to have you on board.

{{#if referral_code}}
Your referral code: {{ referral_code }}
Share it with friends to earn rewards!
{{/if}}

Best regards,
The {{ app_name }} Team
TEMPLATE;

        $this->templates['password_reset'] = <<<'TEMPLATE'
Subject: Reset your password

Hi {{ name }},

You requested a password reset. Click the link below to reset your password:

{{ reset_link }}

This link expires in {{ expires_in }} minutes.

If you didn't request this, please ignore this email.

Best,
{{ app_name }} Security Team
TEMPLATE;

        $this->templates['order_confirmation'] = <<<'TEMPLATE'
Subject: Order Confirmed — #{{ order_id }}

Hi {{ name }},

Your order #{{ order_id }} has been confirmed.

Total: {{ total }}
Shipping to: {{ shipping_address }}

{{#each items}}
  - {{ product_name }} x {{ quantity }} — ${{ price }}
{{/each}}

Thank you for your purchase!
TEMPLATE;

        $this->templates['weekly_digest'] = <<<'TEMPLATE'
Subject: Your Weekly Digest

Hi {{ name }},

Here's your weekly activity summary for {{ app_name }}:

New followers: {{ new_followers }}
Messages received: {{ messages }}
Orders placed: {{ orders }}

{{#each top_posts}}
  • {{ title }} — {{ likes }} likes
{{/each}}

See you next week!
TEMPLATE;
    }
}
