<?php
/**
 * PDFtoExcel.in - Universal Header Component
 */
require_once __DIR__ . '/schema.php';

$pageTitle = $pageTitle ?? 'Convert PDF to Excel 100% Free Online - No Login';
$pageDesc = $pageDesc ?? 'Convert PDF to Excel spreadsheets free online with no login or email required. Fast, accurate, secure document table extraction.';
$canonicalUrl = $canonicalUrl ?? 'https://pdftoexcel.in' . ($_SERVER['REQUEST_URI'] ?? '/');
$canonicalUrl = strtok($canonicalUrl, '?'); // strip query strings from canonical
$mainKeyword = $mainKeyword ?? 'pdf to excel';
$toolName = $toolName ?? 'PDF to Excel Converter';
$faqs = $faqs ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
    <?php if (!empty($is404)): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php else: ?>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php endif; ?>
    <?php endif; ?>
    
    <!-- Google Search Console Verification -->
    <meta name="google-site-verification" content="google15909bbf880633d1">
    
    <!-- PWA & Mobile Web App Capabilities -->
    <meta name="theme-color" content="#10B981">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    
    <!-- SEO Keywords Meta Tag & JSON Data -->
    <?php 
    if (empty($supportingKeywords)) {
        $keywordsDb = json_decode(@file_get_contents(__DIR__ . '/../data/keywords.json'), true) ?: [];
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $currentPath = rtrim($currentPath, '/') ?: '/';
        foreach ($keywordsDb as $item) {
            $itemPath = rtrim($item['url'], '/') ?: '/';
            if ($itemPath === $currentPath) {
                $supportingKeywords = $item['supporting_keywords'] ?? [];
                if (empty($mainKeyword)) {
                    $mainKeyword = $item['main_keyword'] ?? '';
                }
            }
        }
    }
    $allMetaKeywords = array_filter(array_unique(array_merge([$mainKeyword], $supportingKeywords ?? [])));
    // Extract at least 10 highly relevant page-specific keywords
    $pageKeywords = array_slice($allMetaKeywords, 0, 12);
    if (count($pageKeywords) < 10 && !empty($mainKeyword)) {
        $fallbacks = [
            $mainKeyword . ' online',
            'convert ' . $mainKeyword,
            $mainKeyword . ' free',
            $mainKeyword . ' converter',
            'free ' . $mainKeyword,
            $mainKeyword . ' no login',
            'best ' . $mainKeyword,
            $mainKeyword . ' spreadsheet',
            $mainKeyword . ' xlsx',
            $mainKeyword . ' csv'
        ];
        $pageKeywords = array_unique(array_merge($pageKeywords, $fallbacks));
        $pageKeywords = array_slice($pageKeywords, 0, 10);
    }
    ?>
    <meta name="keywords" content="<?= htmlspecialchars(implode(', ', $pageKeywords)) ?>">
    <meta name="author" content="PDFtoExcel.in">
    <meta name="publisher" content="PDFtoExcel.in">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <?php if (!empty($canonicalUrl)): ?>
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta property="og:site_name" content="PDFtoExcel.in">
    <meta property="og:image" content="https://pdftoexcel.in/assets/images/og-preview.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="PDFtoExcel.in - Free Online Document Tools">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <?php if (!empty($canonicalUrl)): ?>
    <meta name="twitter:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php endif; ?>
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDesc) ?>">
    <meta name="twitter:image" content="https://pdftoexcel.in/assets/images/og-preview.png">

    <!-- Fonts & Core Styles (Optimized Payload) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?: time() ?>">

    <!-- JSON-LD Structured Data -->
    <?= renderJsonLdSchema([
        'title' => $pageTitle,
        'description' => $pageDesc,
        'url' => $canonicalUrl ?: 'https://pdftoexcel.in/',
        'tool_name' => $toolName,
        'keywords' => $allMetaKeywords,
        'faqs' => $faqs,
        'is404' => !empty($is404)
    ]) ?>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="site-logo" title="PDF to Excel Converter">
                <span class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="8" y1="13" x2="16" y2="13"></line>
                        <line x1="8" y1="17" x2="16" y2="17"></line>
                        <line x1="10" y1="9" x2="8" y2="9"></line>
                    </svg>
                </span>
                <span class="logo-text">PDFtoExcel<span class="tld">.in</span></span>
            </a>

            <nav class="site-nav" id="siteNav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="/" class="nav-link <?= $canonicalUrl === 'https://pdftoexcel.in/' ? 'active' : '' ?>">PDF to Excel</a></li>
                    <li class="nav-item"><a href="/excel-to-pdf" class="nav-link <?= strpos($canonicalUrl, 'excel-to-pdf') !== false && strpos($canonicalUrl, 'excel-to-pdf-') === false ? 'active' : '' ?>">Excel to PDF</a></li>
                    <li class="nav-item"><a href="/bank-statement-pdf-to-excel" class="nav-link <?= strpos($canonicalUrl, 'bank-statement') !== false ? 'active' : '' ?>">Bank Statements</a></li>
                    <li class="nav-item dropdown">
                        <a href="#more-tools" class="nav-link dropdown-toggle">More Tools <span class="arrow">▼</span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/pdf-invoice-to-excel">PDF Invoice to Excel</a></li>
                            <li><a href="/pdf-to-csv">PDF to CSV</a></li>
                            <li><a href="/word-to-excel">Word to Excel</a></li>
                            <li><a href="/txt-to-excel">TXT to Excel</a></li>
                            <li><a href="/image-to-excel-converter-online">Image to Excel</a></li>
                            <li><a href="/pdf-to-excel-ocr">Scanned PDF (OCR)</a></li>
                            <li><a href="/pdf-to-excel-converter-ai">AI PDF to Excel</a></li>
                            <li><a href="/pdf-to-google-sheets">PDF to Google Sheets</a></li>
                            <li><a href="/multiple-pdf-to-excel-converter">Multiple PDF (Batch)</a></li>
                            <li><a href="/large-pdf-to-excel-converter">Large PDF (50MB+)</a></li>
                            <li><a href="/excel-to-pdf-in-one-page">Excel to PDF (1-Page)</a></li>
                            <li><a href="/excel-to-pdf-landscape">Excel to PDF Landscape</a></li>
                            <li><a href="/convert-password-protected-pdf-to-excel">Password Protected PDF</a></li>
                            <li><a href="/pdf-to-excel-converter-online-free-without-changing-format">Keep Original Format</a></li>
                            <li><a href="/pdf-to-excel-converter-online-free-without-email">No Email Required</a></li>
                            <li><a href="/how-to-insert-pdf-in-excel">How to Insert PDF</a></li>
                            <li><a href="/how-to-convert-pdf-to-excel-in-mobile">Convert on Mobile</a></li>
                            <li><a href="/pdf-to-excel-converter-free-download">Desktop Software</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="header-actions">
                <span class="badge-free">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    100% Free · No Login
                </span>
                <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Navigation Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>
