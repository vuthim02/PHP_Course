# Chapter 22: Localization and Internationalization (i18n)

## Learning Objectives

By the end of this chapter you will:
- Understand the difference between i18n and l10n
- Implement multi-language support in PHP applications
- Use gettext for string translation
- Handle locale-specific formatting (dates, numbers, currency)
- Build a locale-switching middleware
- Store and retrieve translated content from databases

---

## 22.1 i18n vs l10n

**Internationalization (i18n)** — Designing your app so it can be adapted to different languages without engineering changes.

**Localization (l10n)** — The actual adaptation: translating text, formatting dates, displaying correct currencies.

Think of it like building a house:
- **i18n** = Building with standard-sized doors and windows so any door/window can fit
- **l10n** = Choosing the actual door style, window blinds, and paint colors for a specific region

---

## 22.2 Locale Basics

A locale is a combination of language and region:

```text
en_US → English as spoken in the United States
en_GB → English as spoken in the United Kingdom
fr_FR → French as spoken in France
fr_CA → French as spoken in Canada
de_DE → German as spoken in Germany
zh_CN → Chinese as spoken in China
ar_SA → Arabic as spoken in Saudi Arabia
```

### Setting the Locale in PHP

```php
<?php
// Set locale for the current process
setlocale(LC_ALL, 'en_US.UTF-8', 'en_US.utf8', 'en_US');

// Set specific categories
setlocale(LC_TIME, 'de_DE.UTF-8');   // Date/time formatting
setlocale(LC_MONETARY, 'fr_FR.UTF-8'); // Currency formatting
setlocale(LC_NUMERIC, 'en_US.UTF-8'); // Number formatting

// Check available locales
$locales = array_filter([
    'en_US.UTF-8', 'fr_FR.UTF-8', 'de_DE.UTF-8',
    'es_ES.UTF-8', 'ja_JP.UTF-8',
], fn($locale) => setlocale(LC_ALL, $locale) !== false);
```

### Detecting User Locale

```php
<?php
class LocaleDetector
{
    public function fromBrowser(): string
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en-US';

        // Parse Accept-Language header
        // Example: "fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7"
        $locales = [];
        foreach (explode(',', $acceptLanguage) as $part) {
            $parts = explode(';q=', trim($part));
            $locale = str_replace('-', '_', $parts[0]);
            $quality = (float) ($parts[1] ?? 1.0);
            $locales[$locale] = $quality;
        }

        arsort($locales);

        $supported = ['en_US', 'fr_FR', 'de_DE', 'es_ES', 'ja_JP'];

        foreach ($locales as $locale => $quality) {
            // Try exact match first
            if (in_array($locale, $supported)) {
                return $locale;
            }

            // Try language-only match
            $lang = substr($locale, 0, 2);
            foreach ($supported as $supportedLocale) {
                if (str_starts_with($supportedLocale, $lang)) {
                    return $supportedLocale;
                }
            }
        }

        return 'en_US'; // Default fallback
    }

    public function fromUrl(): ?string
    {
        // URL-based: example.com/fr/page or fr.example.com/page
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $path)));

        $supported = ['en', 'fr', 'de', 'es', 'ja'];
        if (!empty($segments) && in_array($segments[0], $supported)) {
            return $segments[0];
        }

        return null;
    }

    public function fromSession(): ?string
    {
        return $_SESSION['locale'] ?? null;
    }

    public function fromCookie(): ?string
    {
        return $_COOKIE['locale'] ?? null;
    }

    public function detect(): string
    {
        // Priority: URL > Session > Cookie > Browser > Default
        return $this->fromUrl()
            ?? $this->fromSession()
            ?? $this->fromCookie()
            ?? $this->fromBrowser();
    }
}
```

---

## 22.3 Locale Middleware

```php
<?php
class LocaleMiddleware
{
    private LocaleDetector $detector;

    public function __construct()
    {
        $this->detector = new LocaleDetector();
    }

    public function handle(callable $next): void
    {
        $locale = $this->detector->detect();

        // Store in session
        $_SESSION['locale'] = $locale;

        // Set PHP locale
        setlocale(LC_ALL, "{$locale}.UTF-8", $locale);

        // Set gettext locale
        putenv("LANG={$locale}.UTF-8");
        putenv("LANGUAGE={$locale}.UTF-8");
        setlocale(LC_MESSAGES, "{$locale}.UTF-8");

        // Make locale available to views
        View::share('locale', $locale);

        $next();
    }
}
```

---

## 22.4 String Translation with gettext

gettext is the industry standard for PHP translation. It uses `.po` (Portable Object) and `.mo` (Machine Object) files.

### Setup

```php
<?php
// 1. Configure gettext
$locale = 'fr_FR';
$domain = 'messages';

putenv("LANG={$locale}.UTF-8");
setlocale(LC_ALL, "{$locale}.UTF-8");

// Path to your translation files
bindtextdomain($domain, '/var/www/locale');
textdomain($domain);

// For UTF-8 support
bind_textdomain_codeset($domain, 'UTF-8');
```

### Directory Structure

```text
locale/
├── en_US/
│   └── LC_MESSAGES/
│       └── messages.po
│       └── messages.mo
├── fr_FR/
│   └── LC_MESSAGES/
│       └── messages.po
│       └── messages.mo
└── de_DE/
    └── LC_MESSAGES/
        └── messages.po
        └── messages.mo
```

### Using gettext in PHP

```php
<?php
// Basic translation
echo _("Welcome to our application");
echo gettext("Welcome to our application");

// With variables (use sprintf)
echo sprintf(_("Hello, %s!"), $username);

// Plural forms
echo ngettext(
    "%d comment",      // Singular
    "%d comments",     // Plural
    $commentCount      // Number to determine singular/plural
);

// Context (disambiguate same word in different contexts)
echo pgettext("button", "Save");     // Button label
echo pgettext("file", "Save");       // Save file action
```

### Creating Translation Files

```bash
# Extract translatable strings from PHP files
xgettext --language=PHP \
         --from-code=UTF-8 \
         --output=locale/fr_FR/LC_MESSAGES/messages.po \
         --keyword=_ \
         --keyword=ngettext:1,2 \
         --keyword=pgettext:1c,2 \
         src/

# Create .mo binary from .po (done after translation)
msgfmt locale/fr_FR/LC_MESSAGES/messages.po \
       --output-file=locale/fr_FR/LC_MESSAGES/messages.mo
```

### Example .po File

```po
msgid ""
msgstr ""
"Project-Id-Version: MyApp 1.0\n"
"Language: fr_FR\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Plural-Forms: nplurals=2; plural=(n > 1);\n"

msgid "Welcome to our application"
msgstr "Bienvenue sur notre application"

msgid "Hello, %s!"
msgstr "Bonjour, %s !"

msgid "%d comment"
msgid_plural "%d comments"
msgstr[0] "%d commentaire"
msgstr[1] "%d commentaires"

msgctxt "button"
msgid "Save"
msgstr "Enregistrer"

msgctxt "file"
msgid "Save"
msgstr "Sauvegarder"
```

---

## 22.5 Alternative: Array-Based Translation

For simpler apps without gettext:

```php
<?php
class Translator
{
    private array $translations = [];
    private string $locale;

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function trans(string $key, array $params = []): string
    {
        $message = $this->translations[$key] ?? $key;

        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        return $message;
    }

    public function transChoice(string $key, int $count, array $params = []): string
    {
        $messages = $this->translations[$key] ?? [$key, $key];
        $index = $count === 1 ? 0 : 1;

        $message = $messages[$index] ?? $messages[0];

        $params[':count'] = $count;
        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        return $message;
    }

    private function loadTranslations(): void
    {
        $path = __DIR__ . "/translations/{$this->locale}.php";
        if (file_exists($path)) {
            $this->translations = require $path;
        }
    }
}

// Translation file: translations/fr.php
<?php
return [
    'welcome' => 'Bienvenue sur notre application',
    'hello' => 'Bonjour, :name!',
    'comments' => [':count commentaire', ':count commentaires'],
    'greeting' => 'Bonjour',
    'farewell' => 'Au revoir',
];

// Usage
$t = new Translator('fr');
echo $t->trans('welcome');                    // Bienvenue sur notre application
echo $t->trans('hello', ['name' => 'Alice']); // Bonjour, Alice!
echo $t->transChoice('comments', 1);          // 1 commentaire
echo $t->transChoice('comments', 5);          // 5 commentaires
```

---

## 22.6 Formatting Dates, Numbers, and Currency

```php
<?php
class LocaleFormatter
{
    private string $locale;

    public function __construct(string $locale = 'en_US')
    {
        $this->locale = $locale;
        setlocale(LC_ALL, "{$locale}.UTF-8", $locale);
    }

    // Date formatting
    public function date(\DateTimeInterface $date, string $format = null): string
    {
        // Using IntlDateFormatter (recommended)
        $formatter = new \IntlDateFormatter(
            $this->locale,
            \IntlDateFormatter::MEDIUM,  // Date type
            \IntlDateFormatter::NONE     // Time type
        );

        return $formatter->format($date);
    }

    public function datetime(\DateTimeInterface $date): string
    {
        $formatter = new \IntlDateFormatter(
            $this->locale,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::SHORT
        );

        return $formatter->format($date);
    }

    // Custom date pattern
    public function dateCustom(\DateTimeInterface $date, string $pattern): string
    {
        $formatter = new \IntlDateFormatter(
            $this->locale,
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL
        );
        $formatter->setPattern($pattern);

        return $formatter->format($date);
    }

    // Number formatting
    public function number(float $number, int $decimals = 0): string
    {
        $formatter = new \NumberFormatter(
            $this->locale,
            \NumberFormatter::DECIMAL
        );
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $formatter->format($number);
    }

    // Currency formatting
    public function currency(float $amount, string $currency = 'USD'): string
    {
        $formatter = new \NumberFormatter(
            $this->locale,
            \NumberFormatter::CURRENCY
        );

        return $formatter->formatCurrency($amount, $currency);
    }

    // Percentage
    public function percent(float $value, int $decimals = 1): string
    {
        $formatter = new \NumberFormatter(
            $this->locale,
            \NumberFormatter::PERCENT
        );
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);

        return $formatter->format($value / 100);
    }

    // Ordinal (1st, 2nd, 3rd)
    public function ordinal(int $number): string
    {
        $formatter = new \NumberFormatter(
            $this->locale,
            \NumberFormatter::ORDINAL
        );

        return $formatter->format($number);
    }

    // Spell out numbers
    public function spellout(int $number): string
    {
        $formatter = new \NumberFormatter(
            $this->locale,
            \NumberFormatter::SPELLOUT
        );

        return $formatter->format($number);
    }
}

// Usage examples
$fmt = new LocaleFormatter('de_DE');

echo $fmt->date(new \DateTime());              // 15. Jan. 2024
echo $fmt->datetime(new \DateTime());          // 15. Jan. 2024, 14:30
echo $fmt->number(1234567.89, 2);              // 1.234.567,89
echo $fmt->currency(29.99, 'EUR');             // 29,99 €
echo $fmt->percent(25, 2);                     // 25,00 %
echo $fmt->ordinal(42);                        // 42.
echo $fmt->spellout(42);                       // zweiundvierzig
```

### Timezone Handling

```php
<?php
class TimezoneManager
{
    private array $userTimezones = [];

    public function setUserTimezone(int $userId, string $timezone): void
    {
        $this->userTimezones[$userId] = $timezone;
        $_SESSION['timezone'] = $timezone;
    }

    public function getUserTimezone(int $userId): string
    {
        return $this->userTimezones[$userId]
            ?? $_SESSION['timezone']
            ?? 'UTC';
    }

    public function convertToUser(\DateTimeInterface $date, int $userId): \DateTimeImmutable
    {
        $tz = new \DateTimeZone($this->getUserTimezone($userId));
        return (new \DateTimeImmutable())
            ->setTimestamp($date->getTimestamp())
            ->setTimezone($tz);
    }

    public function convertToUtc(\DateTimeInterface $date): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())
            ->setTimestamp($date->getTimestamp())
            ->setTimezone(new \DateTimeZone('UTC'));
    }

    // Display a timezone selector
    public function getTimezoneOptions(?string $selected = null): string
    {
        $options = '';
        $timezones = \DateTimeZone::listIdentifiers();

        foreach ($timezones as $tz) {
            $offset = (new \DateTimeZone($tz))->getOffset(new \DateTime('now'));
            $offsetStr = sprintf('%+03d:%02d', $offset / 3600, abs($offset % 3600) / 60);
            $selectedAttr = $tz === $selected ? ' selected' : '';
            $options .= "<option value=\"{$tz}\"{$selectedAttr}>({$offsetStr}) {$tz}</option>\n";
        }

        return $options;
    }
}
```

---

## 22.7 Database-Driven Translations

For dynamic content (user-generated, CMS), translations belong in the database:

```sql
-- Schema for translatable content
CREATE TABLE translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    locale VARCHAR(10) NOT NULL,
    group_name VARCHAR(100) NOT NULL,  -- e.g., 'page', 'email', 'notification'
    key_name VARCHAR(255) NOT NULL,     -- e.g., 'welcome_title', 'reset_password_body'
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_translation (locale, group_name, key_name)
);

-- Index for fast lookup
CREATE INDEX idx_translation_lookup ON translations(locale, group_name);

-- Sample data
INSERT INTO translations (locale, group_name, key_name, value) VALUES
('en', 'page', 'welcome_title', 'Welcome to Our Platform'),
('fr', 'page', 'welcome_title', 'Bienvenue sur notre plateforme'),
('de', 'page', 'welcome_title', 'Willkommen auf unserer Plattform'),
('en', 'email', 'greeting', 'Hello :name!'),
('fr', 'email', 'greeting', 'Bonjour :name !');
```

```php
<?php
class DatabaseTranslator
{
    private PDO $db;
    private array $cache = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function trans(string $key, string $group = 'default', array $params = []): string
    {
        $locale = $_SESSION['locale'] ?? 'en';
        $message = $this->getTranslation($locale, $group, $key);

        if ($message === null) {
            // Fallback to English
            $message = $this->getTranslation('en', $group, $key) ?? $key;
        }

        // Replace parameters
        foreach ($params as $key => $value) {
            $message = str_replace(":{$key}", $value, $message);
        }

        return $message;
    }

    private function getTranslation(string $locale, string $group, string $key): ?string
    {
        $cacheKey = "{$locale}:{$group}:{$key}";

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $stmt = $this->db->prepare(
            'SELECT value FROM translations WHERE locale = ? AND group_name = ? AND key_name = ?'
        );
        $stmt->execute([$locale, $group, $key]);
        $value = $stmt->fetchColumn();

        if ($value !== false) {
            $this->cache[$cacheKey] = $value;
        }

        return $value ?: null;
    }

    // Warm the cache for a group
    public function warmCache(string $locale, string $group): void
    {
        $stmt = $this->db->prepare(
            'SELECT key_name, value FROM translations WHERE locale = ? AND group_name = ?'
        );
        $stmt->execute([$locale, $group]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->cache["{$locale}:{$group}:{$row['key_name']}"] = $row['value'];
        }
    }
}
```

---

## 22.8 URL-Based Locale Routing

```php
<?php
class LocalizedRouter
{
    private array $supportedLocales = ['en', 'fr', 'de', 'es', 'ja'];
    private string $defaultLocale = 'en';

    public function resolve(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($uri, PHP_URL_PATH);

        // Remove leading slash and split
        $segments = array_values(array_filter(explode('/', $path)));

        // Check if first segment is a locale
        if (!empty($segments) && in_array($segments[0], $this->supportedLocales)) {
            $_SESSION['locale'] = $segments[0];

            // Remove locale from path and redirect without it
            // (or keep it, depending on your strategy)
            return $segments[0];
        }

        return $_SESSION['locale'] ?? $this->defaultLocale;
    }

    // Generate localized URL
    public function url(string $path, string $locale = null): string
    {
        $locale = $locale ?? ($_SESSION['locale'] ?? $this->defaultLocale);
        $path = ltrim($path, '/');

        if ($locale === $this->defaultLocale) {
            return "/{$path}";
        }

        return "/{$locale}/{$path}";
    }

    // Generate locale switcher HTML
    public function localeSwitcher(): string
    {
        $current = $_SERVER['REQUEST_URI'];
        $html = '<ul class="locale-switcher">';

        foreach ($this->supportedLocales as $locale) {
            $name = \Locale::getDisplayLanguage($locale, $locale);
            $url = $this->switchUrl($current, $locale);
            $active = ($_SESSION['locale'] ?? $this->defaultLocale) === $locale;
            $class = $active ? 'active' : '';

            $html .= "<li class=\"{$class}\"><a href=\"{$url}\">{$name}</a></li>";
        }

        return $html . '</ul>';
    }

    private function switchUrl(string $currentUri, string $newLocale): string
    {
        $path = parse_url($currentUri, PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $path)));

        if (!empty($segments) && in_array($segments[0], $this->supportedLocales)) {
            $segments[0] = $newLocale;
        } else {
            array_unshift($segments, $newLocale);
        }

        return '/' . implode('/', $segments);
    }
}
```

---

## 22.9 Best Practices

1. **Use ICU/Intl extension** — It handles locale-specific rules correctly
2. **Always store UTC in database** — Convert to user timezone only when displaying
3. **Use ICU message format** for complex translations (gender, plurals)
4. **Separate code from content** — Never hardcode display strings in business logic
5. **Provide fallbacks** — Always have `en` as default, and the key itself as last resort
6. **Cache translations** — .mo files are already binary, but DB translations should be cached
7. **RTL support** — For Arabic, Hebrew, set `dir="rtl"` on the `<html>` tag

### RTL Support

```php
<?php
// In your layout template
$rtlLocales = ['ar', 'he', 'fa', 'ur'];
$dir = in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';
?>
<html dir="<?= $dir ?>" lang="<?= $locale ?>">
```

---

## 22.10 Exercises

1. **gettext setup:** Create translation files for English and French, translate 10 strings
2. **Locale detector:** Build a class that detects locale from browser, URL, and session
3. **Locale formatter:** Display dates, numbers, and currency in 3 different locales
4. **Database translations:** Create a translatable CMS page using the database approach
5. **Locale switcher:** Build a UI component that lets users switch between languages
6. **RTL support:** Add right-to-left support for Arabic

---

## 22.11 Interview Questions

1. "What's the difference between i18n and l10n?"
2. "How do you handle plural forms in different languages?"
3. "Explain the difference between gettext and array-based translation."
4. "How do you detect and persist a user's locale preference?"
5. "What is the Intl extension and what problems does it solve?"
6. "How would you handle date/timezone display for a global user base?"
7. "What considerations go into translating a dynamic (database-driven) application?"

---

## Further Reading

- **PHP Manual:** [IntlDateFormatter](https://www.php.net/manual/en/class.intldateformatter.php)
- **PHP Manual:** [NumberFormatter](https://www.php.net/manual/en/class.numberformatter.php)
- **PHP Manual:** [gettext](https://www.php.net/manual/en/book.gettext.php)
- **Resource:** [ICU Message Format](https://unicode-org.github.io/icu/userguide/format_parse/messages/)
- **Spec:** [Accept-Language header (RFC 7231)](https://tools.ietf.org/html/rfc7231#section-5.3.5)
- **Book:** "Internationalization: Developing Software for Global Markets"

---

*End of Chapter 22. Proceed to Chapter 23: Cloud File Storage.*
