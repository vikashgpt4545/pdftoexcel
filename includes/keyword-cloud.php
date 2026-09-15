<?php
/**
 * PDFtoExcel.in - Semantic Format Hub & Cross-Tool Directory
 * Replaces obsolete tag clouds with structured internal linking & format architecture
 */
$mainKw = $mainKeyword ?? 'pdf to excel';

$quickConversionHub = [
    [
        'title' => 'Convert PDF to XLSX',
        'desc' => 'Generate modern Microsoft Excel OpenXML sheets with complete styling and formula cells.',
        'url' => '/',
        'badge' => 'Default Format'
    ],
    [
        'title' => 'Scanned PDF to Excel (OCR)',
        'desc' => 'Optical character recognition extracts tabular data from paper scans and smartphone photos.',
        'url' => '/pdf-to-excel-ocr',
        'badge' => 'OCR Enabled'
    ],
    [
        'title' => 'Bank Statement PDF to Excel',
        'desc' => 'Isolate debit, credit, and running balance columns from financial statements without merged cells.',
        'url' => '/bank-statement-pdf-to-excel',
        'badge' => 'Financial Grade'
    ],
    [
        'title' => 'PDF Invoice & Bill to Excel',
        'desc' => 'Extract line items, tax breakdown, and vendor totals into clean accounting worksheets.',
        'url' => '/pdf-invoice-to-excel',
        'badge' => 'Accounting'
    ],
    [
        'title' => 'PDF to CSV Delimited Table',
        'desc' => 'Export flat comma-separated values for direct ingestion into SQL databases, Python, or R.',
        'url' => '/pdf-to-csv',
        'badge' => 'Lightweight'
    ],
    [
        'title' => 'PDF to Google Sheets',
        'desc' => 'Transform documents into cloud-compatible spreadsheets ready for instant Google Drive import.',
        'url' => '/pdf-to-google-sheets',
        'badge' => 'Cloud Ready'
    ],
    [
        'title' => 'Batch Multiple PDF Converter',
        'desc' => 'Queue multiple documents simultaneously and compile them into individual or unified workbooks.',
        'url' => '/multiple-pdf-to-excel-converter',
        'badge' => 'Bulk Workflow'
    ],
    [
        'title' => 'Convert PDF on Mobile Devices',
        'desc' => 'Zero-install responsive progressive web utility optimized for Android, iPhone, and iPad browsers.',
        'url' => '/how-to-convert-pdf-to-excel-in-mobile',
        'badge' => 'iOS & Android'
    ],
    [
        'title' => 'Password Protected PDF to Excel',
        'desc' => 'Unlock and convert encrypted documents into editable spreadsheets with client-side security.',
        'url' => '/convert-password-protected-pdf-to-excel',
        'badge' => 'Encrypted'
    ]
];
?>
<section class="format-hub-section" id="format-hub">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Conversion Capabilities</span>
            <h2 class="section-title">Supported Document Formats & Dedicated Extraction Engines</h2>
            <p class="section-subtitle">Explore specialized tools designed for financial statements, scanned invoices, multi-page catalogs, and mobile workflows.</p>
        </div>
        
        <div class="format-hub-grid">
            <?php foreach ($quickConversionHub as $tool): ?>
            <a href="<?= htmlspecialchars($tool['url']) ?>" class="format-hub-card">
                <div class="format-hub-header">
                    <span class="hub-card-badge"><?= htmlspecialchars($tool['badge']) ?></span>
                    <span class="hub-card-arrow">→</span>
                </div>
                <h3 class="format-hub-title"><?= htmlspecialchars($tool['title']) ?></h3>
                <p class="format-hub-desc"><?= htmlspecialchars($tool['desc']) ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
