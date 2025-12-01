<?php
/**
 * Seed Data Script
 * Creates example folder, file, and comment
 */

define('DATA_DIR', __DIR__ . '/data');
define('UPLOADS_DIR', __DIR__ . '/uploads');

// Ensure directories exist
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(UPLOADS_DIR)) mkdir(UPLOADS_DIR, 0755, true);

// Generate cryptic link (20-27 characters)
function generateLink($length = 24) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $link = '';
    for ($i = 0; $i < $length; $i++) {
        $link .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $link;
}

// Create example folder
$folderId = 'seed_folder_001';
$folderLink = 'BeispielOrdnerXYZ2024abc';

$folders = [
    [
        'id' => $folderId,
        'name' => 'Beispiel-Projektordner',
        'link' => $folderLink,
        'created_at' => date('Y-m-d H:i:s'),
        'permanent' => false
    ]
];

file_put_contents(DATA_DIR . '/folders.json', json_encode($folders, JSON_PRETTY_PRINT));
echo "✅ Beispielordner erstellt\n";
echo "   Link: ?f={$folderLink}\n\n";

// Create uploads directory for folder
$folderDir = UPLOADS_DIR . '/' . $folderId;
if (!is_dir($folderDir)) {
    mkdir($folderDir, 0755, true);
}

// Create example file
$fileId = 'seed_file_001';
$fileName = 'willkommen.txt';
$fileContent = "Willkommen bei Kurzzeitparker! 🎉

Dies ist eine Beispieldatei, die zeigt, wie der Dienst funktioniert.

Funktionen:
• Dateien hochladen per Drag & Drop
• Dateien werden nach 7 Tagen automatisch gelöscht
• Mit dem 🔒 Button können Dateien dauerhaft gespeichert werden
• Kommentare können zu jeder Datei hinzugefügt werden
• Der gesamte Ordner kann als ZIP heruntergeladen werden

Viel Spaß beim Teilen! 🚀
";

$filePath = $folderDir . '/' . $fileId . '.txt';
file_put_contents($filePath, $fileContent);

$files = [
    [
        'id' => $fileId,
        'folder_id' => $folderId,
        'name' => $fileName,
        'relative_path' => '',
        'stored_name' => $fileId . '.txt',
        'size' => strlen($fileContent),
        'mime_type' => 'text/plain',
        'created_at' => date('Y-m-d H:i:s'),
        'permanent' => false,
        'is_new' => true
    ]
];

file_put_contents(DATA_DIR . '/files.json', json_encode($files, JSON_PRETTY_PRINT));
echo "✅ Beispieldatei erstellt\n";
echo "   Name: {$fileName}\n\n";

// Create example comment
$comments = [
    [
        'id' => 'seed_comment_001',
        'item_id' => $fileId,
        'text' => 'Das ist ein Beispielkommentar! Hier können alle Nutzer Kommentare hinterlassen. 💬',
        'created_at' => date('Y-m-d H:i:s')
    ],
    [
        'id' => 'seed_comment_002',
        'item_id' => $folderId,
        'text' => 'Willkommen in diesem gemeinsamen Ordner! Teilt diesen Link mit anderen, um Dateien auszutauschen.',
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
    ]
];

file_put_contents(DATA_DIR . '/comments.json', json_encode($comments, JSON_PRETTY_PRINT));
echo "✅ Beispielkommentare erstellt\n\n";

echo "═══════════════════════════════════════════\n";
echo "🎉 Seed-Daten erfolgreich erstellt!\n";
echo "═══════════════════════════════════════════\n\n";
echo "Öffne die App mit diesem Link:\n";
echo "  → index.php?f={$folderLink}\n\n";
