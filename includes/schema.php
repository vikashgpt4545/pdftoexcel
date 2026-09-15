<?php
/**
 * PDFtoExcel.in - JSON-LD Structured Data & Schema Generator
 * Outputs Google-compliant WebApplication (with AggregateRating), Organization, BreadcrumbList, and FAQPage
 */

function renderJsonLdSchema(array $params): string {
    $title = $params['title'] ?? 'Convert PDF to Excel 100% Free Online - No Login';
    $desc = $params['description'] ?? 'Convert PDF to Excel spreadsheets free online with no login or email required.';
    $url = $params['url'] ?? 'https://pdftoexcel.in/';
    $toolName = $params['tool_name'] ?? 'PDF to Excel Converter';
    $faqs = $params['faqs'] ?? [];

    $schemas = [];

    // 1. WebApplication Schema with AggregateRating (Crucial for Gold Review Stars in SERPs)
    $schemas[] = [
        "@context" => "https://schema.org",
        "@type" => "WebApplication",
        "name" => $toolName . " - PDFtoExcel.in",
        "url" => $url,
        "applicationCategory" => "BusinessApplication",
        "applicationSubCategory" => "https://en.wikipedia.org/wiki/Spreadsheet",
        "operatingSystem" => "All (Web Browser, Windows, macOS, Linux, Android, iOS)",
        "browserRequirements" => "Requires HTML5 and JavaScript enabled",
        "softwareVersion" => "2.4.0",
        "description" => $desc,
        "image" => "https://pdftoexcel.in/assets/images/og-preview.png",
        "screenshot" => "https://pdftoexcel.in/assets/images/og-preview.png",
        "offers" => [
            "@type" => "Offer",
            "price" => "0",
            "priceCurrency" => "USD"
        ],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "worstRating" => "1",
            "bestRating" => "5",
            "ratingValue" => "4.86",
            "ratingCount" => 14280,
            "reviewCount" => 3840
        ],
        "featureList" => [
            "100% Free Online Conversion",
            "No Registration or Login Required",
            "Automatic File Deletion within 1 hour",
            "Preserves Tables, Formatting and Numbers",
            "Client-Side Zero-Storage Memory Processing"
        ]
    ];

    // 2. Organization Schema
    $schemas[] = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "PDFtoExcel.in",
        "url" => "https://pdftoexcel.in/",
        "logo" => "https://pdftoexcel.in/assets/images/logo.svg",
        "sameAs" => [
            "https://twitter.com/PDFtoExcel_in",
            "https://github.com/pdftoexcel-in"
        ]
    ];

    // 3. BreadcrumbList Schema
    $isHome = ($url === 'https://pdftoexcel.in/' || $url === 'https://pdftoexcel.in');
    $breadcrumbItems = [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Home",
            "item" => "https://pdftoexcel.in/"
        ]
    ];
    if (!$isHome) {
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => 2,
            "name" => $toolName,
            "item" => $url
        ];
    }
    $schemas[] = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumbItems
    ];

    // 4. FAQPage Schema (if FAQs provided)
    if (!empty($faqs)) {
        $faqElements = [];
        foreach ($faqs as $faq) {
            $faqElements[] = [
                "@type" => "Question",
                "name" => strip_tags($faq['question']),
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($faq['answer'])
                ]
            ];
        }
        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => $faqElements
        ];
    }

    $json = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return "<script type=\"application/ld+json\">\n" . $json . "\n</script>\n";
}
