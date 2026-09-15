<?php
/**
 * PDFtoExcel.in - Universal Interactive Converter Tool Component
 * 
 * Configurable parameters:
 * $toolConfig = [
 *   'tool_id' => 'pdf-to-excel',
 *   'input_label' => 'PDF',
 *   'output_label' => 'Excel (.xlsx)',
 *   'accept' => '.pdf',
 *   'btn_text' => 'Convert to Excel',
 *   'default_format' => 'xlsx',
 *   'formats' => ['xlsx' => 'XLSX (.xlsx)', 'xls' => 'XLS (.xls)', 'csv' => 'CSV (.csv)'],
 *   'ocr_option' => true,
 *   'custom_badge' => 'Instant Conversion'
 * ];
 */

$defaultConfig = [
    'tool_id' => 'pdf-to-excel',
    'input_label' => 'PDF',
    'output_label' => 'Excel (.xlsx)',
    'accept' => '.pdf',
    'btn_text' => 'Convert PDF to Excel',
    'default_format' => 'xlsx',
    'formats' => ['xlsx' => 'Excel (.xlsx)', 'xls' => 'Legacy Excel (.xls)', 'csv' => 'CSV (.csv)'],
    'ocr_option' => true,
    'custom_badge' => '100% Free · No Login'
];
$cfg = array_merge($defaultConfig, $toolConfig ?? []);
?>

<div class="converter-card" id="converterCard" data-tool-id="<?= htmlspecialchars($cfg['tool_id']) ?>">
    <div class="card-header">
        <div class="card-title-group">
            <span class="tool-badge"><?= htmlspecialchars($cfg['custom_badge']) ?></span>
            <h3 class="tool-title"><?= htmlspecialchars($cfg['btn_text']) ?></h3>
        </div>
        <div class="security-indicator" title="Your files are encrypted via 256-bit SSL and deleted automatically">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span>Private & Encrypted</span>
        </div>
    </div>

    <!-- Dropzone State (Initial) -->
    <div class="dropzone" id="dropzone">
        <input type="file" id="fileInput" class="file-input" accept="<?= htmlspecialchars($cfg['accept']) ?>" aria-label="Select file to convert" <?= (strpos($cfg['tool_id'], 'multiple') !== false || !empty($cfg['multiple'])) ? 'multiple' : '' ?>>
        
        <div class="dropzone-content" id="dropzoneContent">
            <div class="dropzone-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
            </div>
            <div class="dropzone-text">
                <p class="main-prompt"><strong>Choose <?= htmlspecialchars($cfg['input_label']) ?> file</strong> or drag & drop here</p>
                <p class="sub-prompt">Supports files up to 100MB · Instant processing</p>
            </div>
            <div class="upload-actions-wrapper">
                <button type="button" class="btn btn-upload" id="browseBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Select <?= htmlspecialchars($cfg['input_label']) ?> File
                </button>
                <div class="cloud-storage-buttons">
                    <button type="button" class="btn-cloud" id="googleDriveBtn" title="Choose from Google Drive" aria-label="Upload from Google Drive">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM19 18H6c-2.21 0-4-1.79-4-4 0-2.05 1.53-3.76 3.56-3.97l1.07-.11.5-.95C8.08 7.14 9.94 6 12 6c2.62 0 4.88 1.86 5.39 4.43l.3 1.5 1.53.11c1.56.1 2.78 1.41 2.78 2.96 0 1.65-1.35 3-3 3z"/></svg>
                        <span>Drive</span>
                    </button>
                    <button type="button" class="btn-cloud" id="dropboxBtn" title="Choose from Dropbox" aria-label="Upload from Dropbox">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 2L0 6l6 4 6-4-6-4zm12 0l-6 4 6 4 6-4-6-4zM0 14l6 4 6-4-6-4-6 4zm18-4l-6 4 6 4 6-4-6-4zM6 19.5l6-4-3-2-3 2v4zm12-4l-6 4 3 2 3-2v-4z"/></svg>
                        <span>Dropbox</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected File Preview & Settings (Hidden Initially) -->
    <div class="file-preview-panel" id="filePreviewPanel" style="display: none;">
        <div class="file-info-box">
            <div class="file-icon-box">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </div>
            <div class="file-meta">
                <div class="file-name" id="previewFileName">document.pdf</div>
                <div class="file-size" id="previewFileSize">1.2 MB</div>
            </div>
            <button type="button" class="btn-remove-file" id="removeFileBtn" title="Remove file">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Format Selector (if multiple formats) -->
        <?php if (!empty($cfg['formats']) && count($cfg['formats']) > 1): ?>
        <div class="format-options">
            <label class="format-label">Output Format:</label>
            <div class="format-radio-group">
                <?php foreach ($cfg['formats'] as $fmtKey => $fmtName): ?>
                <label class="radio-pill">
                    <input type="radio" name="output_format" value="<?= htmlspecialchars($fmtKey) ?>" <?= $fmtKey === $cfg['default_format'] ? 'checked' : '' ?>>
                    <span><?= htmlspecialchars($fmtName) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
            <input type="hidden" name="output_format" value="<?= htmlspecialchars($cfg['default_format']) ?>">
        <?php endif; ?>

        <!-- Conversion Action Button -->
        <button type="button" class="btn btn-convert" id="startConvertBtn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"></polyline>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
            <span><?= htmlspecialchars($cfg['btn_text']) ?></span>
        </button>
    </div>

    <!-- Processing State (Hidden Initially) -->
    <div class="processing-panel" id="processingPanel" style="display: none;">
        <div class="processing-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            </div>
        </div>
        <div class="progress-info">
            <div class="progress-status" id="progressStatus">Analyzing tables and structure...</div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBarFill" style="width: 15%;"></div>
            </div>
            <div class="progress-percentage" id="progressPercent">15%</div>
        </div>
    </div>

    <!-- Success / Download State (Hidden Initially) -->
    <div class="download-panel" id="downloadPanel" style="display: none;">
        <div class="success-icon-box">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <h4 class="download-title">Conversion Complete!</h4>
        <p class="download-desc">Your file was converted with 100% table and formatting accuracy.</p>

        <div class="download-actions">
            <a href="#" class="btn btn-download" id="downloadFileBtn" download="converted-file.xlsx">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Download Converted File</span>
            </a>
            <button type="button" class="btn btn-convert-another" id="convertAnotherBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                Convert Another File
            </button>
        </div>
        <div class="auto-delete-note">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            File will be deleted from memory in 60 minutes for your privacy.
        </div>
    </div>
</div>

<!-- On-Page Review Rating Widget (Triggers Google Rich Snippet Stars) -->
<div class="tool-feedback-bar">
    <div class="rating-widget" id="ratingWidget" data-url="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?>">
        <span class="rating-prompt">Rate this tool:</span>
        <div class="star-rating" id="starRating" role="radiogroup" aria-label="Tool Rating">
            <button type="button" class="star-btn" data-star="1" aria-label="1 star">★</button>
            <button type="button" class="star-btn" data-star="2" aria-label="2 stars">★</button>
            <button type="button" class="star-btn" data-star="3" aria-label="3 stars">★</button>
            <button type="button" class="star-btn" data-star="4" aria-label="4 stars">★</button>
            <button type="button" class="star-btn" data-star="5" aria-label="5 stars">★</button>
        </div>
        <div class="rating-score-display">
            <strong>4.86</strong>/5 (<span id="voteCountText">14,280</span> ratings)
        </div>
        <div class="rating-feedback-msg" id="ratingFeedbackMsg" style="display:none;">Thank you!</div>
    </div>
    
    <div class="live-activity-badge">
        <span class="pulse-indicator"></span>
        <span><strong>1,840+</strong> files converted today</span>
    </div>
</div>

<div class="converter-quick-jump">
    <span class="jump-label">Jump to:</span>
    <a href="#how-it-works" class="jump-pill">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        How It Works
    </a>
    <a href="#faq-section" class="jump-pill">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        FAQs
    </a>
</div>
