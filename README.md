# Kurzzeitparker

Temporäres Datei-Sharing im macOS-Stil - ohne Login, mit automatischer Löschung nach 7 Tagen.

## Features

- **Ordnerverwaltung**: Erstelle Ordner mit kryptischen Links (20-27 Zeichen)
- **Drag & Drop Upload**: Dateien und ganze Ordner hochladen
- **Live-Updates**: Änderungen werden für alle Nutzer in Echtzeit angezeigt
- **Kommentare**: Kommentiere Dateien und Ordner
- **Automatische Löschung**: Dateien werden nach 7 Tagen automatisch gelöscht
- **Dauerhaft-Flag**: Dateien können als dauerhaft markiert werden
- **ZIP-Download**: Gesamten Ordner als ZIP herunterladen
- **6 GB Limit**: Maximale Speichergrenze pro Upload

## Installation

1. Dateien auf einen PHP-fähigen Webserver (z.B. alwaysdata) hochladen
2. Seed-Daten erstellen: `php seed.php`
3. Cron-Job für automatische Löschung einrichten:
   ```
   0 3 * * * php /pfad/zu/cron.php
   ```

## Dateistruktur

```
Kurzzeitparker/
├── index.php          # Frontend (HTML/CSS/JS)
├── api.php            # Backend API
├── seed.php           # Seed-Daten erstellen
├── cron.php           # Automatische Löschung
├── .htaccess          # Apache-Konfiguration
├── data/              # JSON-Datenbank
│   ├── folders.json
│   ├── files.json
│   └── comments.json
└── uploads/           # Hochgeladene Dateien
```

## API Endpoints

| Action | Method | Parameter |
|--------|--------|-----------|
| `create_folder` | POST | name |
| `get_folder` | GET | link |
| `upload` | POST | folder_id, file |
| `delete_file` | POST | file_id |
| `toggle_permanent` | POST | file_id |
| `get_comments` | GET | item_id |
| `add_comment` | POST | item_id, text |
| `download` | GET | file_id |
| `download_zip` | GET | folder_id |
| `poll` | GET | folder_id |

## Beispiel-Link

Nach dem Ausführen von `seed.php`:
```
index.php?f=BeispielOrdnerXYZ2024abc
```

## Technologie

- **Backend**: PHP 7.4+
- **Frontend**: Vanilla JavaScript, CSS3
- **Datenbank**: JSON-Dateien
- **Design**: macOS-inspiriert, hell und bunt

## Sicherheit

- Keine PHP-Ausführung im uploads-Ordner
- Kein direkter Zugriff auf data-Ordner
- XSS-Schutz durch Content-Disposition Header
- Kryptische Links für Ordner

## Lizenz

MIT License
