# PD FTOEXCEL.IN — MASTER TASK LEDGER

**Current Status Standards:**  
Allowed statuses: `NOT_REVIEWED` | `VERIFIED` | `READY_FOR_APPROVAL` | `APPROVED` | `IN_PROGRESS` | `BLOCKED` | `VERIFICATION_FAILED` | `COMPLETED` | `CANCELLED`  
*Rule:* A task is NEVER marked `COMPLETED` until all code, verification tests, regression tests, and rollback procedures pass.

---

### TASK-00: Phase Zero Safety System Initialization
- **Audit Recommendation ID:** N/A (Project Governance)
- **Description:** Establish read-only safety system, control documents, Git audit baseline, and approval checkpoint.
- **Evidence & Source:** User prompt Phase Zero requirements; existing forensic SEO audit report.
- **Priority / Dependencies:** P0 (Prerequisite for all tasks) / None.
- **Affected Files / Routes:** `.project_control/*`, `AGENTS.md`.
- **Approval Status:** Ready for User Review.
- **Current Status:** `IN_PROGRESS` (Active Phase Zero).
- **Implementation Summary:** Created `.project_control/` with `.htaccess` deny rule, established `AGENTS.md`, `PROJECT_MASTER_PLAN.md`, `TASK_LEDGER.md`, `EXECUTION_STATE.md`, `RANKING_BASELINE.md`, `CHANGE_CONTROL.md`, `TEST_MATRIX.md`, and `CHANGELOG.md`.
- **Tests & Verification:** Verified `.htaccess` blocks public web serving; verified git working tree has no files reset or overwritten.
- **Ranking Risk:** None.
- **Rollback Requirements:** Delete `.project_control` and `AGENTS.md`.
- **Completion Evidence:** Pending user Phase Zero approval.

---

### TASK-01: Correct Corrupted Excel-to-PDF Conversion Output
- **Audit Recommendation ID:** `REC-01`
- **Description:** Replace SheetJS fake `.xlsx` rename with authentic client-side PDF table compilation so `/excel-to-pdf` and `/excel-to-pdf-in-one-page` produce genuine, readable PDF files that open in all PDF readers.
- **Evidence & Source:** `assets/js/converter.js` lines 201–232: SheetJS previously outputted `xlsx` binary wrapped in `Blob(type: 'application/pdf')` and downloaded as `.pdf`.
- **Priority / Dependencies:** P1 (Critical UX Blocker) / TASK-00.
- **Affected Files / Routes:** `assets/js/converter.js`, `/excel-to-pdf`, `/excel-to-pdf-in-one-page`, `/excel-to-pdf-landscape`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Implemented `compileTableToPdfBlob(fileName, rows, isLandscape)` in `assets/js/converter.js`. Compiles true vector ISO 32000-1 PDF (Helvetica/Helvetica-Bold fonts, brand emerald accent, navy headers, zebra stripes, auto-proportional column widths, and single-page row scaling). Handled `pdf_landscape` format extension.
- **Tests & Verification:** Tested vector PDF generation via Node.js TextEncoder test (3,843 bytes, `%PDF-1.4` header, valid xref table & trailer). Tested live in browser subagent on local server with 0 console errors.
- **Ranking Risk:** None (UX defect resolved).
- **Rollback Requirements:** Restore previous `converter.js`.
- **Completion Evidence:** Clean PDF generation output and zero console errors verified in live browser session.

---

### TASK-02: Eliminate Unnecessary Full-File Background Uploads
- **Audit Recommendation ID:** `REC-05`
- **Description:** Stop `converter.js` from uploading raw user files to `/api/convert.php` in the background after client conversion completes. Transmit only lightweight JSON telemetry (`{ tool_id, format, file_size }`).
- **Evidence & Source:** `assets/js/converter.js` line 242 previously sent multi-megabyte user documents to server via multipart `FormData`.
- **Priority / Dependencies:** P1 (Privacy & Bandwidth) / TASK-00.
- **Affected Files / Routes:** `assets/js/converter.js`, `api/convert.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Replaced `formData.append('file', file)` in `assets/js/converter.js` with JSON telemetry payload `{ tool_id, format, file_size }`. Updated `api/convert.php` to decode JSON payload, sanitize tool and format inputs, and log conversion metrics to SQLite without receiving raw document files.
- **Tests & Verification:** Verified HTTP POST to `/api/convert.php` with JSON payload returns HTTP 200 `{"status":"success","message":"Conversion logged successfully"}`. Zero raw file binary transmitted over network.
- **Ranking Risk:** None.
- **Rollback Requirements:** Revert `converter.js` and `api/convert.php`.
- **Completion Evidence:** Verified HTTP 200 JSON telemetry response on local server.

---

### TASK-03: Secure API Endpoints & Add Abuse Throttling
- **Audit Recommendation ID:** `REC-09`
- **Description:** Add payload sanitization, MIME-type checks, and client IP rate limiting on `/api/convert.php` and `/api/rate.php` to prevent DOS attacks and temp storage exhaustion.
- **Evidence & Source:** `api/convert.php` and `api/rate.php` previously had no rate limiting or input sanitization.
- **Priority / Dependencies:** P2 (Security) / TASK-02.
- **Affected Files / Routes:** `api/convert.php`, `api/rate.php`, `includes/db.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Added `rate_limits` table in `includes/db.php` with self-cleaning index. Implemented `Database::checkRateLimit($action, $maxRequests, $windowSeconds)`. Enforced 60 conversions/min limit on `api/convert.php` and 15 ratings/min on `api/rate.php`. Returns HTTP 429 Too Many Requests when threshold exceeded.
- **Tests & Verification:** Sent 20 sequential POST requests to `/api/rate.php`: successfully hit rate limit on request #15 with HTTP 429 Too Many Requests. Verified database indexing and parameter binding.
- **Ranking Risk:** None.
- **Rollback Requirements:** Revert `api/*.php` and `includes/db.php`.
- **Completion Evidence:** Verified HTTP 429 response on request #15 during live HTTP test.

---

### TASK-04 (CORR-01): Upgrade Mock Word/Image/OCR Fallback Handlers
- **Audit Recommendation ID:** `REC-10`
- **Description:** Replace synthetic demo spreadsheet output with genuine client-side `.docx` table extraction on `/word-to-excel`. For documents that cannot be parsed, return an honest user-facing error instead of a fake 4-row template.
- **Evidence & Source:** `assets/js/converter.js` previously emitted generic placeholder data (`generateFallbackRows()`) whenever a non-PDF/TXT file was processed.
- **Priority / Dependencies:** P1 (Critical Functionality) / None.
- **Affected Files / Routes:** `assets/js/converter.js`, `includes/converter-tool.php`, `pages/word-to-excel.php`.
- **Approval Status:** Authorized by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:**
  1. Implemented native browser-side OpenXML `.docx` extractor (`extractRowsFromDocx`) in `assets/js/converter.js`.
  2. Implemented native ZIP stream extraction (`extractZipFile`, `extractFromLocalHeaders`, `decompressDeflateRaw`) reading `word/document.xml` using `DecompressionStream('deflate-raw')` and `DOMParser` with zero external dependencies.
  3. Extracts full tabular grids (`<w:tbl>`, `<w:tr>`, `<w:tc>`) with `gridSpan` column alignment and multi-paragraph cell handling.
  4. Predictably extracts documents without tables by parsing paragraphs (`<w:p>`) and tab stops (`<w:tab>`).
  5. Completely eliminated `generateFallbackRows()` from `converter.js`.
  6. Added `errorPanel` in `includes/converter-tool.php` and `showError()` in `converter.js` to display honest error states with a "Try Another File" reset button.
  7. Added legacy Word 97-2003 `.doc` binary header detection (`\xD0\xCF\x11\xE0`) providing clear guidance to save as `.docx`.
  8. Clarified Word OpenXML FAQ on `pages/word-to-excel.php`.
- **Tests & Verification:**
  - Automated unit test suite (`scratch/test_word_to_excel.js`): Verified removal of `generateFallbackRows`, successful ZIP stream extraction of `word/document.xml`, legacy `.doc` header detection, and corrupted file error handling.
  - Regression testing (`scratch/test_node_pdf.js`): Verified Excel-to-PDF single-page fit vector compiler `%PDF-1.4` output intact.
  - Protected asset validation: Confirmed Homepage (`/`) and `/excel-to-pdf-in-one-page` titles, H1s, and canonical URLs remain 100% frozen and unmodified.
  - Brand suffix verification: Confirmed legal/support page titles retain brand suffix, zero automatic brand suffix on tool pages.
- **Ranking Risk:** None (Improves user trust on `/word-to-excel`; preserves URL, canonical, title).
- **Rollback Requirements:** Revert `assets/js/converter.js`, `includes/converter-tool.php`, and `pages/word-to-excel.php`.
- **Completion Evidence:** Passing verification suite `scratch/test_word_to_excel.js`, zero dummy fallback rows, clean UI error panel state.

---

### TASK-05: Connect Support Form on `/contact`
- **Audit Recommendation ID:** `REC-03`
- **Description:** Replace dummy `onsubmit="event.preventDefault()"` handler with an actual backend handler / database queue so customer inquiries and bug reports are captured.
- **Evidence & Source:** `pages/contact.php` line 55: inline JavaScript previously displayed a fake success banner without sending data anywhere. User inquiries were lost.
- **Priority / Dependencies:** P1 (Operational Reliability) / TASK-00.
- **Affected Files / Routes:** `pages/contact.php`, `api/contact.php`, `includes/db.php`, `router.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Created `contact_inquiries` SQLite table with indexing on `created_at`. Added `Database::logContactInquiry($name, $email, $subject, $message)`. Created secure JSON endpoint `api/contact.php` featuring POST enforcement, rate limiting (5 req / 5 min / IP), honeypot anti-spam trap (`company_website`), string sanitization, and input length checks. Connected `pages/contact.php` via async `fetch('/api/contact')` with loading indicator, inline error banner, success modal, and form reset. Added route in `router.php`.
- **Tests & Verification:** Tested via `test_phase2_contact.php` (CLI schema & DB persistence) and `test_contact_http.py` (live HTTP tests): confirmed HTTP 400 on invalid email/missing fields, silent trap on bot honeypot, HTTP 200 on valid submission with SQLite insertion, and HTTP 429 upon reaching rate limit threshold. Zero console errors on `/contact`.
- **Ranking Risk:** None (Operational support fix; URLs, canonicals, titles, and layout preserved).
- **Rollback Requirements:** Revert `pages/contact.php`, delete `api/contact.php`, remove method in `includes/db.php`.
- **Completion Evidence:** Verified SQLite record insertion (`contact_inquiries` ID 1, 2) and passing automated HTTP integration test suite.

---

### TASK-06: Conditionally Enqueue Vendor JavaScript on Non-Tool Pages
- **Audit Recommendation ID:** `REC-04`
- **Description:** Wrap `pdf.min.js`, `xlsx.full.min.js`, and `converter.js` (1.22 MB uncompressed) in `footer.php` so they load only on pages with an interactive converter card (`!empty($toolConfig)`).
- **Evidence & Source:** `includes/footer.php` lines 84–89 previously unconditionally injected heavy document engines on `/contact`, `/terms`, `/privacy-policy`, `/security`, and 404 pages.
- **Priority / Dependencies:** P2 (Core Web Vitals & Performance) / TASK-01.
- **Affected Files / Routes:** `includes/footer.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Wrapped `pdf.min.js`, `xlsx.full.min.js`, and `converter.js` in `<?php if (!empty($toolConfig)): ?>` in `includes/footer.php`. Preserved mobile navigation toggle script across all site pages. Eliminates 1.22 MB uncompressed JS footprint from all non-tool and error pages.
- **Tests & Verification:** Tested via automated HTTP test suite (`test_vendor_enqueue.py`): verified all 6 primary tool pages (`/`, `/excel-to-pdf`, `/excel-to-pdf-in-one-page`, `/bank-statement-pdf-to-excel`, `/multiple-pdf-to-excel-converter`, `/pdf-to-csv`) enqueue all required vendor engines and `converter.js`. Verified all non-tool pages (`/contact`, `/privacy-policy`, `/terms`, `/security`) and 404 pages completely exclude vendor scripts while preserving mobile toggle functionality.
- **Ranking Risk:** None (Pure performance enhancement; improves Core Web Vitals, mobile LCP, and Total Blocking Time).
- **Rollback Requirements:** Revert conditional wrapper in `footer.php`.
- **Completion Evidence:** Passing automated test suite `test_vendor_enqueue.py` confirming 0 KB vendor JS on non-tool routes.

---

### TASK-07: Implement Privacy-First Visitor Analytics & Admin Command Center
- **Audit Recommendation ID:** `REC-02` (Fulfills Phase 4 per User Direction)
- **Description:** Implement a self-hosted, privacy-first analytics and conversion tracking system with an executive Admin Dashboard (`/admin`) to monitor:
  1. Total visitors, unique IP hash counts, page impressions, and daily traffic charts.
  2. Page visit tracking (which routes users visit and their relative engagement share).
  3. Tool adoption rates (visitors who land on a tool page vs. who trigger an interactive conversion).
  4. Conversion health (successful vs. failed conversions, output formats, file sizes, and error diagnostics).
  5. Customer inquiries inbox capturing support messages from `/contact`.
- **Evidence & Source:** User request: "Phase 4 do like make admin deshboard where i can see how much user come in website and i want to see data whcih page they go and tool they use or not and they succes or not like thise data". Zero third-party trackers or external cookies required; 100% self-hosted on SQLite.
- **Priority / Dependencies:** P1 (Analytics & Conversion Visibility) / TASK-00.
- **Affected Files / Routes:** `includes/db.php`, `api/convert.php`, `assets/js/converter.js`, `router.php`, `robots.txt`, `pages/admin.php`.
- **Approval Status:** Approved and directed by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:**
  1. **SQLite Analytics Schema (`includes/db.php`):** Created `page_views` table (indexed by `page_url`, `created_at`, `ip_hash`) with automatic user agent device type detection (`mobile`, `desktop`, `tablet`). Updated `conversion_stats` table schema with safe column additions (`format`, `status`, `error_msg`).
  2. **Reporting Database Methods (`includes/db.php`):** Added `Database::logPageView()`, updated `Database::logConversion()`, `Database::clearRateLimit()`, and built comprehensive query aggregators: `getDashboardMetrics()`, `getTopPages()`, `getToolFunnel()`, `getDailyTrafficSeries()`, `getRecentConversions()`, and `getRecentInquiries()`.
  3. **Real-Time Client Telemetry (`assets/js/converter.js` & `api/convert.php`):** Transmits lightweight telemetry on conversion completion with `status: 'success'` or on exception fallback with `status: 'error'` and error message.
  4. **Clean Router & Security Gate (`router.php` & `robots.txt`):** Added `Disallow: /admin` in `robots.txt`. Routed `/admin` and `/admin.php` to `pages/admin.php`. Integrated automatic `Database::logPageView($requestUri)` in `router.php` for all public page visits.
  5. **Executive Admin Dashboard (`pages/admin.php`):** Built glassmorphic dark-mode command center with password authentication gate (`admin123`), brute-force rate limit protection, time range filtering (Today, 7 Days, 30 Days, All Time), responsive SVG daily trend charts, top visited pages breakdown, tool funnel adoption matrix, real-time conversion stream, and support inquiries inbox.
- **Tests & Verification:** Tested via `scratch/test_admin_dashboard.py` (CLI / HTTP integration suite) and browser subagent (`admin_dashboard_preview_1791867042389.webp`):
  - Robots.txt disallow check passed.
  - Page visit tracking across public routes passed.
  - Success and error telemetry logging to `api/convert.php` passed.
  - Admin login gate with `<meta name="robots" content="noindex, nofollow">` passed.
  - Master passcode validation and rate limit reset passed.
  - SVG daily traffic series, top pages table, tool funnels, conversion stream, and inquiries passed.
  - Logout session cleanup passed.
- **Ranking Risk:** None (Protected with `noindex, nofollow` and `Disallow: /admin`; zero client-side performance drag on public visitors; zero third-party cookies).
- **Rollback Requirements:** Revert `router.php`, `includes/db.php`, `api/convert.php`, `assets/js/converter.js`, and delete `pages/admin.php`.
- **Completion Evidence:** Verified SQLite record insertion and visual inspection via browser subagent recording `admin_dashboard_preview_1791867042389.webp`.

---

### TASK-08: Validate 404 Behavior for Dead May 2026 URLs
- **Audit Recommendation ID:** `REC-07`
- **Description:** Ensure `/pricing`, `/blog`, `/terms-of-use`, `/api-documentation`, `/register`, and `/blog/my-first-post` consistently respond with HTTP 404 and `noindex, nofollow` to avoid soft-404 dilution.
- **Evidence & Source:** `searchconsol/https___pdftoexcel.in_-Coverage-Drilldown-2026-10-12.xlsx`: 6 of 8 non-indexed URLs are obsolete May 2026 pages.
- **Priority / Dependencies:** P3 (Crawl Hygiene) / TASK-00.
- **Affected Files / Routes:** `router.php`, `.htaccess`, `includes/header.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Verified that `router.php` strictly returns genuine HTTP 404 for all 6 obsolete URLs, setting `$is404 = true` which enforces `<meta name="robots" content="noindex, nofollow">`, omits canonical tags, suppresses WebApplication JSON-LD schemas, and skips converter vendor JS injection. No soft-404 redirects to the homepage occur.
- **Tests & Verification:** Automated test suite `test_phase5_seo.py` confirmed 100% HTTP 404 response codes, presence of `noindex, nofollow`, zero canonical tags, and zero structured data across all 6 dead routes.
- **Ranking Risk:** None (Safeguards domain authority from soft-404 dilution).
- **Rollback Requirements:** None.
- **Completion Evidence:** Verified automated test log confirming clean HTTP 404 + noindex on all 6 targets.

---

### TASK-09: Strengthen Crawl Discovery for Unindexed Tool Pages
- **Audit Recommendation ID:** `REC-08`
- **Description:** Add contextual, high-relevance body cross-links from top-traffic pages (`/multiple-pdf-to-excel-converter`, `/pdf-to-csv`) to `/large-pdf-to-excel-converter` and `/word-to-excel`.
- **Evidence & Source:** GSC Coverage report flags `/large-pdf-to-excel-converter` and `/word-to-excel` as "Crawled - currently not indexed" since Sept 16–17.
- **Priority / Dependencies:** P3 (Indexing Equity) / TASK-01.
- **Affected Files / Routes:** `pages/multiple-pdf-to-excel-converter.php`, `pages/pdf-to-csv.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Integrated contextual, in-body editorial internal links:
  - In `pages/multiple-pdf-to-excel-converter.php`: Added contextual anchor links to `/large-pdf-to-excel-converter` (for files >50MB) and `/word-to-excel` (for DOCX table extraction).
  - In `pages/pdf-to-csv.php`: Added contextual anchor links to `/word-to-excel` and `/large-pdf-to-excel-converter`. Replaced legacy broken anchor `/pdf-to-excel-converter-free` with high-capacity `/large-pdf-to-excel-converter`.
- **Tests & Verification:** Verified via `test_phase5_seo.py`: all 4 cross-links present in rendered HTML; both destination URLs respond with HTTP 200, clean canonical tags, and `index, follow` directives.
- **Ranking Risk:** Low (Enhances topical crawl graph and internal equity distribution).
- **Rollback Requirements:** Revert additions in `pages/multiple-pdf-to-excel-converter.php` and `pages/pdf-to-csv.php`.
- **Completion Evidence:** Passing automated test suite `test_phase5_seo.py` validating 100% internal link accessibility.

---

### TASK-10: Refine CTR Snippet on `/excel-to-pdf-in-one-page`
- **Audit Recommendation ID:** `REC-06`
- **Description:** Refine title tag and meta description for `/excel-to-pdf-in-one-page` to target exact high-impression queries (`excel to pdf in one page`, `convert excel to pdf in one page`) emphasizing single-page scaling and zero table splits.
- **Evidence & Source:** GSC 28-day data: 1,042 impressions with 4.80% CTR; exact query `excel to pdf in one page` had 117 impressions with only 1 click (0.85% CTR).
- **Priority / Dependencies:** P2 (Organic Growth) / TASK-01 (Must fix corrupted PDF download first).
- **Affected Files / Routes:** `pages/excel-to-pdf-in-one-page.php`.
- **Approval Status:** Approved by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:** Refined `$pageTitle` to `"Convert Excel to PDF in One Page - Fit All Columns (Free)"` (57 chars) and `$pageDesc` to `"Convert Excel to PDF in one page free. Automatically fit all spreadsheet columns on 1 single page with zero table splits or cutoffs. 100% private & fast."` (153 chars). Front-loaded high-impression exact match search queries while strictly preserving canonical URL (`https://pdftoexcel.in/excel-to-pdf-in-one-page`), primary keyword, and H1 structure.
- **Tests & Verification:** Tested via automated script `test_phase6_ctr.py`: confirmed title tag (57 chars, within 50-60 sweet spot), meta description (153 chars, within 140-160 sweet spot), canonical tag unchanged, H1 tag unchanged, robots tag `index, follow` intact, and syntax check `php -l` passed.
- **Ranking Risk:** Low (Strictly preserves primary keyword and URL identity while aligning snippet with user intent).
- **Rollback Requirements:** Restore previous metadata variables in `pages/excel-to-pdf-in-one-page.php`.
- **Completion Evidence:** Verified automated HTTP snippet test output (`test_phase6_ctr.py`).

---

### TASK-12 (CORR-02): Interactive Password-Protected PDF Decryption Prompt
- **Audit Recommendation ID:** `TASK-12` / `CORR-02`
- **Description:** Implement client-side password prompt modal dialog for encrypted PDFs, intercepting PDF.js `onPassword` callbacks to allow users to unlock secured files and extract tables.
- **Evidence & Source:** Section 9 of Project Master Execution Prompt: previously encrypted PDFs failed with uncaught `PasswordException` without prompting user for document password.
- **Priority / Dependencies:** P1 (Functional Integrity) / TASK-00.
- **Affected Files / Routes:** `assets/js/converter.js`, `includes/converter-tool.php`, `pages/convert-password-protected-pdf-to-excel.php`.
- **Approval Status:** Authorized by User.
- **Current Status:** `COMPLETED`.
- **Implementation Summary:**
  1. Added `passwordModalPanel` in `includes/converter-tool.php` featuring document password input, clear decryption instructions, inline error banner, and Cancel / "Unlock & Convert" action buttons.
  2. Integrated `loadingTask.onPassword = (callback, reason) => promptPdfPassword(callback, reason)` in `assets/js/converter.js`.
  3. Built `promptPdfPassword()` in `assets/js/converter.js`: handles both initial `NEED_PASSWORD` and `INCORRECT_PASSWORD` states.
  4. Supports Enter and Escape keyboard shortcuts for seamless usability.
  5. **Strict Security & Privacy Enforcement:** Document passwords are processed exclusively in volatile browser RAM to decrypt the document stream and are NEVER serialized, logged, transmitted over the network, or saved to databases. Input value is immediately zeroed from memory upon submission or cancellation.
  6. Cancellation safely cleans up listeners, aborts the task, and displays an honest cancellation notice without throwing unhandled exceptions.
- **Tests & Verification:**
  - Automated test suite `scratch/test_password_pdf.js`: Verified `loadingTask.onPassword` attachment, DOM element existence in `converter-tool.php`, memory zeroing of password input, and zero password transmission in telemetry payloads.
  - Regression testing (`test_node_pdf.js`, `test_word_to_excel.js`): All passed.
  - Protected URL checks: Homepage and `/excel-to-pdf-in-one-page` verified 100% frozen.
- **Ranking Risk:** None (Provides working functionality for `/convert-password-protected-pdf-to-excel`; preserves URL, canonical, title, and schema).
- **Rollback Requirements:** Revert `assets/js/converter.js` and `includes/converter-tool.php`.
- **Completion Evidence:** Passing automated test suite `scratch/test_password_pdf.js` validating client-side modal decryption flow.

