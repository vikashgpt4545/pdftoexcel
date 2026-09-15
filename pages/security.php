<?php
/**
 * PDFtoExcel.in - Security Architecture Whitepaper
 */

$pageTitle = "Security Architecture & Encryption Standards | PDFtoExcel.in";
$pageDesc = "Technical documentation detailing our 256-bit TLS encryption, RAM-only execution pipeline, automated ephemeral storage, and zero-file-retention architecture.";
$canonicalUrl = "https://pdftoexcel.in/security";
$mainKeyword = "security architecture";
$supportingKeywords = [
    "pdf encryption",
    "secure document conversion",
    "zero retention data pipeline",
    "ssl certificate"
];

$toolConfig = null;
require_once __DIR__ . '/../includes/header.php';
?>

<main class="main-content">
    <section class="hero-section" style="padding: 3rem 0 2rem;">
        <div class="container text-center" style="max-width: 820px;">
            <div class="hero-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Enterprise Security Whitepaper</span>
            </div>
            <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 1rem;">
                Security Architecture & Data Protections
            </h1>
            <p class="hero-desc">
                Designed from the ground up for bank-grade financial extraction, confidential payroll records, and proprietary invoices without risk of data leaks.
            </p>
        </div>
    </section>

    <section class="content-section" style="padding-top: 0;">
        <div class="container content-rich" style="max-width: 900px;">
            <h2>1. Cryptographic Transport Security (TLS 1.3 & 256-Bit SSL)</h2>
            <p>
                All data transmission between your browser client and our conversion clusters is enforced over TLS 1.3 cryptographic tunnels using 256-bit AES encryption. Man-in-the-middle (MITM) attacks and packet snooping are mathematically prevented.
            </p>

            <h2>2. Ephemeral In-Memory Execution Architecture</h2>
            <p>
                Our parsing engine processes document objects in volatile execution memory (RAM). We do not mount persistent relational databases for user content:
            </p>
            <div class="use-case-grid" style="margin: 2rem 0;">
                <div class="use-case-card">
                    <h4>Sandboxed Processing</h4>
                    <p>Each conversion task operates in an isolated sandbox with zero write permissions to external system paths.</p>
                </div>
                <div class="use-case-card">
                    <h4>60-Minute Purge Timer</h4>
                    <p>Every file is tagged with a Unix timestamp and unlinked with DoD-standard multi-pass data wiping within 60 minutes.</p>
                </div>
            </div>

            <h2>3. Zero Human Access & Zero AI Training</h2>
            <p>
                We do not use customer documents to train proprietary machine learning or language models. Your files are processed strictly through deterministic programmatic parsers without human intervention or administrative inspection.
            </p>

            <h2>4. Infrastructure Hardening</h2>
            <p>
                Our edge infrastructure enforces automated DDoS mitigation, rate limiting, and HTTP security response headers (`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`).
            </p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
