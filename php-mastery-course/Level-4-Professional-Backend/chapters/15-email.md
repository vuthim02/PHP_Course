# Chapter 15: Email Integration

## Learning Objectives

- Send emails with SMTP and APIs
- Build email templates
- Handle bounce and spam complaints
- Implement email queues

---

## 15.1 Email Service

```php
<?php
namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mailer;

    public function __construct(array $config = [])
    {
        $this->mailer = new PHPMailer(true);
        
        // SMTP configuration
        $this->mailer->isSMTP();
        $this->mailer->Host = $config['host'] ?? $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $config['username'] ?? $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $config['password'] ?? $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $config['port'] ?? 587;
        
        // Default sender
        $this->mailer->setFrom(
            $config['from_address'] ?? $_ENV['MAIL_FROM_ADDRESS'],
            $config['from_name'] ?? $_ENV['MAIL_FROM_NAME']
        );
    }

    public function send(
        string|array $to,
        string $subject,
        string $body,
        ?string $alternativeText = null
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Recipients
            if (is_string($to)) {
                $this->mailer->addAddress($to);
            } else {
                foreach ($to as $email => $name) {
                    $this->mailer->addAddress($email, $name);
                }
            }

            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);
            $this->mailer->AltBody = $alternativeText ?: strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            throw new MailException("Mail could not be sent: {$this->mailer->ErrorInfo}");
        }
    }

    public function sendTemplate(
        string|array $to,
        string $template,
        array $data = []
    ): bool {
        $body = $this->renderTemplate($template, $data);
        $subject = $data['subject'] ?? 'No Subject';
        return $this->send($to, $subject, $body);
    }

    private function renderTemplate(string $template, array $data): string
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../../views/emails/{$template}.php";
        return ob_get_clean();
    }
}
```

---

## 15.2 Exercises

1. Build an email service that supports multiple providers (SMTP, Mailgun, SendGrid)
2. Create HTML email templates with inline CSS
3. Implement email queue with retry for failed sends
4. Add webhook handling for bounce and complaint notifications

---

## Further Reading

- **Doc:** [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- **Doc:** [Mailgun API](https://documentation.mailgun.com/)
