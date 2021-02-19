# Pollen Pdf Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/container/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen **Pdf** Component provides tools to generate, write, share, read and display PDF files.

## Installation

```bash
composer require pollen-solutions/pdf
```

## Basic Usage

### Displaying PDF

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Pdf\DemoPdfController;

$response = (new DemoPdfController())->responseDisplay();
(new SapiEmitter())->emit($response->psr());
exit;
```

### Downloading PDF

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Pdf\DemoPdfController;

$response = (new DemoPdfController())->responseDownload();
(new SapiEmitter())->emit($response->psr());
exit;
```

### Rendering HTML

```php
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Pollen\Pdf\DemoPdfController;

$response = (new DemoPdfController())->responseHtml();
(new SapiEmitter())->emit($response->psr());
exit;
```

## Through a Routing System (Pollen Routing Component example)

### Displaying PDF

```php
use Pollen\Http\Request;
use Pollen\Pdf\DemoPdfController;
use Pollen\Routing\Router;

// Create the Request object
$request = Request::createFromGlobals();

// Router instantiation
$router = new Router();

// Map a route
$router->map('GET', '/', [DemoPdfController::class, 'responseDisplay']);

// Catch HTTP Response
$response = $router->handleRequest($request);

// Send the response to the browser
$router->sendResponse($response);

// Trigger the terminate event
$router->terminateEvent($request, $response);
```

### Downloading PDF

```php
use Pollen\Http\Request;
use Pollen\Pdf\DemoPdfController;
use Pollen\Routing\Router;

// Create the Request object
$request = Request::createFromGlobals();

// Router instantiation
$router = new Router();

// Map a route
$router->map('GET', '/', [DemoPdfController::class, 'responseDownload']);

// Catch HTTP Response
$response = $router->handleRequest($request);

// Send the response to the browser
$router->sendResponse($response);

// Trigger the terminate event
$router->terminateEvent($request, $response);
```

### Rendering HTML

```php
use Pollen\Http\Request;
use Pollen\Pdf\DemoPdfController;
use Pollen\Routing\Router;

// Create the Request object
$request = Request::createFromGlobals();

// Router instantiation
$router = new Router();

// Map a route
$router->map('GET', '/', [DemoPdfController::class, 'responseHtml']);

// Catch HTTP Response
$response = $router->handleRequest($request);

// Send the response to the browser
$router->sendResponse($response);

// Trigger the terminate event
$router->terminateEvent($request, $response);
```