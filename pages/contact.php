<?php
/**
 * PDFtoExcel.in - Support & Contact Portal
 */

$pageTitle = "Contact Us & Customer Support | PDFtoExcel.in";
$pageDesc = "Reach out to the PDFtoExcel.in technical support team for conversion feedback, enterprise feature requests, bug reports, and partnership inquiries.";
$canonicalUrl = "https://pdftoexcel.in/contact";
$mainKeyword = "contact us";
$supportingKeywords = [
    "customer support",
    "pdf tool feedback",
    "help desk",
    "support inquiry"
];

$toolConfig = null;
require_once __DIR__ . '/../includes/header.php';
?>

<main class="main-content">
    <section class="hero-section" style="padding: 3rem 0 2rem;">
        <div class="container text-center" style="max-width: 820px;">
            <div class="hero-badge-pill">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Support & Inquiries</span>
            </div>
            <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 1rem;">
                Get in Touch with Our Engineering Team
            </h1>
            <p class="hero-desc">
                Have questions regarding table extraction, custom OCR integration, format anomalies, or partnership proposals? We are here to help.
            </p>
        </div>
    </section>

    <section class="content-section" style="padding-top: 0;">
        <div class="container" style="max-width: 860px;">
            <div class="use-case-grid" style="margin-bottom: 3rem;">
                <div class="use-case-card text-center">
                    <h4>Technical Inquiries</h4>
                    <p>Encountering an unusual PDF layout or corrupted table structure?</p>
                    <p style="color: var(--primary); font-weight: 600; margin-top: 0.5rem;">support@pdftoexcel.in</p>
                </div>
                <div class="use-case-card text-center">
                    <h4>Security & Privacy</h4>
                    <p>Compliance documentation, audit inquiries, and data questions.</p>
                    <p style="color: var(--primary); font-weight: 600; margin-top: 0.5rem;">security@pdftoexcel.in</p>
                </div>
            </div>

            <div class="converter-card" style="max-width: 680px; margin: 0 auto; padding: 2.5rem;">
                <div id="contactFormWrapper">
                    <h3 style="margin-bottom: 1.25rem;">Send Us a Message</h3>
                    <form id="contactForm" onsubmit="event.preventDefault(); document.getElementById('contactFormWrapper').style.display = 'none'; document.getElementById('contactSuccessWrapper').style.display = 'block';">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Your Name</label>
                                <input type="text" required placeholder="Alex Johnson" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Email Address</label>
                                <input type="email" required placeholder="alex@company.com" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Subject</label>
                            <input type="text" required placeholder="Question about table conversion" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Message</label>
                            <textarea required rows="4" placeholder="How can we assist you?" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-upload" style="width: 100%; border: none; cursor: pointer;">
                            Submit Inquiry
                        </button>
                    </form>
                </div>

                <div id="contactSuccessWrapper" style="display: none; text-align: center; padding: 2rem 1rem;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(16, 185, 129, 0.15); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Inquiry Received</h3>
                    <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto 1.5rem; line-height: 1.6;">
                        Thank you for contacting PDFtoExcel.in. An engineering specialist will review your document technical query and follow up at your email address within 24 business hours.
                    </p>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('contactSuccessWrapper').style.display = 'none'; document.getElementById('contactFormWrapper').style.display = 'block'; document.getElementById('contactForm').reset();">
                        Send Another Inquiry
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
