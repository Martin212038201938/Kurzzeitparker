<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurzzeitparker - Temporäres Datei-Sharing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #f5f5f7;
            --bg-secondary: #ffffff;
            --bg-tertiary: #fafafa;
            --text-primary: #1d1d1f;
            --text-secondary: #6e6e73;
            --text-muted: #86868b;
            --accent-blue: #007aff;
            --accent-purple: #af52de;
            --accent-pink: #ff2d55;
            --accent-orange: #ff9500;
            --accent-green: #34c759;
            --accent-teal: #5ac8fa;
            --accent-red: #ff3b30;
            --border-color: #d2d2d7;
            --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-heavy: 0 8px 32px rgba(0, 0, 0, 0.12);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.5;
        }

        /* Landing Page - No Link */
        .landing-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px;
        }

        .landing-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 60px;
            text-align: center;
            max-width: 500px;
            box-shadow: var(--shadow-heavy);
        }

        .landing-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 48px;
        }

        .landing-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .landing-subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 40px;
        }

        .create-folder-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .input-field {
            padding: 16px 20px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 16px;
            font-family: inherit;
            transition: all 0.2s ease;
            outline: none;
        }

        .input-field:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.3);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-secondary);
            border-color: var(--accent-blue);
        }

        .btn-danger {
            background: var(--accent-red);
            color: white;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
        }

        /* App Container */
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            min-height: 100vh;
        }

        /* Header */
        .app-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 24px 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-medium);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .folder-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-pink));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .folder-info h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .folder-meta {
            display: flex;
            gap: 16px;
            margin-top: 4px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        /* Toolbar */
        .toolbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            padding: 16px 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .file-count {
            font-weight: 600;
            color: var(--text-secondary);
        }

        /* Upload Zone */
        .upload-zone {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 3px dashed var(--border-color);
            border-radius: var(--radius-xl);
            padding: 60px;
            text-align: center;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: var(--accent-blue);
            background: rgba(0, 122, 255, 0.05);
            transform: scale(1.01);
        }

        .upload-zone.dragover {
            border-style: solid;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent-teal), var(--accent-blue));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }

        .upload-zone h3 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .upload-zone p {
            color: var(--text-secondary);
        }

        .upload-input {
            display: none;
        }

        /* Upload Progress */
        .upload-progress-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-medium);
            display: none;
        }

        .upload-progress-container.active {
            display: block;
        }

        .upload-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .upload-header h4 {
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .upload-stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .upload-files-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .upload-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: var(--bg-tertiary);
            border-radius: var(--radius-sm);
            margin-bottom: 8px;
        }

        .upload-file-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .upload-file-icon {
            font-size: 20px;
        }

        .upload-file-name {
            font-weight: 500;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upload-file-size {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .upload-file-progress {
            width: 100px;
            height: 6px;
            background: var(--border-color);
            border-radius: 3px;
            overflow: hidden;
        }

        .upload-file-progress-fill {
            height: 100%;
            background: var(--accent-green);
            border-radius: 3px;
            transition: width 0.2s ease;
        }

        /* Files Grid */
        .files-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 24px;
            box-shadow: var(--shadow-medium);
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .file-card {
            background: var(--bg-tertiary);
            border-radius: var(--radius-lg);
            padding: 20px;
            transition: all 0.2s ease;
            position: relative;
            border: 2px solid transparent;
        }

        .file-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-medium);
            border-color: var(--accent-blue);
        }

        .file-card.permanent {
            border-color: var(--accent-red);
            background: linear-gradient(135deg, rgba(255, 59, 48, 0.05), rgba(255, 45, 85, 0.05));
        }

        .file-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 16px;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-teal));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .file-icon.image {
            background: linear-gradient(135deg, var(--accent-pink), var(--accent-orange));
        }

        .file-icon.document {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
        }

        .file-icon.video {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
        }

        .file-icon.audio {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-red));
        }

        .file-icon.archive {
            background: linear-gradient(135deg, var(--accent-teal), var(--accent-green));
        }

        .file-details {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-size {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .file-badges {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-days {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-pink));
            color: white;
        }

        .badge-permanent {
            background: var(--accent-red);
            color: white;
        }

        .badge-new {
            background: var(--accent-green);
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .file-actions {
            display: flex;
            gap: 8px;
        }

        .file-action-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .file-action-btn:hover {
            background: var(--accent-blue);
            color: white;
        }

        .file-action-btn.delete:hover {
            background: var(--accent-red);
        }

        .file-action-btn.permanent-toggle {
            background: var(--accent-red);
            color: white;
        }

        .file-action-btn.permanent-toggle.active {
            background: var(--accent-green);
        }

        /* Comment Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: white;
            border-radius: var(--radius-xl);
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow: hidden;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .modal {
            transform: scale(1);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--bg-tertiary);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--accent-red);
            color: white;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .comment-item {
            padding: 14px 18px;
            background: var(--bg-tertiary);
            border-radius: var(--radius-md);
        }

        .comment-text {
            font-size: 14px;
            line-height: 1.6;
        }

        .comment-time {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .no-comments {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 12px;
        }

        .modal-footer input {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .modal-footer input:focus {
            border-color: var(--accent-blue);
        }

        .modal-footer button {
            padding: 14px 24px;
        }

        /* Share Modal */
        .share-link-container {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .share-link-input {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: monospace;
            background: var(--bg-tertiary);
            outline: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
        }

        .empty-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--accent-teal), var(--accent-blue));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 48px;
        }

        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-secondary);
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            background: white;
            border-radius: var(--radius-md);
            padding: 16px 24px;
            box-shadow: var(--shadow-heavy);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }

        .toast.error {
            border-left: 4px solid var(--accent-red);
        }

        .toast.success {
            border-left: 4px solid var(--accent-green);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .app-container {
                padding: 16px;
            }

            .app-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .folder-meta {
                justify-content: center;
            }

            .toolbar {
                flex-direction: column;
                gap: 12px;
            }

            .files-grid {
                grid-template-columns: 1fr;
            }
        }

        .hidden {
            display: none !important;
        }

        /* Loading Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-top-color: var(--accent-blue);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Landing Page (No Link) -->
    <div id="landingPage" class="landing-page">
        <div class="landing-card">
            <div class="landing-icon">📁</div>
            <h1 class="landing-title">Kurzzeitparker</h1>
            <p class="landing-subtitle">Erstelle einen temporären Ordner zum Teilen von Dateien.<br>Dateien werden nach 7 Tagen automatisch gelöscht.</p>
            <form class="create-folder-form" id="createFolderForm">
                <input type="text" class="input-field" id="newFolderName" placeholder="Ordnername eingeben..." required>
                <button type="submit" class="btn btn-primary">📂 Neuen Ordner erstellen</button>
            </form>
        </div>
    </div>

    <!-- App Container (With Link) -->
    <div id="appContainer" class="app-container hidden">
        <!-- Header -->
        <header class="app-header">
            <div class="header-left">
                <div class="folder-icon">📂</div>
                <div class="folder-info">
                    <h1 id="folderName">Ordnername</h1>
                    <div class="folder-meta">
                        <span class="meta-item">💾 <span id="folderSize">0 MB</span></span>
                        <span class="meta-item">⏳ <span id="folderDays">7 Tage</span></span>
                        <span class="meta-item">💬 <span id="folderComments">0</span> Kommentare</span>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" id="shareBtn">🔗 Link kopieren</button>
                <button class="btn btn-secondary" id="commentFolderBtn">💬 Kommentieren</button>
                <button class="btn btn-primary" id="downloadAllBtn">📦 Alles als ZIP</button>
            </div>
        </header>

        <!-- Upload Zone -->
        <div class="upload-zone" id="uploadZone">
            <div class="upload-icon">⬆️</div>
            <h3>Dateien hierher ziehen</h3>
            <p>oder klicken zum Auswählen • Dateien & Ordner werden unterstützt</p>
            <input type="file" id="fileInput" class="upload-input" multiple webkitdirectory>
            <input type="file" id="fileInputSingle" class="upload-input" multiple>
        </div>

        <!-- Upload Progress -->
        <div class="upload-progress-container" id="uploadProgress">
            <div class="upload-header">
                <h4>⬆️ Upload läuft...</h4>
                <div class="upload-stats">
                    <span id="uploadedCount">0/0 Dateien</span>
                    <span id="uploadedSize">0 MB / 0 MB</span>
                    <span id="uploadTime">~0s verbleibend</span>
                </div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <div class="upload-files-list" id="uploadFilesList"></div>
        </div>

        <!-- Files Container -->
        <div class="files-container">
            <div class="files-grid" id="filesGrid">
                <!-- Files will be rendered here -->
            </div>
            <div class="empty-state hidden" id="emptyState">
                <div class="empty-icon">📭</div>
                <h3>Noch keine Dateien</h3>
                <p>Ziehe Dateien in den Upload-Bereich oder klicke dort, um loszulegen.</p>
            </div>
        </div>
    </div>

    <!-- Comment Modal -->
    <div class="modal-overlay" id="commentModal">
        <div class="modal">
            <div class="modal-header">
                <h3>💬 Kommentare</h3>
                <button class="modal-close" id="closeCommentModal">✕</button>
            </div>
            <div class="modal-body">
                <div class="comments-list" id="commentsList">
                    <!-- Comments will be rendered here -->
                </div>
                <div class="no-comments hidden" id="noComments">
                    <p>Noch keine Kommentare vorhanden.</p>
                </div>
            </div>
            <div class="modal-footer">
                <input type="text" id="commentInput" placeholder="Kommentar schreiben...">
                <button class="btn btn-primary" id="sendComment">Senden</button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // App State
        let currentFolder = null;
        let currentFolderLink = null;
        let currentCommentItemId = null;
        let pollInterval = null;

        // Utility Functions
        function formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatTime(seconds) {
            if (seconds < 60) return `~${Math.ceil(seconds)}s`;
            if (seconds < 3600) return `~${Math.ceil(seconds / 60)}min`;
            return `~${Math.ceil(seconds / 3600)}h`;
        }

        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            const videoExts = ['mp4', 'webm', 'avi', 'mov', 'mkv'];
            const audioExts = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
            const docExts = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx'];
            const archiveExts = ['zip', 'rar', '7z', 'tar', 'gz'];

            if (imageExts.includes(ext)) return { icon: '🖼️', class: 'image' };
            if (videoExts.includes(ext)) return { icon: '🎬', class: 'video' };
            if (audioExts.includes(ext)) return { icon: '🎵', class: 'audio' };
            if (docExts.includes(ext)) return { icon: '📄', class: 'document' };
            if (archiveExts.includes(ext)) return { icon: '📦', class: 'archive' };
            return { icon: '📄', class: '' };
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <span>${type === 'error' ? '❌' : '✅'}</span>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        // API Functions
        async function createFolder(name) {
            const formData = new FormData();
            formData.append('action', 'create_folder');
            formData.append('name', name);

            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            return response.json();
        }

        async function getFolder(link) {
            const response = await fetch(`api.php?action=get_folder&link=${encodeURIComponent(link)}`);
            return response.json();
        }

        async function uploadFile(file, folderId, relativePath = '', onProgress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                const formData = new FormData();
                formData.append('action', 'upload');
                formData.append('folder_id', folderId);
                formData.append('file', file);
                formData.append('relative_path', relativePath);

                xhr.upload.onprogress = (e) => {
                    if (e.lengthComputable && onProgress) {
                        onProgress(e.loaded / e.total);
                    }
                };

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        reject(new Error('Upload failed'));
                    }
                };

                xhr.onerror = () => reject(new Error('Upload failed'));
                xhr.open('POST', 'api.php');
                xhr.send(formData);
            });
        }

        async function deleteFile(fileId) {
            const formData = new FormData();
            formData.append('action', 'delete_file');
            formData.append('file_id', fileId);

            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            return response.json();
        }

        async function togglePermanent(fileId) {
            const formData = new FormData();
            formData.append('action', 'toggle_permanent');
            formData.append('file_id', fileId);

            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            return response.json();
        }

        async function getComments(itemId) {
            const response = await fetch(`api.php?action=get_comments&item_id=${encodeURIComponent(itemId)}`);
            return response.json();
        }

        async function addComment(itemId, text) {
            const formData = new FormData();
            formData.append('action', 'add_comment');
            formData.append('item_id', itemId);
            formData.append('text', text);

            const response = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            return response.json();
        }

        async function pollFolder(folderId) {
            const response = await fetch(`api.php?action=poll&folder_id=${encodeURIComponent(folderId)}`);
            return response.json();
        }

        // Render Functions
        function renderFiles(files) {
            const grid = document.getElementById('filesGrid');
            const emptyState = document.getElementById('emptyState');

            if (!files || files.length === 0) {
                grid.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            grid.innerHTML = files.map(file => {
                const iconInfo = getFileIcon(file.name);
                return `
                    <div class="file-card ${file.permanent ? 'permanent' : ''}" data-file-id="${file.id}">
                        <div class="file-header">
                            <div class="file-icon ${iconInfo.class}">${iconInfo.icon}</div>
                            <div class="file-details">
                                <div class="file-name" title="${file.name}">${file.name}</div>
                                <div class="file-size">${formatSize(file.size)}</div>
                            </div>
                        </div>
                        <div class="file-badges">
                            ${file.is_new ? '<span class="badge badge-new">✨ Neu</span>' : ''}
                            ${file.permanent ? '<span class="badge badge-permanent">♾️ Dauerhaft</span>' : `<span class="badge badge-days">⏳ ${file.days_remaining} Tage</span>`}
                        </div>
                        <div class="file-actions">
                            <button class="file-action-btn download-btn" data-file-id="${file.id}">⬇️</button>
                            <button class="file-action-btn comment-btn" data-file-id="${file.id}" data-comment-count="${file.comment_count}">💬 ${file.comment_count}</button>
                            <button class="file-action-btn permanent-toggle ${file.permanent ? 'active' : ''}" data-file-id="${file.id}" title="${file.permanent ? 'Automatische Löschung aktivieren' : 'Dauerhaft speichern'}">
                                ${file.permanent ? '🔓' : '🔒'}
                            </button>
                            <button class="file-action-btn delete" data-file-id="${file.id}">🗑️</button>
                        </div>
                    </div>
                `;
            }).join('');

            // Attach event listeners
            grid.querySelectorAll('.download-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const fileId = btn.dataset.fileId;
                    window.location.href = `api.php?action=download&file_id=${fileId}`;
                });
            });

            grid.querySelectorAll('.comment-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    openCommentModal(btn.dataset.fileId);
                });
            });

            grid.querySelectorAll('.permanent-toggle').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const fileId = btn.dataset.fileId;
                    await togglePermanent(fileId);
                    refreshFolder();
                    showToast('Status aktualisiert');
                });
            });

            grid.querySelectorAll('.file-action-btn.delete').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (confirm('Datei wirklich löschen?')) {
                        const fileId = btn.dataset.fileId;
                        await deleteFile(fileId);
                        refreshFolder();
                        showToast('Datei gelöscht');
                    }
                });
            });
        }

        function renderComments(comments) {
            const list = document.getElementById('commentsList');
            const noComments = document.getElementById('noComments');

            if (!comments || comments.length === 0) {
                list.innerHTML = '';
                noComments.classList.remove('hidden');
                return;
            }

            noComments.classList.add('hidden');
            list.innerHTML = comments.map(comment => `
                <div class="comment-item">
                    <div class="comment-text">${escapeHtml(comment.text)}</div>
                    <div class="comment-time">${formatDate(comment.created_at)}</div>
                </div>
            `).join('');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Modal Functions
        async function openCommentModal(itemId) {
            currentCommentItemId = itemId;
            const modal = document.getElementById('commentModal');
            modal.classList.add('active');

            const result = await getComments(itemId);
            if (result.success) {
                renderComments(result.comments);
            }
        }

        function closeCommentModal() {
            const modal = document.getElementById('commentModal');
            modal.classList.remove('active');
            currentCommentItemId = null;
        }

        // Folder Functions
        async function refreshFolder() {
            if (!currentFolderLink) return;

            const result = await getFolder(currentFolderLink);
            if (result.success) {
                currentFolder = result.folder;
                updateFolderUI();
                renderFiles(result.folder.files);
            }
        }

        function updateFolderUI() {
            if (!currentFolder) return;

            document.getElementById('folderName').textContent = currentFolder.name;
            document.getElementById('folderSize').textContent = formatSize(currentFolder.size);
            document.getElementById('folderDays').textContent = currentFolder.permanent ? '♾️ Dauerhaft' : `${currentFolder.days_remaining} Tage`;
            document.getElementById('folderComments').textContent = currentFolder.comment_count;
        }

        // Upload Functions
        async function handleFiles(files) {
            if (!currentFolder) return;

            const fileArray = Array.from(files);
            if (fileArray.length === 0) return;

            // Calculate total size
            const totalSize = fileArray.reduce((sum, f) => sum + f.size, 0);
            if (totalSize > 6 * 1024 * 1024 * 1024) {
                showToast('Die Dateien überschreiten das 6 GB Limit!', 'error');
                return;
            }

            const progressContainer = document.getElementById('uploadProgress');
            const progressFill = document.getElementById('progressFill');
            const uploadedCount = document.getElementById('uploadedCount');
            const uploadedSize = document.getElementById('uploadedSize');
            const uploadTime = document.getElementById('uploadTime');
            const filesList = document.getElementById('uploadFilesList');

            progressContainer.classList.add('active');

            let uploadedBytes = 0;
            let uploadedFiles = 0;
            const startTime = Date.now();

            // Render file list
            filesList.innerHTML = fileArray.map((file, index) => `
                <div class="upload-file-item" data-index="${index}">
                    <div class="upload-file-info">
                        <span class="upload-file-icon">${getFileIcon(file.name).icon}</span>
                        <span class="upload-file-name">${file.name}</span>
                        <span class="upload-file-size">${formatSize(file.size)}</span>
                    </div>
                    <div class="upload-file-progress">
                        <div class="upload-file-progress-fill" style="width: 0%"></div>
                    </div>
                </div>
            `).join('');

            for (let i = 0; i < fileArray.length; i++) {
                const file = fileArray[i];
                const fileItem = filesList.querySelector(`[data-index="${i}"]`);
                const fileProgress = fileItem.querySelector('.upload-file-progress-fill');

                try {
                    const relativePath = file.webkitRelativePath || '';
                    await uploadFile(file, currentFolder.id, relativePath, (progress) => {
                        fileProgress.style.width = `${progress * 100}%`;

                        const currentUploaded = uploadedBytes + (progress * file.size);
                        const overallProgress = currentUploaded / totalSize;
                        progressFill.style.width = `${overallProgress * 100}%`;

                        const elapsed = (Date.now() - startTime) / 1000;
                        const rate = currentUploaded / elapsed;
                        const remaining = (totalSize - currentUploaded) / rate;
                        uploadTime.textContent = formatTime(remaining);
                    });

                    uploadedBytes += file.size;
                    uploadedFiles++;

                    uploadedCount.textContent = `${uploadedFiles}/${fileArray.length} Dateien`;
                    uploadedSize.textContent = `${formatSize(uploadedBytes)} / ${formatSize(totalSize)}`;
                    fileProgress.style.width = '100%';
                    fileProgress.style.background = 'var(--accent-green)';
                } catch (error) {
                    fileProgress.style.background = 'var(--accent-red)';
                    showToast(`Fehler beim Upload von ${file.name}`, 'error');
                }
            }

            setTimeout(() => {
                progressContainer.classList.remove('active');
                refreshFolder();
                showToast(`${uploadedFiles} Datei(en) hochgeladen`);
            }, 1000);
        }

        // Initialize App
        function initApp() {
            const urlParams = new URLSearchParams(window.location.search);
            const link = urlParams.get('f');

            if (link) {
                // Show folder view
                currentFolderLink = link;
                document.getElementById('landingPage').classList.add('hidden');
                document.getElementById('appContainer').classList.remove('hidden');
                loadFolder(link);
            }

            // Create folder form
            document.getElementById('createFolderForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('newFolderName').value.trim();
                if (!name) return;

                const result = await createFolder(name);
                if (result.success) {
                    window.location.href = `?f=${result.folder.link}`;
                } else {
                    showToast(result.error || 'Fehler beim Erstellen', 'error');
                }
            });

            // Upload zone
            const uploadZone = document.getElementById('uploadZone');
            const fileInput = document.getElementById('fileInputSingle');

            uploadZone.addEventListener('click', () => fileInput.click());

            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });

            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('dragover');
            });

            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('dragover');

                const items = e.dataTransfer.items;
                const files = [];

                const processEntry = (entry, path = '') => {
                    return new Promise((resolve) => {
                        if (entry.isFile) {
                            entry.file((file) => {
                                // Preserve path
                                file.relativePath = path + file.name;
                                files.push(file);
                                resolve();
                            });
                        } else if (entry.isDirectory) {
                            const reader = entry.createReader();
                            reader.readEntries(async (entries) => {
                                for (const ent of entries) {
                                    await processEntry(ent, path + entry.name + '/');
                                }
                                resolve();
                            });
                        } else {
                            resolve();
                        }
                    });
                };

                const promises = [];
                for (const item of items) {
                    const entry = item.webkitGetAsEntry();
                    if (entry) {
                        promises.push(processEntry(entry));
                    }
                }

                Promise.all(promises).then(() => {
                    if (files.length > 0) {
                        handleFiles(files);
                    }
                });
            });

            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                e.target.value = '';
            });

            // Share button
            document.getElementById('shareBtn').addEventListener('click', () => {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    showToast('Link kopiert!');
                });
            });

            // Comment folder button
            document.getElementById('commentFolderBtn').addEventListener('click', () => {
                if (currentFolder) {
                    openCommentModal(currentFolder.id);
                }
            });

            // Download all button
            document.getElementById('downloadAllBtn').addEventListener('click', () => {
                if (currentFolder) {
                    window.location.href = `api.php?action=download_zip&folder_id=${currentFolder.id}`;
                }
            });

            // Comment modal
            document.getElementById('closeCommentModal').addEventListener('click', closeCommentModal);
            document.getElementById('commentModal').addEventListener('click', (e) => {
                if (e.target.id === 'commentModal') closeCommentModal();
            });

            document.getElementById('sendComment').addEventListener('click', async () => {
                const input = document.getElementById('commentInput');
                const text = input.value.trim();
                if (!text || !currentCommentItemId) return;

                await addComment(currentCommentItemId, text);
                input.value = '';

                const result = await getComments(currentCommentItemId);
                if (result.success) {
                    renderComments(result.comments);
                }
                refreshFolder();
                showToast('Kommentar hinzugefügt');
            });

            document.getElementById('commentInput').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    document.getElementById('sendComment').click();
                }
            });
        }

        async function loadFolder(link) {
            const result = await getFolder(link);
            if (result.success) {
                currentFolder = result.folder;
                updateFolderUI();
                renderFiles(result.folder.files);

                // Start polling for live updates
                if (pollInterval) clearInterval(pollInterval);
                pollInterval = setInterval(async () => {
                    const pollResult = await pollFolder(currentFolder.id);
                    if (pollResult.success) {
                        currentFolder = pollResult.folder;
                        updateFolderUI();
                        renderFiles(pollResult.folder.files);
                    }
                }, 3000);
            } else {
                showToast('Ordner nicht gefunden', 'error');
                document.getElementById('appContainer').classList.add('hidden');
                document.getElementById('landingPage').classList.remove('hidden');
            }
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', initApp);
    </script>
</body>
</html>
