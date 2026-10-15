# PD FTOEXCEL.IN — CHANGE CONTROL & APPROVAL POLICY

**Target Website:** `https://pdftoexcel.in/`  
**Workspace:** `d:\pdftoexcel`  
**Control Directory:** `.project_control/`  

---

## 1. CHANGE CLASSIFICATION TIERS

Every proposed modification must be strictly categorized into one of three tiers before any editor tool is opened:

### Tier 1: Within Explicitly Approved Scope (Authorized for Sequential Execution)
Modifications that directly resolve an approved recommendation or task in `TASK_LEDGER.md` using minimal diffs on designated files:
- **`CORR-01` (`TASK-04`):** Word-to-Excel `.docx` genuine table/text parsing in `assets/js/converter.js` and `pages/word-to-excel.php`. Elimination of `generateFallbackRows()` dummy data.
- **`CORR-02` (`TASK-12`):** Client-side password decryption modal prompt in `assets/js/converter.js` upon PDF.js password requirement.
- **`CORR-03` (`TASK-13`):** Scanned PDF detection, OCR evaluation and transparent user notification in `assets/js/converter.js`.
- **`CORR-04` (`TASK-14`):** Large PDF memory protection, page-chunked streaming and progress estimation.
- **`CORR-05` (`TASK-15`):** GA4 telemetry integration stubbing (strictly waiting on user Measurement ID; zero fake ID generation).
- **`CORR-06` (`TASK-16` / `TASK-18`):** Verified internal crawl discovery enhancements and search intent preservation.

### Tier 2: Requires Explicit User Approval (Halt & Request Authorization)
Modifications that alter site-wide behavior, templates, or high-sensitivity assets:
1. Any modification to shared SEO header templates (`includes/header.php`, `includes/schema.php`).
2. Any metadata adjustments to pages other than the specific task scope.
3. Any alteration to the brand suffix on the four legal/support pages (`/contact`, `/privacy-policy`, `/security`, `/terms`).
4. Any change to public URL slugs, routing hierarchies, 301 redirects, or Apache rewrite rules.
5. Any change to SQLite schema tables (`data/app.sqlite`) affecting persisted tables.
6. Any third-party script, analytics tracking code, or external CDN dependency injection.

### Tier 3: Forbidden by Safety Policy (ABSOLUTELY PROHIBITED)
Actions that are strictly forbidden under all circumstances:
1. **Destructive Git commands:** `git reset --hard`, `git clean`, `git checkout -- .`, force-pushing, discarding uncommitted user changes.
2. **Automated scripts or bulk tools:** Python, PowerShell, Bash, Node.js, or PHP CLI scripts to modify project files.
3. **Mass find-and-replace:** Editing multiple files simultaneously using automated patterns.
4. **Altering Protected SEO Assets:**
   - Homepage (`/`) and `/excel-to-pdf-in-one-page`:
     - Titles or meta descriptions.
     - H1 headings.
     - URLs or canonical URLs.
     - Primary search intent or existing content.
     - Core conversion engines or essential functionality.
5. **Automatic Brand Suffixes:** Never append `| PDFtoExcel.in`, `- PDFtoExcel.in`, or any brand suffix automatically to page titles.
6. **Fabrication of Data:** Never fabricate test results, fake visitor numbers, fake reviews, or claim indexing guarantees.
7. **Redesigning the Website:** No visual redesigns, branding changes, CSS overhaul, or layout restructuring.

---

## 2. PROTECTED ASSET REGISTRY

| Asset URL | Target Query Focus | Protected Elements | Policy |
| :--- | :--- | :--- | :--- |
| `https://pdftoexcel.in/` | `convert pdf to excel` (Pos 2.8, 719 imp) | Title: `Convert PDF to Excel 100% Free Online - No Login`<br>H1: `Convert PDF to Excel Free Online - 100% Secure & Private`<br>Canonical: `https://pdftoexcel.in/`<br>Converter Engine: Client-side vector PDF-to-Excel | **FROZEN:** No metadata, heading, URL, or engine modifications permitted. |
| `https://pdftoexcel.in/excel-to-pdf-in-one-page` | `convert excel to pdf in one page` (Pos 9.0, 1,042 imp) | Title: `Convert Excel to PDF in One Page - Fit All Columns (Free)`<br>H1: `Convert Excel to PDF in One Page`<br>Canonical: `https://pdftoexcel.in/excel-to-pdf-in-one-page`<br>Converter Engine: ISO 32000-1 auto-fit vector PDF compiler | **FROZEN:** No metadata, heading, URL, or engine modifications permitted. |
| `/contact`, `/privacy-policy`, `/security`, `/terms` | Support & Legal compliance | Suffix: ` \| PDFtoExcel.in` | **PRESERVED:** Do not strip or alter hardcoded brand suffixes without explicit approval. |

---

## 3. CHANGE EXECUTION WORKFLOW

1. **Pre-Change Verification:** Inspect affected files using `view_file` to confirm current code baseline.
2. **Scope Isolation:** Modify only files explicitly designated for the current task.
3. **Manual Editing:** Use editor replacement tools (`replace_file_content`, `multi_replace_file_content`) line by line.
4. **Diff Review:** Review every changed line to verify that zero unintended code was touched.
5. **Multi-Step Testing:** Execute manual functional and regression tests.
6. **Ledger Update:** Update `TASK_LEDGER.md`, `TEST_MATRIX.md`, `CHANGELOG.md`, and `EXECUTION_STATE.md`.
