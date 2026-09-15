<?php
/**
 * PDFtoExcel.in - Privacy Policy & Data Protection Charter
 */

$pageTitle = "Privacy Policy & Data Security Guarantee | PDFtoExcel.in";
$pageDesc = "Read our transparent privacy policy and zero-retention data security standards. Uploaded documents are permanently wiped from server memory in 60 minutes.";
$canonicalUrl = "https://pdftoexcel.in/privacy-policy";
$mainKeyword = "privacy policy";
$supportingKeywords = [
    "data protection",
    "pdf file security",
    "file deletion policy",
    "gdpr compliance"
];

$toolConfig = null;
require_once __DIR__ . '/../includes/header.php';
?>

<main class="main-content">
    <section class="hero-section" style="padding: 3rem 0 2rem;">
        <div class="container text-center" style="max-width: 820px;">
            <div class="hero-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Zero-Data Retention Policy</span>
            </div>
            <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 1rem;">
                Privacy Policy & Security Standards
            </h1>
            <p class="hero-desc">
                Your confidentiality and data rights are paramount. At PDFtoExcel.in, all file transfers are protected by 256-bit SSL encryption, processed in volatile RAM, and permanently deleted within 60 minutes.
            </p>
        </div>
    </section>

    <section class="content-section" style="padding-top: 0;">
        <div class="container content-rich" style="max-width: 900px;">
            <div class="pro-tip-box" style="margin-bottom: 2.5rem;">
                <h4 style="margin-top: 0; color: var(--primary);">Key Takeaways for Corporate & Personal Users</h4>
                <ul class="checklist" style="margin-bottom: 0;">
                    <li><strong>No Permanent Storage:</strong> Uploaded documents and converted spreadsheets are permanently wiped after 60 minutes.</li>
                    <li><strong>No File Inspection:</strong> Automated parsers process data without human intervention, inspection, or manual review.</li>
                    <li><strong>Zero Account Tracking:</strong> We do not require registration, email capture, credit cards, or phone numbers.</li>
                    <li><strong>No Third-Party Monetization:</strong> We never sell, rent, index, or train machine learning models on your uploaded files.</li>
                </ul>
            </div>

            <h2>1. Information We Do Not Collect</h2>
            <p>
                Unlike commercial SaaS suites that compel users to create accounts, provide business email domains, or link social logins, PDFtoExcel.in operates an anonymous utility model. We do not collect names, home addresses, phone numbers, or payment credentials.
            </p>

            <h2>2. Document Processing & Automated File Deletion</h2>
            <p>
                When you drag and drop a PDF into our converter dropzone:
            </p>
            <ol class="ordered-feature-list">
                <li>
                    <strong>In-Transit Encryption:</strong> Files travel across an end-to-end encrypted 256-bit TLS/SSL connection directly to our isolated sandboxed environment.
                </li>
                <li>
                    <strong>Memory-Only Extraction:</strong> The document layout parser analyzes cell coordinates and font data within ephemeral runtime memory.
                </li>
                <li>
                    <strong>Automated Lifetime Expiration:</strong> The converted spreadsheet is delivered to your client browser. An automated server cron daemon irrevocably shreds and unlinks both source and output files within 60 minutes.
                </li>
            </ol>

            <h2>3. GDPR, CCPA & Global Regulatory Compliance</h2>
            <p>
                We adhere strictly to the European Union General Data Protection Regulation (GDPR) and the California Consumer Privacy Act (CCPA). Because files are held temporarily for the sole mechanical purpose of conversion and permanently destroyed immediately thereafter, we act strictly as an automated, non-retentive data conduit.
            </p>

            <h2>4. Cookies and Analytical Metrics</h2>
            <p>
                We maintain minimal, privacy-centric analytics to monitor server uptime, network latency, and error rates. We do not deploy intrusive third-party cross-site trackers or behavioral advertising cookies.
            </p>

            <h2>5. Contacting the Security & Privacy Team</h2>
            <p>
                If you have administrative inquiries regarding our infrastructure, data handling protocols, or security whitepapers, contact our security officer via our <a href="/contact">Contact Page</a>.
            </p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
