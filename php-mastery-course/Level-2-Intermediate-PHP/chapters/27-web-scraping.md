# Chapter 27: Web Scraping with PHP

## Learning Objectives

By the end of this chapter you will:
- Understand web scraping fundamentals and ethics
- Scrape websites using cURL and Guzzle
- Parse HTML with DOMDocument and DOMXPath
- Use Symfony DomCrawler for clean scraping
- Handle JavaScript-rendered pages with Panther
- Implement politeness policies (rate limiting, robots.txt)
- Build a robust scraper with error handling and retries

---

## 27.1 What is Web Scraping?

Web scraping is extracting data from websites automatically. Instead of copying and pasting data by hand, you write a script that visits pages and picks out the information you need.

Think of it like a librarian who needs to catalog every book:
- **Manually:** Walk to each shelf, read every title, write it down by hand
- **Scraping:** Write a script that visits each page, finds the titles, and saves them to a file

### Ethical Guidelines

Before scraping any website:

1. **Check robots.txt** — `https://example.com/robots.txt` tells you what's allowed
2. **Check Terms of Service** — Some sites explicitly forbid scraping
3. **Rate limit yourself** — Don't hammer the server; add delays
4. **Identify yourself** — Set a descriptive User-Agent
5. **Respect copyright** — Don't republish scraped content without permission
6. **Use official APIs when available** — They're faster, more reliable, and legal

```text
# Example robots.txt
User-agent: *
Disallow: /admin/
Disallow: /api/
Allow: /products/
Crawl-delay: 10
```

---

## 27.2 Basic Scraping with cURL

```php
<?php
/**
 * Basic web scraper using cURL
 */
class BasicScraper
{
    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options + [
            'user_agent' => 'PHP-Mastery-Scraper/1.0 (learning purposes)',
            'timeout' => 30,
            'delay' => 1000000, // 1 second between requests (microseconds)
        ];
    }

    public function fetch(string $url): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => $this->options['timeout'],
            CURLOPT_USERAGENT => $this->options['user_agent'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml',
                'Accept-Language: en-US,en;q=0.5',
            ],
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($html === false) {
            throw new \RuntimeException("cURL error: {$error}");
        }

        if ($httpCode >= 400) {
            throw new \RuntimeException("HTTP {$httpCode} fetching {$url}");
        }

        // Polite delay
        usleep($this->options['delay']);

        return $html;
    }

    public function fetchMultiple(array $urls): array
    {
        $multi = curl_multi_init();
        $handles = [];

        foreach ($urls as $key => $url) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => $this->options['timeout'],
                CURLOPT_USERAGENT => $this->options['user_agent'],
            ]);
            curl_multi_add_handle($multi, $ch);
            $handles[$key] = $ch;
        }

        // Execute all requests in parallel
        $running = null;
        do {
            curl_multi_exec($multi, $running);
            curl_multi_select($multi); // Block until activity
        } while ($running > 0);

        $results = [];
        foreach ($handles as $key => $ch) {
            $results[$key] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($multi, $ch);
            curl_close($ch);
        }

        curl_multi_close($multi);
        return $results;
    }
}
```

---

## 27.3 Parsing HTML with DOMDocument

```php
<?php
/**
 * Parse HTML using PHP's built-in DOMDocument
 */
class HtmlParser
{
    private \DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new \DOMDocument();
    }

    public function load(string $html): void
    {
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $this->dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
    }

    // Find all elements by tag name
    public function getElements(string $tag): \DOMNodeList
    {
        return $this->dom->getElementsByTagName($tag);
    }

    // Find elements by CSS selector (basic)
    public function findByClass(string $className): array
    {
        $xpath = new \DOMXPath($this->dom);
        $nodes = $xpath->query("//*[contains(@class, '{$className}')]");
        return iterator_to_array($nodes);
    }

    // Extract text from first match
    public function getText(string $selector): ?string
    {
        $parts = explode('.', $selector);
        $tag = $parts[0] ?: '*';
        $class = $parts[1] ?? '';

        $xpath = new \DOMXPath($this->dom);
        $query = $class
            ? "//{$tag}[contains(@class, '{$class}')]"
            : "//{$tag}";

        $nodes = $xpath->query($query);
        if ($nodes->length === 0) return null;

        return trim($nodes->item(0)->textContent);
    }

    // Extract all links
    public function getLinks(): array
    {
        $links = [];
        foreach ($this->dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            $text = trim($a->textContent);
            if ($href) {
                $links[] = ['href' => $href, 'text' => $text];
            }
        }
        return $links;
    }

    // Extract all images
    public function getImages(): array
    {
        $images = [];
        foreach ($this->dom->getElementsByTagName('img') as $img) {
            $images[] = [
                'src' => $img->getAttribute('src'),
                'alt' => $img->getAttribute('alt'),
            ];
        }
        return $images;
    }

    // Extract meta tags
    public function getMeta(string $name): ?string
    {
        $xpath = new \DOMXPath($this->dom);
        $nodes = $xpath->query("//meta[@name='{$name}']");
        if ($nodes->length === 0) return null;
        return $nodes->item(0)->getAttribute('content');
    }
}

// Usage
$scraper = new BasicScraper();
$html = $scraper->fetch('https://example.com/products');

$parser = new HtmlParser();
$parser->load($html);

// Get page title
$title = $parser->getText('title');
echo "Page: {$title}\n";

// Get all product links
$links = $parser->getLinks();
foreach ($links as $link) {
    echo "  {$link['text']}: {$link['href']}\n";
}

// Get meta description
$description = $parser->getMeta('description');
echo "Description: {$description}\n";
```

---

## 27.4 Using Symfony DomCrawler

DomCrawler provides a much cleaner API for HTML parsing:

```bash
composer require symfony/dom-crawler symfony/css-selector
```

```php
<?php
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

class CrawlerScraper
{
    public function scrapeProductPage(string $html): array
    {
        $crawler = new Crawler($html);

        return [
            'title' => $this->getText($crawler, 'h1.product-title'),
            'price' => $this->getText($crawler, 'span.price'),
            'description' => $this->getText($crawler, 'div.description'),
            'sku' => $this->getAttr($crawler, 'meta[itemprop="sku"]', 'content'),
            'images' => $this->getAttrs($crawler, 'img.product-image', 'src'),
            'reviews' => $this->getReviews($crawler),
        ];
    }

    private function getText(Crawler $crawler, string $selector): ?string
    {
        try {
            return trim($crawler->filter($selector)->text());
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function getAttr(Crawler $crawler, string $selector, string $attr): ?string
    {
        try {
            return $crawler->filter($selector)->attr($attr);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function getAttrs(Crawler $crawler, string $selector, string $attr): array
    {
        try {
            return $crawler->filter($selector)->each(fn(Crawler $node) => $node->attr($attr));
        } catch (\InvalidArgumentException) {
            return [];
        }
    }

    private function getReviews(Crawler $crawler): array
    {
        return $crawler->filter('div.review')->each(function (Crawler $review) {
            return [
                'author' => $review->filter('.review-author')->text(),
                'rating' => (int) str_replace('★', '', $review->filter('.review-rating')->text()),
                'text' => $review->filter('.review-text')->text(),
            ];
        });
    }
}

// Usage with Guzzle
use GuzzleHttp\Client;

$client = new Client([
    'headers' => [
        'User-Agent' => 'PHP-Mastery-Scraper/1.0',
    ],
]);

$response = $client->get('https://example.com/products/123');
$scraper = new CrawlerScraper();
$product = $scraper->scrapeProductPage((string) $response->getBody());

print_r($product);
```

---

## 27.5 Scraping JavaScript-Rendered Pages

Some sites load content dynamically with JavaScript. PHP's cURL/Guzzle won't see this content because they don't execute JS. Use **Panther** (a PHP browser automation tool):

```bash
composer require symfony/panther
```

```php
<?php
use Symfony\Component\Panther\Client;

class JsScraper
{
    private Client $client;

    public function __construct()
    {
        // Uses ChromeDriver (install via: composer require --dev dbrekelmans/bdi)
        $this->client = Client::createChromeClient();
    }

    public function scrapeDynamicPage(string $url): array
    {
        $crawler = $this->client->request('GET', $url);

        // Wait for JavaScript to execute
        $this->client->waitFor('.content-loaded');

        // Now scrape like normal
        return [
            'title' => $crawler->filter('h1')->text(),
            'content' => $crawler->filter('.dynamic-content')->html(),
            'loaded_at' => $crawler->filter('.timestamp')->text(),
        ];
    }

    public function takeScreenshot(string $url, string $path): void
    {
        $this->client->request('GET', $url);
        $this->client->waitFor('.page-loaded');
        $this->client->takeScreenshot($path);
    }

    public function interactAndScrape(string $url): array
    {
        $crawler = $this->client->request('GET', $url);

        // Click "Load More" button
        $this->client->clickLink('Load More');
        $this->client->waitFor('.more-content');

        // Fill and submit a form
        $this->client->submitForm('Search', [
            'q' => 'PHP scraping',
        ]);
        $this->client->waitFor('.search-results');

        // Scrape the results
        return $crawler->filter('.result-item')->each(function ($node) {
            return [
                'title' => $node->filter('h2')->text(),
                'url' => $node->filter('a')->attr('href'),
            ];
        });
    }

    public function __destruct()
    {
        $this->client->quit();
    }
}
```

---

## 27.6 Building a Robust Scraper

```php
<?php
/**
 * Production-grade web scraper with retries, logging, and politeness
 */
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RobustScraper
{
    private Client $client;
    private array $stats = [
        'requests' => 0,
        'success' => 0,
        'failures' => 0,
        'retries' => 0,
    ];

    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        // 1. Retry middleware
        $handlerStack->push(Middleware::retry(
            function (
                int $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?\Throwable $e = null
            ): bool {
                // Retry on connection errors or 5xx responses
                if ($retries >= 3) return false;

                if ($e instanceof ConnectException) return true;

                if ($response && in_array($response->getStatusCode(), [429, 500, 502, 503])) {
                    return true;
                }

                return false;
            },
            function (int $retries): int {
                // Exponential backoff: 1s, 2s, 4s
                return (int) pow(2, $retries) * 1000;
            }
        ));

        // 2. Delay middleware (politeness)
        $handlerStack->push(Middleware::mapRequest(function (RequestInterface $request) {
            static $lastRequest = 0;
            $delay = 1000000; // 1 second
            $wait = $delay - (microtime(true) - $lastRequest) * 1e6;

            if ($wait > 0) {
                usleep((int) $wait);
            }

            $lastRequest = microtime(true);
            return $request;
        }));

        $this->client = new Client([
            'handler' => $handlerStack,
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'PHP-Mastery-Scraper/1.0 (educational)',
                'Accept' => 'text/html,application/xhtml+xml',
            ],
        ]);
    }

    public function scrape(string $url): string
    {
        $this->stats['requests']++;

        try {
            $response = $this->client->get($url);
            $this->stats['success']++;
            return (string) $response->getBody();
        } catch (\Throwable $e) {
            $this->stats['failures']++;
            throw new \RuntimeException("Failed to scrape {$url}: {$e->getMessage()}");
        }
    }

    // Scrape with callback (streaming for large pages)
    public function scrapeStream(string $url, callable $onChunk): void
    {
        $response = $this->client->get($url, [
            'stream' => true,
        ]);

        $body = $response->getBody();
        while (!$body->eof()) {
            $chunk = $body->read(4096);
            $onChunk($chunk);
        }
    }

    // Save scraped content to file
    public function scrapeToFile(string $url, string $path): void
    {
        $this->client->get($url, [
            'sink' => $path,
        ]);

        $this->stats['success']++;
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    // Check robots.txt
    public function isAllowed(string $url): bool
    {
        $parsed = parse_url($url);
        $robotsUrl = "{$parsed['scheme']}://{$parsed['host']}/robots.txt";

        try {
            $response = $this->client->get($robotsUrl, ['timeout' => 5]);
            $robots = (string) $response->getBody();

            // Very basic robots.txt parser
            $path = $parsed['path'] ?? '/';
            $disallows = [];

            foreach (explode("\n", $robots) as $line) {
                $line = trim($line);
                if (preg_match('/^Disallow:\s*(.*)$/i', $line, $m)) {
                    $disallows[] = $m[1] ?: '/';
                }
            }

            foreach ($disallows as $disallowed) {
                if (str_starts_with($path, $disallowed)) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable) {
            // If we can't fetch robots.txt, assume allowed
            return true;
        }
    }
}
```

---

## 27.7 Data Extraction Patterns

### Pattern 1: Scraping a Product Listing Page

```php
<?php
class ProductListingScraper
{
    public function scrape(string $html): array
    {
        $crawler = new Crawler($html);
        $products = [];

        $crawler->filter('.product-card')->each(function (Crawler $card) use (&$products) {
            $products[] = [
                'name' => $card->filter('.product-name')->text(),
                'price' => $this->parsePrice($card->filter('.product-price')->text()),
                'rating' => $this->parseRating($card->filter('.product-rating')->text()),
                'url' => $card->filter('a')->attr('href'),
                'in_stock' => $card->filter('.out-of-stock')->count() === 0,
            ];
        });

        // Handle pagination
        $nextPage = $crawler->filter('a.next')->attr('href');

        return [
            'products' => $products,
            'next_page' => $nextPage,
        ];
    }

    private function parsePrice(string $text): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $text);
    }

    private function parseRating(string $text): float
    {
        preg_match('/([\d.]+)\s*\/\s*5/', $text, $m);
        return (float) ($m[1] ?? 0);
    }
}
```

### Pattern 2: Scraping Tables

```php
<?php
class TableScraper
{
    public function scrapeTable(string $html, string $tableSelector): array
    {
        $crawler = new Crawler($html);
        $table = $crawler->filter($tableSelector);

        $headers = [];
        $table->filter('th')->each(function (Crawler $th) use (&$headers) {
            $headers[] = trim($th->text());
        });

        $rows = [];
        $table->filter('tr')->each(function (Crawler $tr) use ($headers, &$rows) {
            $cells = [];
            $tr->filter('td')->each(function (Crawler $td) use (&$cells) {
                $cells[] = trim($td->text());
            });

            if (!empty($cells)) {
                $rows[] = count($headers) === count($cells)
                    ? array_combine($headers, $cells)
                    : $cells;
            }
        });

        return $rows;
    }
}
```

### Pattern 3: Scraping JSON-LD Structured Data

```php
<?php
class JsonLdScraper
{
    public function extractJsonLd(string $html): array
    {
        $crawler = new Crawler($html);
        $results = [];

        $crawler->filter('script[type="application/ld+json"]')->each(function (Crawler $script) use (&$results) {
            $data = json_decode($script->text(), true);
            if ($data) {
                $results[] = $data;
            }
        });

        return $results;
    }
}
```

---

## 27.8 Handling Anti-Scraping Measures

```php
<?php
/**
 * Anti-detection measures for stubborn sites
 */
class AntiDetectionScraper
{
    private array $userAgents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
        'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0',
    ];

    public function getWithBypass(string $url): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => $this->userAgents[array_rand($this->userAgents)],

            // Add browser-like headers
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate, br',
                'DNT: 1',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
            ],

            // Set up cookies
            CURLOPT_COOKIEFILE => '/tmp/cookies.txt',
            CURLOPT_COOKIEJAR => '/tmp/cookies.txt',

            // Handle compression
            CURLOPT_ENCODING => 'gzip, deflate, br',
        ]);

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    // Rotating proxy support
    public function setProxy(string $proxy): void
    {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
}
```

---

## 27.9 Exporting Scraped Data

```php
<?php
class DataExporter
{
    public function toJson(array $data, string $path): void
    {
        file_put_contents(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function toCsv(array $data, string $path): void
    {
        $handle = fopen($path, 'w');

        // Headers
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
        }

        // Rows
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }

    public function toSql(array $data, string $table, string $path): void
    {
        $sql = "INSERT INTO {$table} (" . implode(', ', array_keys($data[0])) . ") VALUES\n";
        $values = [];

        foreach ($data as $row) {
            $escaped = array_map(fn($v) => "'" . addslashes($v) . "'", array_values($row));
            $values[] = "(" . implode(', ', $escaped) . ")";
        }

        $sql .= implode(",\n", $values) . ";\n";
        file_put_contents($path, $sql);
    }
}
```

---

## 27.10 Complete Scraping Pipeline

```php
<?php
/**
 * End-to-end product scraping pipeline
 */
class ScrapingPipeline
{
    private RobustScraper $scraper;
    private CrawlerScraper $parser;
    private DataExporter $exporter;

    public function __construct()
    {
        $this->scraper = new RobustScraper();
        $this->parser = new CrawlerScraper();
        $this->exporter = new DataExporter();
    }

    public function run(string $startUrl, string $outputPath): array
    {
        echo "Starting scrape of {$startUrl}\n";
        $allProducts = [];
        $url = $startUrl;

        while ($url) {
            echo "  Fetching: {$url}\n";

            // Check robots.txt
            if (!$this->scraper->isAllowed($url)) {
                echo "  Blocked by robots.txt, skipping\n";
                break;
            }

            // Fetch and parse
            try {
                $html = $this->scraper->scrape($url);
                $result = $this->parseListingPage($html);
            } catch (\Throwable $e) {
                echo "  Error: {$e->getMessage()}\n";
                break;
            }

            $allProducts = array_merge($allProducts, $result['products']);
            $url = $result['next_page'];

            if ($url) {
                // Resolve relative URLs
                $url = $this->resolveUrl($startUrl, $url);
            }
        }

        // Export
        $this->exporter->toJson($allProducts, $outputPath);
        $this->exporter->toCsv($allProducts, str_replace('.json', '.csv', $outputPath));

        echo "Scraping complete. {$allProducts} products saved to {$outputPath}\n";
        print_r($this->scraper->getStats());

        return $allProducts;
    }

    private function parseListingPage(string $html): array
    {
        $crawler = new Crawler($html);
        $products = [];

        $crawler->filter('.product-item')->each(function (Crawler $item) use (&$products) {
            $products[] = [
                'name' => $item->filter('.item-title')->text(),
                'price' => $item->filter('.item-price')->text(),
                'url' => $item->filter('a')->attr('href'),
            ];
        });

        $next = $crawler->filter('a.next')->attr('href');

        return [
            'products' => $products,
            'next_page' => $next ?: null,
        ];
    }

    private function resolveUrl(string $base, string $relative): string
    {
        if (str_starts_with($relative, 'http')) return $relative;
        $parsed = parse_url($base);
        return "{$parsed['scheme']}://{$parsed['host']}{$relative}";
    }
}
```

---

## 27.11 Common Mistakes

| Mistake | Impact | Solution |
|---------|--------|----------|
| No delay between requests | IP banned | Add `usleep()` between requests |
| Ignoring robots.txt | Legal issues | Always check robots.txt |
| Hardcoding selectors | Breaks when site updates | Use data attributes when possible |
| No error handling | Script fails mid-run | Wrap in try/catch with retries |
| Not handling pagination | Only gets first page | Follow next-page links |
| No User-Agent header | Default UA blocked | Set a real browser User-Agent |
| Parsing broken HTML | Missing data | Use DOMDocument with error suppression |

---

## 27.12 Exercises

1. **Basic scraping:** Scrape the title and meta description from 5 websites
2. **Product scraper:** Build a scraper that extracts product info from a listing page
3. **Table scraper:** Scrape a Wikipedia table and export to CSV
4. **Pagination:** Scrape all pages of a paginated listing
5. **Data export:** Build a scraper that outputs JSON, CSV, and SQL
6. **Robust scraper:** Add retry logic, rate limiting, and logging to a scraper
7. **JS scraping:** Use Panther to scrape a JavaScript-rendered page

---

## 27.13 Interview Questions

1. "How do you handle pagination in a web scraper?"
2. "What anti-scraping measures have you encountered and how did you handle them?"
3. "Explain the difference between scraping static and JavaScript-rendered pages."
4. "How would you scrape 10,000 pages efficiently?"
5. "What ethical considerations should guide web scraping?"
6. "How do you parse malformed HTML in PHP?"
7. "What's the difference between DOMDocument and DomCrawler?"

---

## Further Reading

- **Library:** [Symfony DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)
- **Library:** [Symfony Panther](https://github.com/symfony/panther)
- **Library:** [Guzzle HTTP Client](https://docs.guzzlephp.org/)
- **Tool:** [Chrome DevTools](https://developer.chrome.com/docs/devtools/) — Inspect page structure
- **Resource:** [robots.txt](https://www.robotstxt.org/) — Robots exclusion standard
- **Resource:** [Scrapy (Python)](https://scrapy.org/) — Reference architecture

---

*End of Chapter 27. Proceed to Level 4: Professional Backend Development.*
