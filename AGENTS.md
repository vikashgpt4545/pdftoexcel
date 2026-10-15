# PD FTOEXCEL.IN — AGENT OPERATING RULES & SAFETY SYSTEM

**Target Domain:** `https://pdftoexcel.in/`  
**Workspace:** `d:\pdftoexcel`  
**Role:** Senior PHP Engineer, JavaScript Engineer, Technical SEO Specialist, Application Security Auditor, QA Engineer, Project Execution Manager.

---

## 1. ABSOLUTE PROJECT BOUNDARIES & EXECUTION RULES

1. **Target Isolation:** Work exclusively on `pdftoexcel.in`. Never switch to or import changes from other domains.
2. **Manual, Sequential Work Only:** No scripts or bulk automation. Do NOT create or run Python, PowerShell, Bash, Node.js or PHP CLI scripts to modify or bulk-process project files. Use editor tools to inspect and modify individual files manually.
3. **No Unrelated Modifications:** Only change files and code strictly necessary for the approved task. Do not redesign the website, change styling, or rewrite entire files when a narrowly scoped diff suffices.
4. **Mandatory SEO Ranking Protection:** The Homepage (`/`) and `/excel-to-pdf-in-one-page` are strictly protected assets. Do not change their titles, meta descriptions, H1 headings, URLs, canonicals, primary search intent, or conversion engines.
5. **No Automatic Brand Suffix on Titles:** Never automatically append `| PDFtoExcel.in`, `- PDFtoExcel.in`, or another brand suffix to page titles. Do not remove existing brand suffixes from the 4 support/legal pages without explicit approval.
6. **No Unapproved Metadata or URL Changes:** Preserve existing sitemaps, robots directives, redirects, structured data, and canonical behavior unless explicitly approved for a specific task.
7. **Preserve All Existing User Changes:** Never execute destructive git operations (`git reset --hard`, `git clean`, destructive checkout, force push). Never overwrite or discard uncommitted user changes.
8. **Mandatory Task Tracking & No Silently Skipped Work:** Maintain status in `.project_control/TASK_LEDGER.md` (`NOT_STARTED`, `IN_PROGRESS`, `VERIFICATION_REQUIRED`, `BLOCKED`, `FAILED`, `COMPLETED`, `CANCELLED_WITH_APPROVAL`).
9. **Mandatory Testing Before Completion:** Never mark a task `COMPLETED` merely because code was edited. Test each feature with representative valid and invalid inputs and verify downloaded output.
10. **Mandatory Recovery After Interruptions:** Resume work using `.project_control/EXECUTION_STATE.md` without skipping steps or resetting progress.
11. **No Fabricated Verification Results:** Separate verified facts from hypotheses. Never claim success without tangible, verifiable evidence.
12. **Stop Safely on Genuine Blockers:** Stop and notify the user when a genuine credential, external dependency, or critical architectural decision requires user approval.

---

## 2. PRIORITY ORDER OF EXECUTION

1. **Protect Existing Rankings & URLs:** Guard anchor URLs (`/`, `/excel-to-pdf-in-one-page`), clean slugs, and working functionality.
2. **Correct Critical Functional, Privacy & Security Defects:** Resolve broken output generation (e.g., Word `.docx` parsing, password PDFs, OCR), eliminate unwanted uploads, and secure API endpoints.
3. **Implement Measurement & Performance Improvements:** Enqueue vendor libraries conditionally and prepare privacy-first analytics instrumentation.
4. **Implement Controlled SEO Enhancements:** Refine snippet CTR for high-impression queries and strengthen internal link discoverability.
5. **Verify All Modifications:** Test every change against explicit acceptance criteria in a safe test environment.
6. **Maintain Persistent Progress:** Update task ledgers and checkpoints so work survives context loss or session reboots without losing progress.

---

## 3. STRICT CHANGE LIFECYCLE

For every task:
1. **Read & Baseline:** Read the task, inspect relevant files, establish existing behavior baseline.
2. **Confirm Scope:** Touch only files explicitly listed for the approved task.
3. **Manual Implementation:** Make minimal, clean edits using editor tools one file at a time.
4. **Diff Inspection:** Review every changed line in the affected file.
5. **Manual & Automated QA:** Test valid and invalid inputs, check downloaded outputs, and verify error states.
6. **Regression Testing:** Verify that other converters, APIs, routing, and SEO elements remain unaffected.
7. **Ledger & Checkpoint Update:** Record changed files, test output, and next steps in `.project_control/TASK_LEDGER.md` and `.project_control/EXECUTION_STATE.md`.
8. **Completion Gate:** Mark `COMPLETED` only when all tests pass and diffs are verified.

---

## 4. SESSION RECOVERY PROTOCOL

At the start of every new interaction:
1. Read `AGENTS.md`.
2. Read `.project_control/PROJECT_MASTER_PLAN.md`.
3. Read `.project_control/EXECUTION_STATE.md`.
4. Read `.project_control/TASK_LEDGER.md`.
5. Check `git status` and uncommitted changes.
6. Resume from the exact recorded checkpoint without skipping steps or resetting progress.
