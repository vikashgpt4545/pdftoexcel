<?php
/**
 * PDFtoExcel.in - Core PHP Clean URL Router
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If running on PHP built-in server, directly serve existing static files
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . $requestUri;
    if (is_file($filePath)) {
        return false; // serve requested resource as-is
    }
}

// Clean leading/trailing slashes
$route = trim($requestUri, '/');

// Handle XML Sitemap
if ($route === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    if (file_exists(__DIR__ . '/sitemap.xml')) {
        readfile(__DIR__ . '/sitemap.xml');
    } else {
        http_response_code(404);
        echo '<?xml version="1.0" encoding="UTF-8"?><error>Sitemap not found</error>';
    }
    exit;
}

// Handle llms.txt for AI / LLM discoverability
if ($route === 'llms.txt' || $route === 'llms-full.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    $llmFile = __DIR__ . '/' . $route;
    if (file_exists($llmFile)) {
        readfile($llmFile);
    } else {
        http_response_code(404);
    }
    exit;
}

// Handle Robots.txt
if ($route === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    if (file_exists(__DIR__ . '/robots.txt')) {
        readfile(__DIR__ . '/robots.txt');
    } else {
        echo "User-agent: *\nAllow: /\nDisallow: /api/\nDisallow: /data/\nDisallow: /includes/\nDisallow: /admin\nSitemap: https://pdftoexcel.in/sitemap.xml\n";
    }
    exit;
}

// Handle API endpoints
if ($route === 'api/convert' || $route === 'api/convert.php') {
    require __DIR__ . '/api/convert.php';
    exit;
}
if ($route === 'api/rate' || $route === 'api/rate.php') {
    require __DIR__ . '/api/rate.php';
    exit;
}
if ($route === 'api/contact' || $route === 'api/contact.php') {
    require __DIR__ . '/api/contact.php';
    exit;
}

// Handle Admin Dashboard (Protected Portal)
if ($route === 'admin' || $route === 'admin.php') {
    require __DIR__ . '/pages/admin.php';
    exit;
}

// 301 Redirect direct /pages/ requests to clean URLs
if (strpos($route, 'pages/') === 0) {
    $cleanSlug = preg_replace('/^pages\//', '', $route);
    $cleanSlug = preg_replace('/\.php$/', '', $cleanSlug);
    header("Location: https://pdftoexcel.in/" . $cleanSlug, true, 301);
    exit;
}

// 301 Redirect /index.php to root canonical
if ($route === 'index.php') {
    header("Location: https://pdftoexcel.in/", true, 301);
    exit;
}

// Initialize database helper and log visitor page view for self-hosted analytics
require_once __DIR__ . '/includes/db.php';
Database::logPageView($requestUri);

// Route Root to Homepage
if ($route === '') {
    require __DIR__ . '/index.php';
    exit;
}

// Route to individual page in pages/ directory
$pageFile = __DIR__ . '/pages/' . $route . '.php';
if (file_exists($pageFile)) {
    require $pageFile;
    exit;
}

// Serve existing static file in root if requested (e.g., google verification files)
if (is_file(__DIR__ . '/' . $route)) {
    $mime = mime_content_type(__DIR__ . '/' . $route) ?: 'text/html';
    header("Content-Type: $mime");
    readfile(__DIR__ . '/' . $route);
    exit;
}

// 404 Not Found Page (Noindex, Nofollow, No Canonical, No Tool Schema)
http_response_code(404);
$is404 = true;
$pageTitle = "Page Not Found - 404 Error | PDFtoExcel.in";
$pageDesc = "The page you are looking for does not exist or has been moved.";
$canonicalUrl = null;
$mainKeyword = "404";
$supportingKeywords = [];
$toolConfig = null; // Do not render converter tool on 404

require __DIR__ . '/includes/header.php';
?>
<main class="main-content">
    <section class="hero-section container text-center" style="min-height: 50vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div class="badge badge-warning" style="margin-bottom: 1rem;">404 Page Not Found</div>
        <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 1rem;">Oops! We couldn't find that page</h1>
        <p class="hero-desc" style="max-width: 600px; margin: 0 auto 2rem; color: var(--text-muted);">
            The conversion tool or resource you are looking for might have been renamed, moved, or is temporarily unavailable. Return to our homepage to convert PDF to Excel for free.
        </p>
        <div class="cta-group" style="display: flex; gap: 1rem; justify-content: center;">
            <a href="/" class="btn btn-primary">Go to PDF to Excel Converter</a>
            <a href="/excel-to-pdf" class="btn btn-secondary">Convert Excel to PDF</a>
        </div>
    </section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
exit;
