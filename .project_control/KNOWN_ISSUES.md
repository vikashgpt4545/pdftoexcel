# PD FTOEXCEL.IN — KNOWN DEFECTS & UNRESOLVED ISSUES

**Target Domain:** `https://pdftoexcel.in/`  
**Last Updated:** October 14, 2026

---

## 1. RESOLVED ISSUES REGISTRY

1. **ISSUE-01 (Word-to-Excel Fallback Generation):**
   - **Resolution:** `CORR-01` (`TASK-04`) `COMPLETED`. Native client-side OpenXML `.docx` table and paragraph parser implemented using `DecompressionStream('deflate-raw')` and `DOMParser`. `generateFallbackRows()` completely purged. Legacy Word `.doc` files detected with actionable guidance.

2. **ISSUE-02 (Encrypted PDF Decryption Modal Absence):**
   - **Resolution:** `CORR-02` (`TASK-12`) `COMPLETED`. Interactive `passwordModalPanel` implemented in `includes/converter-tool.php` and connected to `loadingTask.onPassword` in `assets/js/converter.js`. Passwords remain in temporary volatile RAM, are zeroed immediately, and are never logged or transmitted over the network.

3. **ISSUE-03 (Scanned PDF & Image OCR Evaluation):**
   - **Resolution:** `CORR-03` (`TASK-13`) `COMPLETED`. Full architectural evaluation conducted: shipping a 15–20 MB client-side Tesseract.js WebAssembly payload risks immediate mobile browser tab OOM crashes for the target audience. Implemented transparent, honest user notifications in `assets/js/converter.js` for scanned PDFs (detecting empty text layers) and image files (`.jpg`, `.png`, `.webp`), directing users to upload vector documents. ZERO synthetic fake spreadsheets are generated.

4. **ISSUE-04 (Large PDF Main-Thread Memory Load):**
   - **Resolution:** `CORR-04` (`TASK-14` / `TASK-18`) `COMPLETED`. Added a 100MB file boundary guard, explicit `page.cleanup()` invocation to free canvas memory on every page, and periodic event loop yielding every 3 pages (`setTimeout(r, 0)`) to maintain UI responsiveness and prevent browser freezes.

---

## 2. EXTERNAL DEPENDENCIES & BLOCKED ITEMS

1. **BLOCK-01 (Third-Party Google Analytics 4 Measurement ID):**
   - **Dependency:** Client must provide a valid Google Analytics 4 Measurement ID (`G-XXXXXXXXXX`).
   - **Current State:** Awaiting client credential. No fake or placeholder ID has been fabricated per Rule D.
   - **Mitigation:** Self-hosted zero-cookie visitor analytics and command center (`/admin`) is 100% active on SQLite, recording unique visitor IP hashes, page impressions, tool conversions, device types, and diagnostic error telemetry.

---

## 3. SEO ASSET PRESERVATION STATE

- **Homepage (`/`):** Title (`Convert PDF to Excel 100% Free Online - No Login`), H1, canonical URL, and vector PDF conversion engine remain 100% frozen.
- **`/excel-to-pdf-in-one-page`:** Title (`Convert Excel to PDF in One Page - Fit All Columns (Free)`), H1, canonical URL, and ISO 32000-1 auto-scaling vector PDF engine remain 100% frozen.
- **Title Brand Suffix Policy:** 0 of 21 tool routes have brand suffixes. All 4 legal/support pages (`/contact`, `/privacy-policy`, `/security`, `/terms`) strictly preserve their brand suffixes (`| PDFtoExcel.in`).
