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
        require __DIR__ . '/includes/generate-sitemap.php';
    }
    exit;
}

// Handle Robots.txt
if ($route === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    if (file_exists(__DIR__ . '/robots.txt')) {
        readfile(__DIR__ . '/robots.txt');
    } else {
        echo "User-agent: *\nAllow: /\nSitemap: https://pdftoexcel.in/sitemap.xml\n";
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

// Route Root to Homepage
if ($route === '' || $route === 'index.php') {
    require __DIR__ . '/index.php';
    exit;
}

// Route to individual page in pages/ directory
$pageFile = __DIR__ . '/pages/' . $route . '.php';
if (file_exists($pageFile)) {
    require $pageFile;
    exit;
}

// 404 Not Found Page
http_response_code(404);
$pageTitle = "Page Not Found - 404 Error | PDFtoExcel.in";
$pageDesc = "The page you are looking for does not exist or has been moved.";
$canonicalUrl = "https://pdftoexcel.in/404";
$mainKeyword = "404";
$supportingKeywords = [];

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
