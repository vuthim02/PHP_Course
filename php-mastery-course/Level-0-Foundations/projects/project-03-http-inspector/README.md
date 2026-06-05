# Project 3: HTTP Inspector & Request Lab

A PHP web application that displays **every detail of an incoming HTTP request** — method, URI, headers, GET/POST parameters, raw body, cookies, and server variables. Includes an HTML form page that submits via both GET and POST so you can inspect the difference.

## Learning Objectives

- Understand the **HTTP request/response lifecycle** (client → server → PHP → response)
- Distinguish between **GET** (params in URL) and **POST** (params in body)
- Read and interpret **HTTP headers** (Content-Type, Accept, User-Agent, Cookie, etc.)
- Explore PHP **superglobals**: `$_GET`, `$_POST`, `$_SERVER`, `$_COOKIE`, `getallheaders()`
- Read the **raw request body** with `php://input`
- Serve a PHP application with the built-in development server (`php -S`)
- Use **browser DevTools** to inspect network requests (Network tab)

## Features

| Feature | Description |
|---------|-------------|
| **Request Line** | Shows `GET /index.php HTTP/1.1` with color-coded components |
| **Headers Table** | All request headers (Host, User-Agent, Content-Type, Accept, Cookie, etc.) |
| **GET Parameters** | Parsed query string displayed in a clean table |
| **POST Parameters** | Form-urlencoded or multipart form data |
| **Raw Body** | Shows raw request payload (useful for JSON, PUT requests) |
| **Cookies** | All cookies sent by the browser |
| **Server Variables** | Key `$_SERVER` entries (remote IP, port, script path, etc.) |
| **Form Demo** | Separate HTML page with GET and POST forms to experiment with |
| **Quick Links** | Pre-built GET test links (`?foo=bar`, `?name=Alex`) |

## How to Run

```bash
cd project-03-http-inspector
php -S localhost:8080
```

Then open:

- **`http://localhost:8080/`** — The inspector tool showing the current request
- **`http://localhost:8080/form-demo.html`** — The form demo page

Try these experiments:

1. Click the links on the inspector page to test GET parameters
2. Submit the GET form → inspect how params appear in the URL and `$_GET`
3. Submit the POST form → inspect how params appear in the body and `$_POST`
4. Open **Browser DevTools > Network tab** → click a request → view headers, payload, response
5. Use `curl` to send custom requests: `curl -v -X POST -d '{"test":"json"}' -H "Content-Type: application/json" http://localhost:8080/`

## Expected Output

The inspector page shows:

- **Colored request line** at the top (method in yellow, URI in purple, protocol in green)
- **Quick links** to test various GET parameter combinations
- **Request Headers** table: Host, User-Agent, Accept, Content-Type, Cookie, etc.
- **GET Parameters** table (populated when query string is present)
- **POST Parameters** table (populated when a POST form is submitted)
- **Raw Request Body** block (shown when body is non-empty — e.g., JSON payload)
- **Cookies** table
- **Server Variables** table: REMOTE_ADDR, REQUEST_TIME, DOCUMENT_ROOT, etc.

## Code Structure

```
project-03-http-inspector/
├── index.php          # Main inspector — displays all request info
├── form-demo.html     # HTML form page with GET and POST examples
└── README.md
```

### File Responsibilities

- **`index.php`** — The core of the project. Uses `getallheaders()`, `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, and `file_get_contents('php://input')` to capture every part of the request. Renders them in a clean single-page UI with responsive tables.
- **`form-demo.html`** — A standalone HTML page with two forms: one with `method="get"` and one with `method="post"`. Both submit to `index.php`. Includes text inputs, selects, checkboxes, radio buttons, and textareas.

## Concepts Practiced

| Concept | How It's Used |
|---------|---------------|
| **How the web works** | Full HTTP request/response cycle demonstrated live |
| **HTTP** | Methods (GET, POST), headers, status codes, URL query strings, message body |
| **Browsers** | Forms, DevTools Network tab, cookies, User-Agent strings |
| **Servers** | PHP built-in server, request handling, `$_SERVER` variables |
| **PHP (prelude)** | Superglobals, `file_get_contents('php://input')`, `getallheaders()` |
| **Web applications** | Dynamic page generation, form handling, request introspection |
| **HTML forms** | GET vs POST, input types, form encoding (`application/x-www-form-urlencoded`) |
| **Security basics** | Why POST is preferred for sensitive data (not visible in URL), `htmlspecialchars` to prevent XSS |
