<?php
/**
 * Cron Script for Automatic File Deletion
 * Run daily: 0 3 * * * php /path/to/cron.php
 *
 * This script deletes files older than 7 days that are not marked as permanent.
 * It also cleans up empty folders and orphaned data.
 */

define('DATA_DIR', __DIR__ . '/data');
define('UPLOADS_DIR', __DIR__ . '/uploads');
define('EXPIRY_DAYS', 7);

// Logging
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    echo "[{$timestamp}] {$message}\n";
}

logMessage("Starting cleanup job...");

// Load databases
function loadJson($filename) {
    $file = DATA_DIR . '/' . $filename;
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

function saveJson($filename, $data) {
    file_put_contents(DATA_DIR . '/' . $filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Load all data
$folders = loadJson('folders.json');
$files = loadJson('files.json');
$comments = loadJson('comments.json');

$deletedFiles = 0;
$deletedFolders = 0;
$deletedComments = 0;

// Calculate expiry timestamp
$expiryTime = time() - (EXPIRY_DAYS * 24 * 60 * 60);

// Delete expired files (not marked as permanent)
$newFiles = [];
$deletedFileIds = [];

foreach ($files as $file) {
    $createdTime = strtotime($file['created_at']);

    // Check if file is expired and not permanent
    if ($createdTime < $expiryTime && !$file['permanent']) {
        // Delete physical file
        $filePath = UPLOADS_DIR . '/' . $file['folder_id'] . '/' . $file['stored_name'];
        if (file_exists($filePath)) {
            unlink($filePath);
            logMessage("Deleted file: {$file['name']} (ID: {$file['id']})");
        }
        $deletedFileIds[] = $file['id'];
        $deletedFiles++;
    } else {
        $newFiles[] = $file;
    }
}

saveJson('files.json', $newFiles);

// Delete comments for deleted files
$newComments = [];
foreach ($comments as $comment) {
    if (!in_array($comment['item_id'], $deletedFileIds)) {
        $newComments[] = $comment;
    } else {
        $deletedComments++;
    }
}
saveJson('comments.json', $newComments);

// Check for empty folders (optional: delete folders with no files after expiry)
$folderFileCount = [];
foreach ($newFiles as $file) {
    $fid = $file['folder_id'];
    $folderFileCount[$fid] = ($folderFileCount[$fid] ?? 0) + 1;
}

$newFolders = [];
$deletedFolderIds = [];

foreach ($folders as $folder) {
    $createdTime = strtotime($folder['created_at']);
    $fileCount = $folderFileCount[$folder['id']] ?? 0;

    // Delete folder if:
    // 1. It's older than expiry period
    // 2. It has no files
    // 3. It's not marked as permanent
    if ($createdTime < $expiryTime && $fileCount === 0 && !($folder['permanent'] ?? false)) {
        // Delete folder directory
        $folderDir = UPLOADS_DIR . '/' . $folder['id'];
        if (is_dir($folderDir)) {
            rmdir($folderDir);
        }
        $deletedFolderIds[] = $folder['id'];
        logMessage("Deleted empty folder: {$folder['name']} (ID: {$folder['id']})");
        $deletedFolders++;
    } else {
        $newFolders[] = $folder;
    }
}

saveJson('folders.json', $newFolders);

// Delete comments for deleted folders
$finalComments = [];
foreach ($newComments as $comment) {
    if (!in_array($comment['item_id'], $deletedFolderIds)) {
        $finalComments[] = $comment;
    } else {
        $deletedComments++;
    }
}
saveJson('comments.json', $finalComments);

// Clean up orphaned upload directories
$uploadDirs = glob(UPLOADS_DIR . '/*', GLOB_ONLYDIR);
$validFolderIds = array_column($newFolders, 'id');

foreach ($uploadDirs as $dir) {
    $dirId = basename($dir);
    if (!in_array($dirId, $validFolderIds)) {
        // Delete all files in directory
        $orphanedFiles = glob($dir . '/*');
        foreach ($orphanedFiles as $orphanedFile) {
            unlink($orphanedFile);
        }
        rmdir($dir);
        logMessage("Deleted orphaned directory: {$dirId}");
    }
}

// Summary
logMessage("═══════════════════════════════════════════");
logMessage("Cleanup completed!");
logMessage("  Files deleted: {$deletedFiles}");
logMessage("  Folders deleted: {$deletedFolders}");
logMessage("  Comments deleted: {$deletedComments}");
logMessage("═══════════════════════════════════════════");
