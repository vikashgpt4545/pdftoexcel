    <footer class="site-footer">
        <div class="container footer-top">
            <div class="footer-brand">
                <a href="/" class="site-logo">
                    <span class="logo-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </span>
                    <span class="logo-text">PDFtoExcel<span class="tld">.in</span></span>
                </a>
                <p class="brand-desc">
                    The fastest, most accurate 100% free online PDF to Excel converter. Seamlessly extract complex financial statements, tabular data, and scanned documents into editable Microsoft Excel spreadsheets without login or software installation.
                </p>
                <div class="trust-indicators">
                    <span class="trust-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> 256-Bit SSL Encrypted</span>
                    <span class="trust-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Auto-Deleted in 1 Hour</span>
                    <span class="trust-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg> No Login or Signup</span>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Top Converters</h4>
                <ul class="footer-links">
                    <li><a href="/">PDF to Excel</a></li>
                    <li><a href="/excel-to-pdf">Excel to PDF</a></li>
                    <li><a href="/bank-statement-pdf-to-excel">Bank Statement PDF to Excel</a></li>
                    <li><a href="/pdf-invoice-to-excel">PDF Invoice to Excel</a></li>
                    <li><a href="/pdf-to-csv">PDF to CSV</a></li>
                    <li><a href="/word-to-excel">Word to Excel</a></li>
                    <li><a href="/txt-to-excel">TXT to Excel</a></li>
                    <li><a href="/image-to-excel-converter-online">Image to Excel</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Specialized Tools</h4>
                <ul class="footer-links">
                    <li><a href="/pdf-to-excel-ocr">Scanned PDF to Excel (OCR)</a></li>
                    <li><a href="/pdf-to-excel-converter-ai">AI PDF to Excel</a></li>
                    <li><a href="/pdf-to-google-sheets">PDF to Google Sheets</a></li>
                    <li><a href="/multiple-pdf-to-excel-converter">Multiple PDF to Excel (Batch)</a></li>
                    <li><a href="/large-pdf-to-excel-converter">Large PDF to Excel (50MB+)</a></li>
                    <li><a href="/convert-password-protected-pdf-to-excel">Password Protected PDF</a></li>
                    <li><a href="/excel-to-pdf-in-one-page">Excel to PDF (One Page)</a></li>
                    <li><a href="/excel-to-pdf-landscape">Excel to PDF Landscape</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">Format & Guides</h4>
                <ul class="footer-links">
                    <li><a href="/pdf-to-excel-converter-online-free-without-changing-format">Keep Original Format</a></li>
                    <li><a href="/pdf-to-excel-converter-online-free-without-email">No Email Required</a></li>
                    <li><a href="/how-to-insert-pdf-in-excel">How to Insert PDF in Excel</a></li>
                    <li><a href="/how-to-convert-pdf-to-excel-in-mobile">Convert PDF on Mobile</a></li>
                    <li><a href="/pdf-to-excel-converter-free-download">Desktop Software Download</a></li>
                    <li><a href="/privacy-policy">Privacy & Data Security</a></li>
                    <li><a href="/terms">Terms of Service</a></li>
                    <li><a href="/contact">Support & Contact</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <p class="copyright">© <?= date('Y') ?> PDFtoExcel.in. All rights reserved. 100% Free Online Document Tools.</p>
                <div class="footer-legal">
                    <a href="/privacy-policy" style="color: inherit; text-decoration: none;">Privacy Policy</a>
                    <span>·</span>
                    <a href="/terms" style="color: inherit; text-decoration: none;">Terms of Service</a>
                    <span>·</span>
                    <a href="/security" style="color: inherit; text-decoration: none;">Security Whitepaper</a>
                    <span>·</span>
                    <a href="/contact" style="color: inherit; text-decoration: none;">Contact Us</a>
                    <span>·</span>
                    <a href="/sitemap.xml" style="color: inherit; text-decoration: none;">XML Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <?php if (!empty($toolConfig)): ?>
    <!-- High-Performance Client Document Engines -->
    <script src="/assets/js/vendor/pdf.min.js" defer></script>
    <script src="/assets/js/vendor/xlsx.full.min.js" defer></script>
    <!-- Universal Converter JavaScript -->
    <script src="/assets/js/converter.js" defer></script>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobileToggle');
            const nav = document.getElementById('siteNav');
            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    nav.classList.toggle('nav-open');
                    toggle.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
