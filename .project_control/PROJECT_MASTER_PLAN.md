# PD FTOEXCEL.IN — PROJECT MASTER PLAN & SCOPE BASELINE

**Target Domain:** `https://pdftoexcel.in/`  
**Master Source Document:** Forensic SEO Audit & Post-Implementation Forensic Report (October 13, 2026)  
**Execution Mode:** Manual, Sequential, Evidence-Based, Safety-First  
**Last Updated:** October 13, 2026

---

## 1. PROJECT OBJECTIVES

1. **Safeguard established organic rankings:** Protect Homepage (#2.8 for `convert pdf to excel`, 64 clicks) and `/excel-to-pdf-in-one-page` (#9.0 for `excel to pdf in one page`, 50 clicks, 1,042 imp).
2. **Resolve remaining functional conversion defects:**
   - Implement genuine Word `.docx` table extraction on `/word-to-excel` (eliminate dummy fallback spreadsheet).
   - Implement interactive password prompt modal for encrypted PDFs on `/convert-password-protected-pdf-to-excel`.
   - Evaluate optical character recognition (OCR) engine for scanned files and images.
3. **Eliminate user trust erosion:** Never emit synthetic fallback demo rows when real conversion fails; show clear, honest error diagnostics.
4. **Maintain measurement & privacy integrity:** Self-hosted zero-cookie telemetry operational; integrate GA4 tag once Measurement ID is provided.
5. **Protect title policy:** Never append brand suffixes automatically; protect ranking pages.

---

## 2. AUDIT RECOMMENDATION MATRIX (REC-01 to REC-10)

| Audit ID | Task ID | Scope Summary | Target Files | Verified Current State | Next Action |
| :--- | :---: | :--- | :--- | :---: | :--- |
| **REC-01** | `TASK-01` | Authentic Excel-to-PDF vector generation | `converter.js`, `footer.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-02** | `TASK-07` | Privacy-first analytics & conversion tracking | `pages/admin.php`, `header.php` | `PARTIALLY_WORKING` | Admin dashboard working; GA4 tag blocked on user ID. |
| **REC-03** | `TASK-05` | Connected contact support queue | `pages/contact.php`, `api/contact.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-04** | `TASK-06` | Conditionally load vendor JavaScript | `includes/footer.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-05** | `TASK-02` | Terminate redundant full-file uploads | `converter.js`, `api/convert.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-06** | `TASK-10` | CTR snippet refinement on `/excel-to-pdf-in-one-page` | `pages/excel-to-pdf-in-one-page.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-07** | `TASK-08` | Genuine 404 behavior for dead May 2026 URLs | `router.php`, `.htaccess` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-08** | `TASK-09` | In-body contextual cross-links | `multiple...`, `pdf-to-csv` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-09** | `TASK-03` | API brute-force and abuse rate limiting | `api/*.php`, `includes/db.php` | `VERIFIED_WORKING` | Preserve without changes. |
| **REC-10** | `TASK-04` | Upgrade mock image/Word/OCR fallback handlers | `converter.js`, `pages/word-to-excel.php` | `NOT_IMPLEMENTED` | **ACTIVE PRIORITY: Implement CORR-01 (Word .docx extraction).** |

---

## 3. OUTSTANDING TASK SEQUENCE

1. **CORR-01 (TASK-04 / REC-10): Genuine Word `.docx` Table Extraction**
   - Eliminate synthetic demo spreadsheet output on `/word-to-excel`.
   - Parse `.docx` XML structure in browser (`word/document.xml`), extract tables and paragraphs into spreadsheet rows, show clear error on failure.
2. **CORR-02 (TASK-12): Password-Protected PDF Prompt Dialog**
   - Intercept PDF.js password exception and render interactive decryption modal in UI.
3. **CORR-03 (REC-02): GA4 Tag Integration (BLOCKED on Measurement ID)**
   - Insert GA4 script block in `header.php` once client provides `G-XXXXXXXXXX`.
4. **CORR-04 (TASK-13): Optical Character Recognition (OCR) Engine Evaluation**
   - Profile performance and bundle size for client-side image OCR.
5. **CORR-05 (Policy): Brand Suffix Review on 4 Support/Legal Pages**
   - Retain current titles until explicit user authorization.
6. **CORR-06 (TASK-18): Large-PDF WebWorker Chunked Streaming**
   - Offload heavy multi-page processing from main UI thread.
