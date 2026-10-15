# PD FTOEXCEL.IN — MASTER TEST & VERIFICATION MATRIX

**Target Website:** `https://pdftoexcel.in/`  
**Workspace:** `d:\pdftoexcel`  
**Control Directory:** `.project_control/`  

---

## 1. COMPLETED BASELINE & PREVIOUS PHASE TESTS

| Test ID | Category | Target Component | Manual Test Procedure & Criteria | Status | Evidence / Notes |
| :--- | :--- | :--- | :--- | :---: | :--- |
| **TEST-01** | Functionality | PDF to Excel Core | Upload native vector PDF, click Convert, inspect downloaded `.xlsx` in Excel. | **PASSED** | Extracted table rows with correct cell boundaries and values. |
| **TEST-02** | Functionality | Excel to PDF Fit All Columns | Upload 10-column `.xlsx` on `/excel-to-pdf-in-one-page`, click Convert, open downloaded PDF in Acrobat/Chrome. | **PASSED** | `%PDF-1.4` vector binary, single page, auto-scaled column widths, zero truncation. |
| **TEST-03** | Privacy | Conversion Telemetry | Upload file, inspect Network tab during conversion. | **PASSED** | Zero document binary payload sent to server. Only lightweight JSON telemetry (<500B) sent to `/api/convert.php`. |
| **TEST-04** | Security | API Rate Limiting | Send >30 requests/min to `/api/convert.php` and `/api/rate.php`. | **PASSED** | HTTP 429 Too Many Requests returned with `Retry-After: 60`. Self-pruning SQLite table. |
| **TEST-05** | Reliability | Support Form on `/contact` | Fill form, submit. Check network and SQLite database. | **PASSED** | HTTP 200 JSON response, entry inserted into `contact_inquiries`, bot honeypot traps spam. |
| **TEST-06** | Performance | Asset Loading | Inspect Network tab on non-tool routes (`/contact`, `/privacy-policy`). | **PASSED** | `pdf.min.js` and `xlsx.full.min.js` NOT loaded on non-tool pages. 1.22 MB bandwidth saved. |
| **TEST-07** | Routing | 301 Redirects & URL Cleanliness | Request `/index.php` and `/pages/word-to-excel.php`. | **PASSED** | HTTP 301 clean redirects to `/` and `/word-to-excel`. |
| **TEST-08** | SEO | Canonical URL Self-Referencing | Check all 25 public HTML outputs. | **PASSED** | Every page serves self-referential canonical tag matching its exact URL slug. |
| **TEST-09** | SEO | 404 Status on Retired May 2026 URLs | Request `/pricing`, `/blog`, `/terms-of-use`. | **PASSED** | HTTP 404 status code, `<meta name="robots" content="noindex, nofollow">`, zero canonical tag. |
| **TEST-10** | Schema | Structured Data Validation | Validate JSON-LD across homepage and tool pages. | **PASSED** | Valid WebApplication, BreadcrumbList, Organization, FAQPage. Zero fabricated aggregateRating stars. |
| **TEST-11** | SEO | In-Body Internal Links | Inspect body copy on `/multiple-pdf-to-excel-converter` and `/pdf-to-csv`. | **PASSED** | Contextual links to `/large-pdf-to-excel-converter` and `/word-to-excel` present and crawlable. |
| **TEST-12** | SEO | CTR Snippet Optimization | Check title and description on `/excel-to-pdf-in-one-page`. | **PASSED** | Title (57 chars) and description (153 chars) fit Google pixel budget without brand suffix. |
| **TEST-13** | Operational | Admin Command Center | Access `/admin`, authenticate with `admin123`. | **PASSED** | Metrics displayed: page impressions, unique visitor hashes, tool adoption, error diagnostics, contact inbox. |

---

## 2. ACTIVE CORRECTION TASKS TEST SUITE

| Test ID | Task ID | Target Component | Procedure & Acceptance Criteria | Current Status |
| :--- | :--- | :--- | :--- | :---: |
| **TEST-14** | `CORR-01` | Word `.docx` Table Extraction | Upload `.docx` containing multi-row/multi-column table. Convert to XLSX. Verify downloaded workbook contains real cells matching document table. | **PASSED** | Extracted 3x3 table with real cell values via native `extractRowsFromDocx`. |
| **TEST-15** | `CORR-01` | Word `.docx` Paragraph Content | Upload `.docx` containing text paragraphs without tables. Convert to XLSX. Verify paragraphs are converted to structured text rows. | **PASSED** | Successfully converts text runs and paragraphs with tab preservation. |
| **TEST-16** | `CORR-01` | Word Error Handling & No Dummy Fallback | Upload corrupted/empty file or unparseable binary. Verify honest error banner displayed; ZERO synthetic demo rows (`generateFallbackRows`) emitted. | **PASSED** | Displays clean UI error panel with "Try Another File"; zero dummy rows. |
| **TEST-17** | `CORR-01` | Word Unsupported Format Notice | Upload legacy binary `.doc` file. Verify user-friendly notice explaining that binary `.doc` requires saving as `.docx`. | **PASSED** | Identifies OLE2 header (`\xD0\xCF\x11\xE0`) and displays clear conversion advice. |
| **TEST-18** | `CORR-01` | Regression: Existing Converters | Test PDF-to-Excel, Excel-to-PDF, TXT-to-Excel converters. Verify zero regressions in core workflows. | **PASSED** | Vector PDF generator, CSV/TXT parsers, and protected pages 100% verified. |
| **TEST-19** | `CORR-02` | Password-Protected PDF Prompt | Upload password-encrypted PDF. Verify password prompt modal appears, valid password unlocks document, invalid password displays error. | **PASSED** | Verified onPassword intercept, secure modal prompt, memory zeroing, and zero telemetry exposure. |
| **TEST-20** | `CORR-03` | Scanned PDF / OCR Evaluation | Upload scanned image PDF. Verify detection of zero-text layer and clear user notification regarding OCR limits. | **PASSED** | Detects bitmap scans without digital text; displays honest diagnostic; zero dummy rows. |
| **TEST-21** | `CORR-04` | Large PDF Memory Protection | Upload multi-page large PDF (>20 pages). Verify progressive rendering and memory safety without browser freezing. | **PASSED** | 100MB limit guard, `page.cleanup()` memory release on every page, event loop yielding every 3 pages. |
| **TEST-22** | `CORR-05` | GA4 Telemetry Verification | Verify telemetry hooks operate smoothly; preserve readiness for Measurement ID without fabricating fake ID. | **BLOCKED (Client ID)** | Self-hosted zero-cookie analytics active on `/admin`; awaiting client `G-XXXXXXXXXX` tag. |
| **TEST-23** | `CORR-06` | Final SEO & Canonical Regression Gate | Verify protected assets (`/` and `/excel-to-pdf-in-one-page`) retain 100% exact titles, H1s, canonicals, and engine logic. | **PASSED** | Homepage & One-Page titles frozen; zero brand suffix on tool pages; 4 legal page suffixes preserved; all 25 routes HTTP 200. |
