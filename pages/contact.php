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
                    <div id="contactErrorBanner" style="display: none; color: #ef4444; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.5;"></div>
                    <form id="contactForm">
                        <input type="text" name="company_website" id="contactCompanyWebsite" style="display:none !important;" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label for="contactName" style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Your Name</label>
                                <input type="text" id="contactName" name="name" required placeholder="Alex Johnson" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                            </div>
                            <div>
                                <label for="contactEmail" style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Email Address</label>
                                <input type="email" id="contactEmail" name="email" required placeholder="alex@company.com" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label for="contactSubject" style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Subject</label>
                            <input type="text" id="contactSubject" name="subject" required placeholder="Question about table conversion" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit;">
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label for="contactMessage" style="display: block; font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">Message</label>
                            <textarea id="contactMessage" name="message" required rows="4" placeholder="How can we assist you?" style="width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main); font-family: inherit; resize: vertical;"></textarea>
                        </div>
                        <button type="submit" id="contactSubmitBtn" class="btn btn-upload" style="width: 100%; border: none; cursor: pointer;">
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
                    <button type="button" id="resetContactBtn" class="btn btn-secondary">
                        Send Another Inquiry
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('contactForm');
    var submitBtn = document.getElementById('contactSubmitBtn');
    var errorBanner = document.getElementById('contactErrorBanner');
    var formWrapper = document.getElementById('contactFormWrapper');
    var successWrapper = document.getElementById('contactSuccessWrapper');
    var resetBtn = document.getElementById('resetContactBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (errorBanner) {
                errorBanner.style.display = 'none';
                errorBanner.textContent = '';
            }

            var nameInput = document.getElementById('contactName');
            var emailInput = document.getElementById('contactEmail');
            var subjectInput = document.getElementById('contactSubject');
            var messageInput = document.getElementById('contactMessage');
            var honeypotInput = document.getElementById('contactCompanyWebsite');

            var name = nameInput ? nameInput.value.trim() : '';
            var email = emailInput ? emailInput.value.trim() : '';
            var subject = subjectInput ? subjectInput.value.trim() : '';
            var message = messageInput ? messageInput.value.trim() : '';
            var honeypot = honeypotInput ? honeypotInput.value : '';

            if (!name || !email || !subject || !message) {
                if (errorBanner) {
                    errorBanner.textContent = 'Please fill out all required fields.';
                    errorBanner.style.display = 'block';
                }
                return;
            }

            var originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.innerHTML = 'Sending Inquiry...';

            fetch('/api/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    subject: subject,
                    message: message,
                    company_website: honeypot
                })
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { status: res.status, ok: res.ok, data: data };
                }).catch(function() {
                    return { status: res.status, ok: res.ok, data: { message: 'Server response error' } };
                });
            })
            .then(function(result) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '';
                submitBtn.style.cursor = 'pointer';
                submitBtn.innerHTML = originalBtnHtml;

                if (result.ok && result.data && result.data.status === 'success') {
                    formWrapper.style.display = 'none';
                    successWrapper.style.display = 'block';
                } else {
                    var errMsg = (result.data && result.data.message) ? result.data.message : 'Unable to submit your inquiry. Please try again.';
                    if (errorBanner) {
                        errorBanner.textContent = errMsg;
                        errorBanner.style.display = 'block';
                    }
                }
            })
            .catch(function(err) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '';
                submitBtn.style.cursor = 'pointer';
                submitBtn.innerHTML = originalBtnHtml;
                if (errorBanner) {
                    errorBanner.textContent = 'Network error connecting to the support server. Please check your connection or email support@pdftoexcel.in directly.';
                    errorBanner.style.display = 'block';
                }
            });
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (errorBanner) {
                errorBanner.style.display = 'none';
                errorBanner.textContent = '';
            }
            if (form) form.reset();
            if (successWrapper) successWrapper.style.display = 'none';
            if (formWrapper) formWrapper.style.display = 'block';
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
