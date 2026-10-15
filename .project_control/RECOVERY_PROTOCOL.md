# PD FTOEXCEL.IN — SESSION RECOVERY PROTOCOL

**Target Website:** `https://pdftoexcel.in/`  
**Workspace:** `d:\pdftoexcel`  
**Control Root:** `.project_control/`  

---

## 1. OBJECTIVE & MANDATE

When an agent or developer starts a new interaction or resumes after an interrupted session, they MUST follow this exact recovery sequence before making any code modifications or running commands.

This protocol ensures zero work is forgotten, zero progress is reset, no completed tasks are repeated unnecessarily, and no uncommitted user changes are overwritten.

---

## 2. STEP-BY-STEP RECOVERY SEQUENCE

Whenever entering the workspace or resuming execution:

### Step 1: Read `AGENTS.md`
- Inspect `AGENTS.md` in the project root (`d:\pdftoexcel\AGENTS.md`).
- Re-familiarize with the core operating rules:
  1. Manual, sequential implementation only.
  2. No scripts or bulk automation.
  3. No unrelated modifications.
  4. Mandatory SEO ranking protection for Homepage (`/`) and `/excel-to-pdf-in-one-page`.
  5. Never append brand suffixes automatically (`| PDFtoExcel.in` / `- PDFtoExcel.in`).
  6. Preserve existing user changes.
  7. Mandatory task tracking in `.project_control/`.
  8. Mandatory manual verification before marking any task complete.

### Step 2: Read `.project_control/EXECUTION_STATE.md`
- Identify the currently active task ID (e.g. `CORR-01`).
- Check the last successfully completed step and checkpoint.
- Check the exact file(s) currently being inspected or modified.
- Identify what tests remain outstanding for the active task.
- Identify the exact next action to perform.

### Step 3: Read `.project_control/TASK_LEDGER.md`
- Review the status of all tasks (`REC-01` through `REC-10`, `TASK-01` through `TASK-18`, `CORR-01` through `CORR-06`).
- Verify the active task's problem description, evidence, and acceptance criteria.
- Never repeat a task marked `COMPLETED` unless a fresh test reveals an undeniable defect.

### Step 4: Review Latest `.project_control/CHANGELOG.md` Entry
- Check the timestamp and details of the most recent code changes.
- Ensure the changelog accurately matches the repository state.

### Step 5: Inspect Current Git Status and Diffs
- Check `git status` via the editor/IDE.
- Verify which files have uncommitted modifications.
- Confirm all modified files map directly to the approved task ledger.
- NEVER run `git reset --hard`, `git clean`, or destructive checkouts.
- Preserve every user change.

### Step 6: Reopen Active Task Source Files
- Open the files specified in `EXECUTION_STATE.md` (e.g. `assets/js/converter.js`, `pages/word-to-excel.php`).
- Review the exact lines of code where implementation is in progress.

### Step 7: Repeat Any Uncertain Tests
- If the session was interrupted during verification, execute the tests listed in `TEST_MATRIX.md` for the active task.
- Verify whether the implementation was finished or partially completed.

### Step 8: Continue From the Last Verified Checkpoint
- Resume execution from the exact point where work stopped.
- Do not skip ahead to other tasks.
- Do not restart from Phase 0 if previous phases are already documented and verified.

---

## 3. CHECKPOINT RE-ENTRY MATRIX

| Interruption Point | State in `EXECUTION_STATE.md` | Action on Recovery |
| :--- | :--- | :--- |
| During Planning / Inspection | Phase 1 / Phase 2 | Finish control files; do not touch source code. |
| During Task Implementation | `IN_PROGRESS` | Inspect diff; finish minimal edits for the current task. |
| During Task Verification | `VERIFICATION_REQUIRED` | Run manual test procedures in browser / local server; record evidence. |
| After Verification Passed | `COMPLETED` (Pending ledger update) | Update `TASK_LEDGER.md`, `CHANGELOG.md`, `EXECUTION_STATE.md`; move to next task. |

---

## 4. STRICT STOP CONDITIONS ON RECOVERY

Immediately halt execution and notify the user if:
1. Files listed in the task ledger are missing or have unexpected upstream modifications.
2. Git reports uncommitted changes from the user that conflict with planned task files.
3. A previously passing test in `TEST_MATRIX.md` now fails (regression detected).
4. A planned modification would alter a protected SEO asset (titles, URLs, canonicals, H1s on `/` or `/excel-to-pdf-in-one-page`).
5. A genuine external dependency is missing (e.g. third-party API credentials, client decisions).
