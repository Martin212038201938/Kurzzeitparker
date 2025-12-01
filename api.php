<?php
/**
 * Kurzzeitparker API
 * Temporary file sharing backend
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

define('DATA_DIR', __DIR__ . '/data');
define('UPLOADS_DIR', __DIR__ . '/uploads');
define('MAX_STORAGE_BYTES', 6 * 1024 * 1024 * 1024); // 6 GB
define('EXPIRY_DAYS', 7);

// Ensure directories exist
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(UPLOADS_DIR)) mkdir(UPLOADS_DIR, 0755, true);

// Generate cryptic link (20-27 characters)
function generateLink($length = null) {
    if ($length === null) {
        $length = rand(20, 27);
    }
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $link = '';
    for ($i = 0; $i < $length; $i++) {
        $link .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $link;
}

// Load folders database
function loadFolders() {
    $file = DATA_DIR . '/folders.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

// Save folders database
function saveFolders($folders) {
    file_put_contents(DATA_DIR . '/folders.json', json_encode($folders, JSON_PRETTY_PRINT));
}

// Load files database
function loadFiles() {
    $file = DATA_DIR . '/files.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

// Save files database
function saveFiles($files) {
    file_put_contents(DATA_DIR . '/files.json', json_encode($files, JSON_PRETTY_PRINT));
}

// Load comments database
function loadComments() {
    $file = DATA_DIR . '/comments.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

// Save comments database
function saveComments($comments) {
    file_put_contents(DATA_DIR . '/comments.json', json_encode($comments, JSON_PRETTY_PRINT));
}

// Calculate folder size
function calculateFolderSize($folderId) {
    $files = loadFiles();
    $size = 0;
    foreach ($files as $file) {
        if ($file['folder_id'] === $folderId) {
            $size += $file['size'];
        }
    }
    return $size;
}

// Calculate total storage used
function calculateTotalStorage() {
    $files = loadFiles();
    $size = 0;
    foreach ($files as $file) {
        $size += $file['size'];
    }
    return $size;
}

// Get days remaining until deletion
function getDaysRemaining($createdAt) {
    $created = strtotime($createdAt);
    $expiry = $created + (EXPIRY_DAYS * 24 * 60 * 60);
    $remaining = ceil(($expiry - time()) / (24 * 60 * 60));
    return max(0, $remaining);
}

// Get comment count for item
function getCommentCount($itemId) {
    $comments = loadComments();
    $count = 0;
    foreach ($comments as $comment) {
        if ($comment['item_id'] === $itemId) {
            $count++;
        }
    }
    return $count;
}

// Handle API requests
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_folder':
        $name = $_POST['name'] ?? '';
        if (empty($name)) {
            echo json_encode(['error' => 'Name is required']);
            exit;
        }

        $folders = loadFolders();
        $link = generateLink();

        $folder = [
            'id' => uniqid(),
            'name' => $name,
            'link' => $link,
            'created_at' => date('Y-m-d H:i:s'),
            'permanent' => false
        ];

        $folders[] = $folder;
        saveFolders($folders);

        echo json_encode(['success' => true, 'folder' => $folder]);
        break;

    case 'get_folder':
        $link = $_GET['link'] ?? '';
        if (empty($link)) {
            echo json_encode(['error' => 'Link is required']);
            exit;
        }

        $folders = loadFolders();
        $folder = null;
        foreach ($folders as $f) {
            if ($f['link'] === $link) {
                $folder = $f;
                break;
            }
        }

        if (!$folder) {
            echo json_encode(['error' => 'Folder not found']);
            exit;
        }

        $folder['size'] = calculateFolderSize($folder['id']);
        $folder['days_remaining'] = getDaysRemaining($folder['created_at']);
        $folder['comment_count'] = getCommentCount($folder['id']);

        // Get files in folder
        $files = loadFiles();
        $folderFiles = [];
        foreach ($files as $file) {
            if ($file['folder_id'] === $folder['id']) {
                $file['days_remaining'] = getDaysRemaining($file['created_at']);
                $file['comment_count'] = getCommentCount($file['id']);
                $folderFiles[] = $file;
            }
        }

        // Sort by newest first
        usort($folderFiles, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $folder['files'] = $folderFiles;

        echo json_encode(['success' => true, 'folder' => $folder]);
        break;

    case 'upload':
        $folderId = $_POST['folder_id'] ?? '';
        $relativePath = $_POST['relative_path'] ?? '';

        if (empty($folderId)) {
            echo json_encode(['error' => 'Folder ID is required']);
            exit;
        }

        if (!isset($_FILES['file'])) {
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];

        // Check storage limit
        $currentStorage = calculateTotalStorage();
        if ($currentStorage + $file['size'] > MAX_STORAGE_BYTES) {
            echo json_encode(['error' => '6 GB Speicherlimit überschritten!']);
            exit;
        }

        // Create upload directory for folder
        $folderDir = UPLOADS_DIR . '/' . $folderId;
        if (!is_dir($folderDir)) {
            mkdir($folderDir, 0755, true);
        }

        // Generate unique filename
        $fileId = uniqid();
        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $storedName = $fileId . ($extension ? '.' . $extension : '');
        $filePath = $folderDir . '/' . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['error' => 'Upload failed']);
            exit;
        }

        $files = loadFiles();
        $fileRecord = [
            'id' => $fileId,
            'folder_id' => $folderId,
            'name' => $originalName,
            'relative_path' => $relativePath,
            'stored_name' => $storedName,
            'size' => $file['size'],
            'mime_type' => $file['type'],
            'created_at' => date('Y-m-d H:i:s'),
            'permanent' => false,
            'is_new' => true
        ];

        $files[] = $fileRecord;
        saveFiles($files);

        $fileRecord['days_remaining'] = EXPIRY_DAYS;
        $fileRecord['comment_count'] = 0;

        echo json_encode(['success' => true, 'file' => $fileRecord]);
        break;

    case 'delete_file':
        $fileId = $_POST['file_id'] ?? '';

        if (empty($fileId)) {
            echo json_encode(['error' => 'File ID is required']);
            exit;
        }

        $files = loadFiles();
        $fileToDelete = null;
        $newFiles = [];

        foreach ($files as $file) {
            if ($file['id'] === $fileId) {
                $fileToDelete = $file;
            } else {
                $newFiles[] = $file;
            }
        }

        if ($fileToDelete) {
            $filePath = UPLOADS_DIR . '/' . $fileToDelete['folder_id'] . '/' . $fileToDelete['stored_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            saveFiles($newFiles);

            // Delete associated comments
            $comments = loadComments();
            $newComments = array_filter($comments, function($c) use ($fileId) {
                return $c['item_id'] !== $fileId;
            });
            saveComments(array_values($newComments));
        }

        echo json_encode(['success' => true]);
        break;

    case 'toggle_permanent':
        $fileId = $_POST['file_id'] ?? '';

        if (empty($fileId)) {
            echo json_encode(['error' => 'File ID is required']);
            exit;
        }

        $files = loadFiles();
        foreach ($files as &$file) {
            if ($file['id'] === $fileId) {
                $file['permanent'] = !$file['permanent'];
                break;
            }
        }
        saveFiles($files);

        echo json_encode(['success' => true]);
        break;

    case 'get_comments':
        $itemId = $_GET['item_id'] ?? '';

        if (empty($itemId)) {
            echo json_encode(['error' => 'Item ID is required']);
            exit;
        }

        $comments = loadComments();
        $itemComments = array_filter($comments, function($c) use ($itemId) {
            return $c['item_id'] === $itemId;
        });

        // Sort by newest first
        usort($itemComments, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        echo json_encode(['success' => true, 'comments' => array_values($itemComments)]);
        break;

    case 'add_comment':
        $itemId = $_POST['item_id'] ?? '';
        $text = $_POST['text'] ?? '';

        if (empty($itemId) || empty($text)) {
            echo json_encode(['error' => 'Item ID and text are required']);
            exit;
        }

        $comments = loadComments();
        $comment = [
            'id' => uniqid(),
            'item_id' => $itemId,
            'text' => $text,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $comments[] = $comment;
        saveComments($comments);

        echo json_encode(['success' => true, 'comment' => $comment]);
        break;

    case 'download':
        $fileId = $_GET['file_id'] ?? '';

        if (empty($fileId)) {
            http_response_code(400);
            echo json_encode(['error' => 'File ID is required']);
            exit;
        }

        $files = loadFiles();
        $fileRecord = null;
        foreach ($files as $file) {
            if ($file['id'] === $fileId) {
                $fileRecord = $file;
                break;
            }
        }

        if (!$fileRecord) {
            http_response_code(404);
            echo json_encode(['error' => 'File not found']);
            exit;
        }

        $filePath = UPLOADS_DIR . '/' . $fileRecord['folder_id'] . '/' . $fileRecord['stored_name'];

        if (!file_exists($filePath)) {
            http_response_code(404);
            echo json_encode(['error' => 'File not found on disk']);
            exit;
        }

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileRecord['name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');

        readfile($filePath);
        exit;

    case 'download_zip':
        $folderId = $_GET['folder_id'] ?? '';

        if (empty($folderId)) {
            http_response_code(400);
            echo json_encode(['error' => 'Folder ID is required']);
            exit;
        }

        $folders = loadFolders();
        $folder = null;
        foreach ($folders as $f) {
            if ($f['id'] === $folderId) {
                $folder = $f;
                break;
            }
        }

        if (!$folder) {
            http_response_code(404);
            echo json_encode(['error' => 'Folder not found']);
            exit;
        }

        $files = loadFiles();
        $folderFiles = array_filter($files, function($f) use ($folderId) {
            return $f['folder_id'] === $folderId;
        });

        if (empty($folderFiles)) {
            http_response_code(404);
            echo json_encode(['error' => 'No files in folder']);
            exit;
        }

        // Create ZIP file
        $zipName = sys_get_temp_dir() . '/' . $folder['name'] . '_' . time() . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not create ZIP file']);
            exit;
        }

        foreach ($folderFiles as $file) {
            $filePath = UPLOADS_DIR . '/' . $folderId . '/' . $file['stored_name'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $file['name']);
            }
        }

        $zip->close();

        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $folder['name'] . '.zip"');
        header('Content-Length: ' . filesize($zipName));
        header('Cache-Control: no-cache, must-revalidate');

        readfile($zipName);
        unlink($zipName);
        exit;

    case 'poll':
        // Long polling for live updates
        $folderId = $_GET['folder_id'] ?? '';
        $lastUpdate = $_GET['last_update'] ?? '';

        if (empty($folderId)) {
            echo json_encode(['error' => 'Folder ID is required']);
            exit;
        }

        $folders = loadFolders();
        $folder = null;
        foreach ($folders as $f) {
            if ($f['id'] === $folderId) {
                $folder = $f;
                break;
            }
        }

        if (!$folder) {
            echo json_encode(['error' => 'Folder not found']);
            exit;
        }

        $files = loadFiles();
        $folderFiles = [];
        foreach ($files as $file) {
            if ($file['folder_id'] === $folderId) {
                $file['days_remaining'] = getDaysRemaining($file['created_at']);
                $file['comment_count'] = getCommentCount($file['id']);
                $folderFiles[] = $file;
            }
        }

        usort($folderFiles, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $folder['size'] = calculateFolderSize($folder['id']);
        $folder['days_remaining'] = getDaysRemaining($folder['created_at']);
        $folder['comment_count'] = getCommentCount($folder['id']);
        $folder['files'] = $folderFiles;

        echo json_encode([
            'success' => true,
            'folder' => $folder,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;

    case 'clear_new_badge':
        $fileId = $_POST['file_id'] ?? '';

        if (empty($fileId)) {
            echo json_encode(['error' => 'File ID is required']);
            exit;
        }

        $files = loadFiles();
        foreach ($files as &$file) {
            if ($file['id'] === $fileId) {
                $file['is_new'] = false;
                break;
            }
        }
        saveFiles($files);

        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
        break;
}
