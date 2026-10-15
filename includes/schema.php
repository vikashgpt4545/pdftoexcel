<?php
/**
 * PDFtoExcel.in - JSON-LD Structured Data & Schema Generator
 * Outputs Google-compliant WebApplication (with AggregateRating), Organization, BreadcrumbList, and FAQPage
 */

function renderJsonLdSchema(array $params): string {
    if (!empty($params['is404'])) {
        return '';
    }

    $title = $params['title'] ?? 'Convert PDF to Excel 100% Free Online - No Login';
    $desc = $params['description'] ?? 'Convert PDF to Excel spreadsheets free online with no login or email required.';
    $url = $params['url'] ?? 'https://pdftoexcel.in/';
    $toolName = $params['tool_name'] ?? 'PDF to Excel Converter';
    $faqs = $params['faqs'] ?? [];

    $schemas = [];

    // 1. WebApplication Schema (Valid Google compliant SoftwareApplication)
    $webAppSchema = [
        "@context" => "https://schema.org",
        "@type" => "WebApplication",
        "name" => $toolName . " - PDFtoExcel.in",
        "url" => $url,
        "applicationCategory" => "BusinessApplication",
        "applicationSubCategory" => "https://en.wikipedia.org/wiki/Spreadsheet",
        "operatingSystem" => "All (Web Browser, Windows, macOS, Linux, Android, iOS)",
        "browserRequirements" => "Requires HTML5 and JavaScript enabled",
        "softwareVersion" => "2.5.0",
        "description" => $desc,
        "image" => "https://pdftoexcel.in/assets/images/og-preview.png",
        "screenshot" => "https://pdftoexcel.in/assets/images/og-preview.png",
        "offers" => [
            "@type" => "Offer",
            "price" => "0",
            "priceCurrency" => "USD"
        ],
        "featureList" => [
            "100% Free Online Document Conversion",
            "No Registration or Login Required",
            "Client-Side In-Memory Processing",
            "Preserves Tables, Formatting and Numeric Cells",
            "Fast Spreadsheet Table Extraction"
        ]
    ];
    $schemas[] = $webAppSchema;

    // 2. Organization Schema
    $schemas[] = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "PDFtoExcel.in",
        "url" => "https://pdftoexcel.in/",
        "logo" => "https://pdftoexcel.in/assets/images/logo.svg"
    ];

    // 3. BreadcrumbList Schema (Only for subpages with at least 2 hierarchy items)
    $isHome = ($url === 'https://pdftoexcel.in/' || $url === 'https://pdftoexcel.in');
    if (!$isHome) {
        $schemas[] = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                [
                    "@type" => "ListItem",
                    "position" => 1,
                    "name" => "Home",
                    "item" => "https://pdftoexcel.in/"
                ],
                [
                    "@type" => "ListItem",
                    "position" => 2,
                    "name" => $toolName,
                    "item" => $url
                ]
            ]
        ];
    }

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
