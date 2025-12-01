# Kurzzeitparker

Temporäres Datei-Sharing im macOS-Stil - ohne Login, mit automatischer Löschung nach 7 Tagen.

**Live**: https://kurzzeitparker.yellow-boat.org

## Features

- **Ordnerverwaltung**: Erstelle Ordner mit kryptischen Links (20-27 Zeichen)
- **Drag & Drop Upload**: Dateien und ganze Ordner hochladen
- **Live-Updates**: Änderungen werden für alle Nutzer in Echtzeit angezeigt
- **Kommentare**: Kommentiere Dateien und Ordner
- **Automatische Löschung**: Dateien werden nach 7 Tagen automatisch gelöscht
- **Dauerhaft-Flag**: Dateien können als dauerhaft markiert werden
- **ZIP-Download**: Gesamten Ordner als ZIP herunterladen
- **6 GB Limit**: Maximale Speichergrenze pro Upload

## Dateistruktur

```
Kurzzeitparker/
├── index.html              # Frontend (HTML/CSS/JS)
├── backend/
│   ├── wsgi.py            # Flask WSGI Application
│   ├── requirements.txt   # Python Dependencies
│   ├── data/              # JSON-Datenbank
│   │   ├── folders.json
│   │   ├── files.json
│   │   └── comments.json
│   └── uploads/           # Hochgeladene Dateien
└── README.md
```

## Installation (alwaysdata)

1. Repository klonen
2. Python virtualenv erstellen und aktivieren
3. Dependencies installieren:
   ```bash
   pip install -r backend/requirements.txt
   ```
4. WSGI-Anwendung konfigurieren:
   - Type: Python WSGI
   - Application path: `/home/user/kurzzeitparker/backend/wsgi.py:application`
   - Working directory: `/home/user/kurzzeitparker/backend`

5. Seed-Daten erstellen (optional):
   ```bash
   curl -X POST https://kurzzeitparker.yellow-boat.org/api/seed
   ```

6. Cron-Job für automatische Löschung:
   ```bash
   curl -X POST https://kurzzeitparker.yellow-boat.org/api/cleanup
   ```

## API Endpoints

| Endpoint | Method | Parameter | Beschreibung |
|----------|--------|-----------|--------------|
| `/api/create_folder` | POST | name | Neuen Ordner erstellen |
| `/api/get_folder` | GET | link | Ordner abrufen |
| `/api/upload` | POST | folder_id, file | Datei hochladen |
| `/api/delete_file` | POST | file_id | Datei löschen |
| `/api/toggle_permanent` | POST | file_id | Dauerhaft-Flag umschalten |
| `/api/get_comments` | GET | item_id | Kommentare abrufen |
| `/api/add_comment` | POST | item_id, text | Kommentar hinzufügen |
| `/api/download` | GET | file_id | Datei herunterladen |
| `/api/download_zip` | GET | folder_id | Ordner als ZIP |
| `/api/poll` | GET | folder_id | Live-Updates |
| `/api/seed` | POST | - | Seed-Daten erstellen |
| `/api/cleanup` | POST | - | Abgelaufene Dateien löschen |

## Beispiel-Link

Nach dem Erstellen von Seed-Daten:
```
https://kurzzeitparker.yellow-boat.org/?f=BeispielOrdnerXYZ2024abc
```

## Technologie

- **Backend**: Python 3.11 + Flask
- **Frontend**: Vanilla JavaScript, CSS3
- **Datenbank**: JSON-Dateien
- **Hosting**: alwaysdata (WSGI)
- **Design**: macOS-inspiriert, hell und bunt

## Lizenz

MIT License
