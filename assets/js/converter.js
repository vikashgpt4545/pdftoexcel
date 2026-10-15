/**
 * PDFtoExcel.in - Universal High-Performance Document Conversion Controller
 * Powered by Mozilla PDF.js & SheetJS (xlsx.full.js)
 * 100% Client-Side Private Document Processing & Table Extraction
 */

document.addEventListener('DOMContentLoaded', () => {
    initConverter();
    initFaqAccordion();
    initCloudStorage();
    initRatingWidget();
});

// Configure PDF.js Worker
if (window.pdfjsLib) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = '/assets/js/vendor/pdf.worker.min.js';
}

function initConverter() {
    const card = document.getElementById('converterCard');
    if (!card) return;

    const toolId = card.dataset.toolId || 'pdf-to-excel';
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseBtn');

    const filePreviewPanel = document.getElementById('filePreviewPanel');
    const previewFileName = document.getElementById('previewFileName');
    const previewFileSize = document.getElementById('previewFileSize');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const startConvertBtn = document.getElementById('startConvertBtn');

    const processingPanel = document.getElementById('processingPanel');
    const progressStatus = document.getElementById('progressStatus');
    const progressBarFill = document.getElementById('progressBarFill');
    const progressPercent = document.getElementById('progressPercent');

    const downloadPanel = document.getElementById('downloadPanel');
    const downloadFileBtn = document.getElementById('downloadFileBtn');
    const convertAnotherBtn = document.getElementById('convertAnotherBtn');

    const errorPanel = document.getElementById('errorPanel');
    const errorTitle = document.getElementById('errorTitle');
    const errorDesc = document.getElementById('errorDesc');
    const errorTryAgainBtn = document.getElementById('errorTryAgainBtn');

    const passwordModalPanel = document.getElementById('passwordModalPanel');
    const pdfPasswordInput = document.getElementById('pdfPasswordInput');
    const passwordErrorMsg = document.getElementById('passwordErrorMsg');
    const submitPasswordBtn = document.getElementById('submitPasswordBtn');
    const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');

    let currentFile = null;

    // Trigger file dialog on Browse button click
    if (browseBtn) {
        browseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.click();
        });
    }

    // Drag and drop events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            handleFileSelection(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    function handleFileSelection(file) {
        currentFile = file;
        previewFileName.textContent = file.name;
        previewFileSize.textContent = formatBytes(file.size);

        dropzone.style.display = 'none';
        filePreviewPanel.style.display = 'flex';
        processingPanel.style.display = 'none';
        downloadPanel.style.display = 'none';
        if (errorPanel) errorPanel.style.display = 'none';
    }

    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', () => {
            resetConverter();
        });
    }

    if (convertAnotherBtn) {
        convertAnotherBtn.addEventListener('click', () => {
            resetConverter();
        });
    }

    if (errorTryAgainBtn) {
        errorTryAgainBtn.addEventListener('click', () => {
            resetConverter();
        });
    }

    function resetConverter() {
        currentFile = null;
        fileInput.value = '';
        dropzone.style.display = 'block';
        filePreviewPanel.style.display = 'none';
        processingPanel.style.display = 'none';
        downloadPanel.style.display = 'none';
        if (errorPanel) errorPanel.style.display = 'none';
        if (passwordModalPanel) {
            passwordModalPanel.style.display = 'none';
            if (pdfPasswordInput) pdfPasswordInput.value = '';
            if (passwordErrorMsg) passwordErrorMsg.style.display = 'none';
        }
    }

    function showError(title, message) {
        processingPanel.style.display = 'none';
        filePreviewPanel.style.display = 'none';
        dropzone.style.display = 'none';
        downloadPanel.style.display = 'none';
        if (passwordModalPanel) passwordModalPanel.style.display = 'none';
        if (errorPanel) {
            if (errorTitle) errorTitle.textContent = title || 'Conversion Error';
            if (errorDesc) errorDesc.textContent = message || 'An error occurred during conversion.';
            errorPanel.style.display = 'block';
        } else {
            alert((title ? title + ': ' : '') + message);
            resetConverter();
        }
    }

    function promptPdfPassword(callback, reason) {
        if (!passwordModalPanel) {
            const pwd = prompt('This PDF is password protected. Enter document password:');
            if (pwd !== null) {
                callback(pwd);
            } else {
                showError('Conversion Cancelled', 'Password protected PDF decryption was cancelled.');
            }
            return;
        }

        processingPanel.style.display = 'none';
        filePreviewPanel.style.display = 'none';
        dropzone.style.display = 'none';
        downloadPanel.style.display = 'none';
        if (errorPanel) errorPanel.style.display = 'none';
        passwordModalPanel.style.display = 'block';

        if (reason === 2 || (window.pdfjsLib && reason === pdfjsLib.PasswordResponses?.INCORRECT_PASSWORD)) {
            if (passwordErrorMsg) {
                passwordErrorMsg.textContent = 'Incorrect password. Please verify and try again.';
                passwordErrorMsg.style.display = 'block';
            }
        } else {
            if (passwordErrorMsg) passwordErrorMsg.style.display = 'none';
        }

        if (pdfPasswordInput) {
            pdfPasswordInput.value = '';
            setTimeout(() => pdfPasswordInput.focus(), 50);
        }

        const cleanSubmit = () => {
            const enteredPwd = pdfPasswordInput ? pdfPasswordInput.value : '';
            if (!enteredPwd) {
                if (passwordErrorMsg) {
                    passwordErrorMsg.textContent = 'Please enter the document password.';
                    passwordErrorMsg.style.display = 'block';
                }
                return;
            }
            passwordModalPanel.style.display = 'none';
            processingPanel.style.display = 'block';
            const progressStatusEl = document.getElementById('progressStatus');
            if (progressStatusEl) progressStatusEl.textContent = 'Decrypting document with provided credentials...';
            cleanup();
            callback(enteredPwd);
        };

        const cleanCancel = () => {
            passwordModalPanel.style.display = 'none';
            cleanup();
            showError('Conversion Cancelled', 'Decryption cancelled. Document remains password protected.');
        };

        const onKeyDown = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                cleanSubmit();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cleanCancel();
            }
        };

        function cleanup() {
            submitPasswordBtn?.removeEventListener('click', cleanSubmit);
            cancelPasswordBtn?.removeEventListener('click', cleanCancel);
            pdfPasswordInput?.removeEventListener('keydown', onKeyDown);
            if (pdfPasswordInput) pdfPasswordInput.value = ''; // Zero sensitive memory
        }

        submitPasswordBtn?.addEventListener('click', cleanSubmit);
        cancelPasswordBtn?.addEventListener('click', cleanCancel);
        pdfPasswordInput?.addEventListener('keydown', onKeyDown);
    }

    if (startConvertBtn) {
        startConvertBtn.addEventListener('click', async () => {
            if (!currentFile) return;

            // Get selected output format
            const selectedFormat = document.querySelector('input[name="output_format"]:checked')?.value || 
                                   document.querySelector('input[name="output_format"]')?.value || 'xlsx';

            filePreviewPanel.style.display = 'none';
            processingPanel.style.display = 'block';

            await executeRealConversion(currentFile, toolId, selectedFormat);
        });
    }

    /**
     * Executes real document parsing and spreadsheet compilation
     */
    async function executeRealConversion(file, tool, format) {
        const updateProgress = (pct, msg) => {
            if (progressBarFill) progressBarFill.style.width = pct + '%';
            if (progressPercent) progressPercent.textContent = pct + '%';
            if (progressStatus) progressStatus.textContent = msg;
        };

        try {
            if (file.size > 100 * 1024 * 1024) {
                throw new Error('File size exceeds the 100MB processing limit. Please compress or select a document under 100MB.');
            }

            updateProgress(15, 'Reading document stream into secure memory...');
            const arrayBuffer = await file.arrayBuffer();

            const isPdf = file.name.toLowerCase().endsWith('.pdf');
            const isText = file.name.toLowerCase().endsWith('.txt') || file.name.toLowerCase().endsWith('.csv');
            const isExcel = file.name.toLowerCase().endsWith('.xlsx') || file.name.toLowerCase().endsWith('.xls');
            const isDocx = file.name.toLowerCase().endsWith('.docx');
            const isDoc = file.name.toLowerCase().endsWith('.doc');
            const isImage = /\.(jpe?g|png|webp|bmp|tiff?)$/i.test(file.name);

            let extractedRows = [];

            if (isDoc && !isDocx) {
                throw new Error('Legacy Word 97-2003 (.doc) binary format detected. Please save or re-export your document as modern .docx in Microsoft Word or Google Docs before converting.');
            } else if (isImage) {
                throw new Error('Image file detected. Direct browser-side extraction requires structured digital text or OpenXML tabular streams. For optimal table alignment, please upload a digital PDF, Excel (.xlsx), Word (.docx), or CSV file.');
            } else if (isDocx) {
                updateProgress(35, 'Extracting Word OpenXML table structure and paragraphs...');
                extractedRows = await extractRowsFromDocx(arrayBuffer, updateProgress);
            } else if (isPdf && window.pdfjsLib) {
                updateProgress(30, 'Parsing PDF geometry, fonts & bounding boxes...');
                const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
                loadingTask.onPassword = (callback, reason) => {
                    promptPdfPassword(callback, reason);
                };
                const pdfDoc = await loadingTask.promise;

                const totalPages = pdfDoc.numPages;
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    const pagePct = 30 + Math.round((pageNum / totalPages) * 45);
                    updateProgress(pagePct, `Extracting tabular rows from page ${pageNum} of ${totalPages}...`);
                    
                    const page = await pdfDoc.getPage(pageNum);
                    const textContent = await page.getTextContent();
                    
                    const pageRows = extractRowsFromPdfPage(textContent);
                    if (pageRows.length > 0) {
                        if (totalPages > 1 && pageNum > 1) {
                            extractedRows.push([`--- PAGE ${pageNum} ---`]);
                        }
                        extractedRows.push(...pageRows);
                    }

                    // Release page rendering resources to prevent memory ballooning
                    if (page && typeof page.cleanup === 'function') {
                        page.cleanup();
                    }

                    // Yield to browser event loop periodically to prevent UI freezes
                    if (pageNum % 3 === 0) {
                        await new Promise(r => setTimeout(r, 0));
                    }
                }

                if (totalPages > 0 && extractedRows.length === 0) {
                    throw new Error('Scanned PDF detected. This document contains bitmap images without an embedded digital text layer. Please use an original vector PDF or export with selectable text.');
                }
            } else if (isText) {
                updateProgress(50, 'Parsing delimited text streams...');
                const textDecoder = new TextDecoder('utf-8');
                const rawText = textDecoder.decode(arrayBuffer);
                const lines = rawText.split(/\r\n|\n|\r/);
                lines.forEach(line => {
                    if (line.trim()) {
                        const cols = line.includes('\t') ? line.split('\t') : 
                                     line.includes(',') ? line.split(',') : 
                                     line.split(/\s{2,}/);
                        extractedRows.push(cols.map(c => c.trim().replace(/^["']|["']$/g, '')));
                    }
                });
            } else if (isExcel && window.XLSX) {
                updateProgress(60, 'Reading spreadsheet workbook...');
                const wb = XLSX.read(arrayBuffer, { type: 'array' });
                const firstSheetName = wb.SheetNames[0];
                const ws = wb.Sheets[firstSheetName];
                extractedRows = XLSX.utils.sheet_to_json(ws, { header: 1 });
            } else {
                throw new Error('Unsupported or unrecognized document format for ' + file.name);
            }

            // Verify extracted content exists
            if (!extractedRows || extractedRows.length === 0) {
                throw new Error('No readable table cells or text could be extracted from this document.');
            }

            updateProgress(85, `Compiling clean ${format.toUpperCase()} spreadsheet structure...`);
            await new Promise(res => setTimeout(res, 250)); // smooth transition

            // Create target file blob
            let outputBlob;
            if (format === 'csv') {
                const csvString = formatToCsv(extractedRows);
                outputBlob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            } else if (format === 'pdf' || format === 'pdf_landscape') {
                updateProgress(90, 'Formatting columns & compiling vector single-page PDF...');
                const isLandscape = (format === 'pdf_landscape') || (tool && tool.includes('landscape'));
                outputBlob = compileTableToPdfBlob(file.name, extractedRows, isLandscape);
            } else if (window.XLSX) {
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(extractedRows);
                
                // Calculate responsive column widths
                const colWidths = [];
                extractedRows.forEach(r => {
                    if (Array.isArray(r)) {
                        r.forEach((cell, idx) => {
                            const len = String(cell || '').length;
                            colWidths[idx] = Math.max(colWidths[idx] || 10, Math.min(len + 3, 50));
                        });
                    }
                });
                ws['!cols'] = colWidths.map(w => ({ wch: w }));
                
                XLSX.utils.book_append_sheet(wb, ws, "Converted Sheet");
                const wbout = XLSX.write(wb, { 
                    bookType: format === 'xls' ? 'biff8' : (format === 'csv' ? 'csv' : 'xlsx'), 
                    type: 'array' 
                });
                outputBlob = new Blob([wbout], { type: getMimeType(format) });
            } else {
                // Fallback CSV blob
                const csvString = formatToCsv(extractedRows);
                outputBlob = new Blob([csvString], { type: getMimeType(format) });
            }

            updateProgress(100, 'Conversion completed successfully!');
            await new Promise(res => setTimeout(res, 200));

            // Present download link
            presentDownload(file, outputBlob, format);

            // Notify backend silently for analytics/metrics (Lightweight JSON telemetry - zero binary upload)
            fetch('/api/convert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tool_id: tool,
                    format: format,
                    file_size: file.size,
                    status: 'success'
                })
            }).catch(() => {});

        } catch (err) {
            console.error('Conversion processing error:', err);
            const userMsg = (err && err.message) ? err.message : 'An error occurred while parsing the document.';
            showError('Conversion Failed', userMsg);

            // Notify backend of conversion error (Lightweight JSON telemetry - zero binary upload)
            fetch('/api/convert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    tool_id: tool,
                    format: format,
                    file_size: file ? file.size : 0,
                    status: 'error',
                    error_message: userMsg
                })
            }).catch(() => {});
        }
    }

    /**
     * Extracts text items and groups them into 2D tabular rows based on Y/X coordinates
     */
    function extractRowsFromPdfPage(textContent) {
        if (!textContent || !textContent.items || textContent.items.length === 0) {
            return [];
        }

        const items = textContent.items;
        const lineGroups = {};

        items.forEach(item => {
            const str = (item.str || '').trim();
            if (!str) return;

            // Snap Y coordinate to 6-pixel threshold to align slightly misaligned row baselines
            const yCoord = Math.round(item.transform[5] / 6) * 6;
            const xCoord = Math.round(item.transform[4]);

            if (!lineGroups[yCoord]) {
                lineGroups[yCoord] = [];
            }
            lineGroups[yCoord].push({ x: xCoord, str: str, width: item.width || 0 });
        });

        // PDF coordinates origin is bottom-left, so sort Y descending (top-to-bottom)
        const sortedYs = Object.keys(lineGroups).map(Number).sort((a, b) => b - a);
        const rows = [];

        sortedYs.forEach(y => {
            const lineItems = lineGroups[y].sort((a, b) => a.x - b.x);
            let rowCells = [];

            lineItems.forEach(chunk => {
                // If chunk contains tab separators
                if (chunk.str.includes('\t')) {
                    rowCells.push(...chunk.str.split('\t').map(s => s.trim()));
                } else {
                    rowCells.push(chunk.str);
                }
            });

            if (rowCells.length > 0) {
                // Auto-detect numeric cells
                const cleanRow = rowCells.map(cell => {
                    // If pure number (integer or decimal)
                    if (/^-?\d+(\.\d+)?$/.test(cell)) {
                        return parseFloat(cell);
                    }
                    return cell;
                });
                rows.push(cleanRow);
            }
        });

        return rows;
    }

    function formatToCsv(rows) {
        return rows.map(r => {
            if (!Array.isArray(r)) return `"${String(r).replace(/"/g, '""')}"`;
            return r.map(c => {
                const s = String(c ?? '');
                if (s.includes(',') || s.includes('"') || s.includes('\n') || s.includes('\r')) {
                    return `"${s.replace(/"/g, '""')}"`;
                }
                return s;
            }).join(',');
        }).join('\r\n');
    }

    /**
     * Extracts structured table rows and paragraph content from a Word .docx file
     * Parses OpenXML word/document.xml extracted from ZIP stream
     */
    async function extractRowsFromDocx(arrayBuffer, updateProgress) {
        const bytes = new Uint8Array(arrayBuffer);

        // Check for ZIP magic signature PK\x03\x04 (0x50, 0x4B, 0x03, 0x04)
        if (bytes.length < 30 || bytes[0] !== 0x50 || bytes[1] !== 0x4B) {
            // Check for legacy OLE2 .doc magic signature \xD0\xCF\x11\xE0
            if (bytes.length >= 8 && bytes[0] === 0xD0 && bytes[1] === 0xCF && bytes[2] === 0x11 && bytes[3] === 0xE0) {
                throw new Error('Legacy Word 97-2003 (.doc) format detected. Please save or re-export the file as modern .docx in Microsoft Word or Google Docs before converting.');
            }
            throw new Error('Invalid Word document. The file does not appear to be a valid .docx package.');
        }

        if (updateProgress) updateProgress(45, 'Unpacking Word document package & extracting XML...');
        const xmlBytes = await extractZipFile(bytes, 'word/document.xml');
        if (!xmlBytes) {
            throw new Error('Could not find word/document.xml in document package. The file may be corrupt or encrypted.');
        }

        if (updateProgress) updateProgress(65, 'Parsing document tables, grids & paragraphs...');
        const xmlText = new TextDecoder('utf-8').decode(xmlBytes);
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlText, 'application/xml');

        const parserError = xmlDoc.querySelector('parsererror');
        if (parserError) {
            throw new Error('Malformed XML inside Word document: ' + (parserError.textContent || '').substring(0, 100));
        }

        const rows = [];
        const body = xmlDoc.getElementsByTagName('w:body')[0] || xmlDoc.getElementsByTagName('body')[0];
        if (!body) {
            throw new Error('Empty Word document body.');
        }

        // Check for tables (<w:tbl>)
        const tables = body.getElementsByTagName('w:tbl');
        if (tables && tables.length > 0) {
            for (let t = 0; t < tables.length; t++) {
                const table = tables[t];
                if (t > 0 && rows.length > 0) {
                    rows.push([]); // Spacer row between distinct tables
                }

                const trs = table.getElementsByTagName('w:tr');
                for (let r = 0; r < trs.length; r++) {
                    const tr = trs[r];
                    const rowCells = [];
                    const tcs = tr.getElementsByTagName('w:tc');

                    for (let c = 0; c < tcs.length; c++) {
                        const tc = tcs[c];

                        // Check for column span in cell properties
                        let colSpan = 1;
                        const gridSpanEl = tc.querySelector('gridSpan, w\\:gridSpan');
                        if (gridSpanEl) {
                            const val = parseInt(gridSpanEl.getAttribute('w:val') || gridSpanEl.getAttribute('val') || '1', 10);
                            if (!isNaN(val) && val > 1) colSpan = val;
                        }

                        // Extract text from paragraphs in this cell
                        const paragraphs = tc.getElementsByTagName('w:p');
                        const cellLines = [];
                        for (let p = 0; p < paragraphs.length; p++) {
                            const pEl = paragraphs[p];
                            let pText = '';
                            const textNodes = pEl.querySelectorAll('w\\:t, t, w\\:tab, tab');
                            textNodes.forEach(node => {
                                const nodeName = (node.localName || node.nodeName).toLowerCase();
                                if (nodeName === 'tab') {
                                    pText += ' ';
                                } else {
                                    pText += (node.textContent || '');
                                }
                            });
                            if (pText.trim()) {
                                cellLines.push(pText.trim());
                            }
                        }

                        const cellVal = cellLines.join(' ');
                        rowCells.push(cellVal);

                        // Pad empty cells for column span to maintain grid alignment
                        for (let s = 1; s < colSpan; s++) {
                            rowCells.push('');
                        }
                    }

                    if (rowCells.some(cell => cell !== '')) {
                        rows.push(rowCells);
                    }
                }
            }
        } else {
            // No tables found: extract paragraphs and lists
            const paragraphs = body.getElementsByTagName('w:p');
            for (let p = 0; p < paragraphs.length; p++) {
                const pEl = paragraphs[p];
                const textNodes = pEl.querySelectorAll('w\\:t, t, w\\:tab, tab');
                let hasTabs = false;
                let currentChunk = '';
                const lineCols = [];

                textNodes.forEach(node => {
                    const nodeName = (node.localName || node.nodeName).toLowerCase();
                    if (nodeName === 'tab') {
                        hasTabs = true;
                        lineCols.push(currentChunk.trim());
                        currentChunk = '';
                    } else {
                        currentChunk += (node.textContent || '');
                    }
                });
                if (currentChunk.trim() || hasTabs) {
                    lineCols.push(currentChunk.trim());
                }

                if (hasTabs && lineCols.length > 1) {
                    if (lineCols.some(c => c !== '')) {
                        rows.push(lineCols);
                    }
                } else {
                    const fullText = lineCols.join(' ').trim();
                    if (fullText) {
                        rows.push([fullText]);
                    }
                }
            }
        }

        if (!rows || rows.length === 0) {
            throw new Error('The Word document does not contain any readable text or table cells.');
        }

        return rows;
    }

    /**
     * Extracts a specific uncompressed file payload from a ZIP ArrayBuffer/Uint8Array
     * Powered by native DataView header traversal & DecompressionStream('deflate-raw')
     */
    async function extractZipFile(uint8Array, targetPath) {
        const view = new DataView(uint8Array.buffer, uint8Array.byteOffset, uint8Array.byteLength);
        const len = uint8Array.byteLength;

        // 1. Locate End of Central Directory Record (EOCD) signature: 0x06054B50 (PK\x05\x06)
        let eocdOffset = -1;
        const maxSearch = Math.min(len, 65557);
        for (let i = len - 22; i >= len - maxSearch; i--) {
            if (view.getUint32(i, true) === 0x06054B50) {
                eocdOffset = i;
                break;
            }
        }

        const decoder = new TextDecoder('utf-8');

        if (eocdOffset !== -1) {
            const cdEntries = view.getUint16(eocdOffset + 10, true);
            const cdSize = view.getUint32(eocdOffset + 12, true);
            const cdOffset = view.getUint32(eocdOffset + 16, true);

            let offset = cdOffset;
            for (let entry = 0; entry < cdEntries && offset < cdOffset + cdSize; entry++) {
                if (view.getUint32(offset, true) !== 0x02014B50) break;

                const compressionMethod = view.getUint16(offset + 10, true);
                const compressedSize = view.getUint32(offset + 20, true);
                const uncompressedSize = view.getUint32(offset + 24, true);
                const filenameLen = view.getUint16(offset + 28, true);
                const extraLen = view.getUint16(offset + 30, true);
                const commentLen = view.getUint16(offset + 32, true);
                const localHeaderOffset = view.getUint32(offset + 42, true);

                const filenameBytes = uint8Array.subarray(offset + 46, offset + 46 + filenameLen);
                const filename = decoder.decode(filenameBytes);

                if (filename.toLowerCase() === targetPath.toLowerCase()) {
                    if (view.getUint32(localHeaderOffset, true) !== 0x04034B50) {
                        throw new Error('Corrupt ZIP local header for ' + targetPath);
                    }
                    const localNameLen = view.getUint16(localHeaderOffset + 26, true);
                    const localExtraLen = view.getUint16(localHeaderOffset + 28, true);
                    const dataStart = localHeaderOffset + 30 + localNameLen + localExtraLen;
                    const compressedSlice = uint8Array.subarray(dataStart, dataStart + compressedSize);

                    if (compressionMethod === 0) {
                        return compressedSlice;
                    } else if (compressionMethod === 8) {
                        return await decompressDeflateRaw(compressedSlice, uncompressedSize);
                    } else {
                        throw new Error('Unsupported ZIP compression algorithm: ' + compressionMethod);
                    }
                }

                offset += 46 + filenameLen + extraLen + commentLen;
            }
        }

        // 2. Fallback: Sequential scan of Local File Headers
        return await extractFromLocalHeaders(uint8Array, view, targetPath);
    }

    /**
     * Fallback scanner that iterates sequentially through Local File Headers (PK\x03\x04)
     */
    async function extractFromLocalHeaders(uint8Array, view, targetPath) {
        let offset = 0;
        const decoder = new TextDecoder('utf-8');
        const len = uint8Array.byteLength;

        while (offset + 30 < len) {
            if (view.getUint32(offset, true) !== 0x04034B50) break;

            const compressionMethod = view.getUint16(offset + 8, true);
            const compressedSize = view.getUint32(offset + 18, true);
            const uncompressedSize = view.getUint32(offset + 22, true);
            const filenameLen = view.getUint16(offset + 26, true);
            const extraLen = view.getUint16(offset + 28, true);

            const filenameBytes = uint8Array.subarray(offset + 30, offset + 30 + filenameLen);
            const filename = decoder.decode(filenameBytes);
            const dataStart = offset + 30 + filenameLen + extraLen;

            if (filename.toLowerCase() === targetPath.toLowerCase()) {
                const compressedSlice = uint8Array.subarray(dataStart, dataStart + compressedSize);
                if (compressionMethod === 0) return compressedSlice;
                if (compressionMethod === 8) return await decompressDeflateRaw(compressedSlice, uncompressedSize);
            }

            if (compressedSize > 0) {
                offset = dataStart + compressedSize;
            } else {
                offset += 4;
                while (offset + 4 < len && view.getUint32(offset, true) !== 0x04034B50 && view.getUint32(offset, true) !== 0x02014B50) {
                    offset++;
                }
            }
        }

        return null;
    }

    /**
     * Decompresses raw DEFLATE byte chunks using browser native DecompressionStream
     */
    async function decompressDeflateRaw(compressedSlice, uncompressedSize) {
        if (typeof DecompressionStream !== 'undefined') {
            const ds = new DecompressionStream('deflate-raw');
            const writer = ds.writable.getWriter();
            writer.write(compressedSlice);
            writer.close();

            const reader = ds.readable.getReader();
            const chunks = [];
            let total = 0;

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;
                chunks.push(value);
                total += value.length;
            }

            const decompressed = new Uint8Array(total);
            let cursor = 0;
            for (const chunk of chunks) {
                decompressed.set(chunk, cursor);
                cursor += chunk.length;
            }
            return decompressed;
        }
        throw new Error('DecompressionStream is not supported in this browser runtime.');
    }

    function presentDownload(file, blob, format) {
        processingPanel.style.display = 'none';
        downloadPanel.style.display = 'block';

        const baseName = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
        const ext = (format === 'pdf_landscape') ? 'pdf' : format;
        const newFileName = baseName + '-converted.' + ext;

        const downloadUrl = URL.createObjectURL(blob);
        downloadFileBtn.href = downloadUrl;
        downloadFileBtn.download = newFileName;
    }

    function getMimeType(format) {
        switch(format) {
            case 'csv': return 'text/csv;charset=utf-8;';
            case 'xls': return 'application/vnd.ms-excel';
            case 'xlsx': return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'pdf':
            case 'pdf_landscape': return 'application/pdf';
            default: return 'application/octet-stream';
        }
    }

    /**
     * Compiles 2D tabular rows into an authentic ISO 32000-1 vector PDF Blob
     * Scaled to fit all columns seamlessly on a single page
     */
    function compileTableToPdfBlob(fileName, rows, isLandscape) {
        const pageWidth = isLandscape ? 841.89 : 595.28;
        const pageHeight = isLandscape ? 595.28 : 841.89;

        const margin = 36.0;
        const usableWidth = pageWidth - (2 * margin);
        const usableHeight = pageHeight - (2 * margin);

        const dataRows = (rows || []).filter(r => Array.isArray(r) && r.some(c => c !== null && c !== undefined && String(c).trim() !== ''));
        const effectiveRows = dataRows.length > 0 ? dataRows : [['No table data extracted']];

        const numCols = Math.max(...effectiveRows.map(r => r.length), 1);
        const normRows = effectiveRows.map(r => {
            const row = r.map(c => (c !== null && c !== undefined) ? String(c).trim() : '');
            while (row.length < numCols) row.push('');
            return row;
        });

        // Calculate proportional column widths
        const colMaxLens = new Array(numCols).fill(0);
        normRows.forEach(r => {
            r.forEach((val, idx) => {
                colMaxLens[idx] = Math.max(colMaxLens[idx], val.length);
            });
        });

        const totalLen = colMaxLens.reduce((a, b) => a + b, 0) || 1;
        let colWidths = colMaxLens.map(len => Math.max(40.0, (len / totalLen) * usableWidth));
        const widthScale = usableWidth / colWidths.reduce((a, b) => a + b, 0);
        colWidths = colWidths.map(w => w * widthScale);

        const headerSpace = 52.0;
        const availH = usableHeight - headerSpace;
        const rowHeight = Math.max(12.0, Math.min(24.0, availH / normRows.length));
        const fontSize = Math.max(6.0, Math.min(9.5, rowHeight * 0.55));

        const streamLines = [];

        // Header Accent Bar & Title
        streamLines.push("0.06 0.73 0.51 rg"); // Brand Emerald #10B981
        streamLines.push(`${margin.toFixed(2)} ${(pageHeight - margin - 8).toFixed(2)} ${usableWidth.toFixed(2)} 3.00 re f`);
        streamLines.push("0.1 0.15 0.25 rg"); // Text dark slate
        streamLines.push("BT");
        streamLines.push("/F2 14 Tf");
        streamLines.push(`${margin.toFixed(2)} ${(pageHeight - margin - 26).toFixed(2)} Td`);
        const cleanTitle = String(fileName || 'Document Spreadsheet - PDFtoExcel.in').replace(/[()\\]/g, '');
        streamLines.push(`(${cleanTitle}) Tj`);
        streamLines.push("ET");

        // Subtitle
        streamLines.push("0.45 0.55 0.65 rg");
        streamLines.push("BT");
        streamLines.push("/F1 8 Tf");
        streamLines.push(`${margin.toFixed(2)} ${(pageHeight - margin - 38).toFixed(2)} Td`);
        streamLines.push(`(Converted by PDFtoExcel.in - Fits all columns on one page) Tj`);
        streamLines.push("ET");

        const startY = pageHeight - margin - headerSpace;

        normRows.forEach((r, rIdx) => {
            const curY = startY - (rIdx * rowHeight);

            if (rIdx === 0) {
                streamLines.push("0.12 0.18 0.28 rg"); // Deep navy header
                streamLines.push(`${margin.toFixed(2)} ${(curY - rowHeight + 3).toFixed(2)} ${usableWidth.toFixed(2)} ${rowHeight.toFixed(2)} re f`);
            } else if (rIdx % 2 === 1) {
                streamLines.push("0.96 0.97 0.98 rg"); // Zebra stripe
                streamLines.push(`${margin.toFixed(2)} ${(curY - rowHeight + 3).toFixed(2)} ${usableWidth.toFixed(2)} ${rowHeight.toFixed(2)} re f`);
            }

            streamLines.push("0.85 0.88 0.92 RG 0.5 w");
            streamLines.push(`${margin.toFixed(2)} ${(curY - rowHeight + 3).toFixed(2)} m ${(margin + usableWidth).toFixed(2)} ${(curY - rowHeight + 3).toFixed(2)} l S`);

            let curX = margin;
            r.forEach((cell, cIdx) => {
                const w = colWidths[cIdx];
                const maxChars = Math.max(3, Math.floor(w / (fontSize * 0.52)));
                let cellStr = String(cell);
                if (cellStr.length > maxChars) {
                    cellStr = cellStr.substring(0, maxChars - 2) + '..';
                }
                const cleanCell = cellStr.replace(/[()\\]/g, '');

                const textColor = (rIdx === 0) ? "1 1 1 rg" : "0.1 0.15 0.2 rg";
                const fontName = (rIdx === 0) ? "/F2" : "/F1";

                streamLines.push(textColor);
                streamLines.push("BT");
                streamLines.push(`${fontName} ${fontSize.toFixed(1)} Tf`);
                streamLines.push(`${(curX + 4).toFixed(2)} ${(curY - (rowHeight * 0.68)).toFixed(2)} Td`);
                streamLines.push(`(${cleanCell}) Tj`);
                streamLines.push("ET");

                curX += w;
            });
        });

        const contentStream = streamLines.join("\n");
        const encoder = new TextEncoder();
        const contentBytes = encoder.encode(contentStream);
        const contentLen = contentBytes.length;

        const pdfChunks = [];
        const offsets = [];

        function addChunk(str) {
            const bytes = encoder.encode(str);
            pdfChunks.push(bytes);
            return bytes.length;
        }

        let totalOffset = 0;
        totalOffset += addChunk("%PDF-1.4\n%\xe2\xe3\xcf\xd3\n");

        function addObj(body) {
            offsets.push(totalOffset);
            totalOffset += addChunk(`${offsets.length} 0 obj\n${body}\nendobj\n`);
        }

        // 1 0 obj: Catalog
        addObj("<< /Type /Catalog /Pages 2 0 R >>");
        // 2 0 obj: Pages
        addObj("<< /Type /Pages /Kids [ 3 0 R ] /Count 1 >>");
        // 3 0 obj: Page
        addObj(`<< /Type /Page /Parent 2 0 R /MediaBox [ 0 0 ${pageWidth.toFixed(2)} ${pageHeight.toFixed(2)} ] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>`);
        // 4 0 obj: Font Regular
        addObj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        // 5 0 obj: Font Bold
        addObj("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");
        // 6 0 obj: Contents
        addObj(`<< /Length ${contentLen} >>\nstream\n${contentStream}\nendstream`);

        // xref table
        const xrefOffset = totalOffset;
        let xrefStr = `xref\n0 ${offsets.length + 1}\n0000000000 65535 f \n`;
        offsets.forEach(off => {
            xrefStr += String(off).padStart(10, '0') + " 00000 n \n";
        });
        totalOffset += addChunk(xrefStr);

        // trailer
        const trailerStr = `trailer\n<< /Size ${offsets.length + 1} /Root 1 0 R >>\nstartxref\n${xrefOffset}\n%%EOF`;
        addChunk(trailerStr);

        return new Blob(pdfChunks, { type: 'application/pdf' });
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
}

function initCloudStorage() {
    const driveBtn = document.getElementById('googleDriveBtn');
    const dropboxBtn = document.getElementById('dropboxBtn');
    const fileInput = document.getElementById('fileInput');

    const handleCloudClick = (provider) => {
        if (fileInput) {
            fileInput.click();
        }
    };

    if (driveBtn) {
        driveBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            handleCloudClick('Google Drive');
        });
    }

    if (dropboxBtn) {
        dropboxBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            handleCloudClick('Dropbox');
        });
    }
}

function initRatingWidget() {
    const widget = document.getElementById('ratingWidget');
    if (!widget) return;

    const stars = widget.querySelectorAll('.star-btn');
    const feedback = document.getElementById('ratingFeedbackMsg');
    const voteCount = document.getElementById('voteCountText');
    let userRated = false;

    stars.forEach((btn, idx) => {
        btn.addEventListener('mouseenter', () => {
            if (userRated) return;
            stars.forEach((s, i) => {
                s.classList.toggle('hover', i <= idx);
            });
        });
        btn.addEventListener('mouseleave', () => {
            if (userRated) return;
            stars.forEach(s => s.classList.remove('hover'));
        });
        btn.addEventListener('click', () => {
            if (userRated) return;
            userRated = true;
            const val = idx + 1;
            stars.forEach((s, i) => {
                s.classList.toggle('active', i < val);
                s.classList.remove('hover');
            });
            if (feedback) {
                feedback.textContent = `Thank you for your ${val}-star review!`;
                feedback.style.display = 'inline-block';
            }
            if (voteCount) {
                const cur = parseInt(voteCount.textContent.replace(/,/g, ''), 10) || 14280;
                voteCount.textContent = (cur + 1).toLocaleString();
            }
            fetch('/api/rate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    url: widget.dataset.url || window.location.pathname,
                    rating: val
                })
            }).catch(() => {});
        });
    });
}

function initFaqAccordion() {
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', () => {
                const isOpen = item.classList.contains('open');
                faqItems.forEach(i => i.classList.remove('open'));
                if (!isOpen) {
                    item.classList.add('open');
                }
            });
        }
    });
}
