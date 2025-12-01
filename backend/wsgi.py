"""
Kurzzeitparker - Flask Backend
Temporary file sharing application
"""

import os
import json
import random
import string
import zipfile
import tempfile
from datetime import datetime, timedelta
from functools import wraps
from flask import Flask, request, jsonify, send_file, send_from_directory
from flask_cors import CORS
from werkzeug.utils import secure_filename

# Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.join(BASE_DIR, 'data')
UPLOADS_DIR = os.path.join(BASE_DIR, 'uploads')
STATIC_DIR = os.path.dirname(BASE_DIR)  # Parent directory for static files
MAX_STORAGE_BYTES = 6 * 1024 * 1024 * 1024  # 6 GB
EXPIRY_DAYS = 7

# Ensure directories exist
os.makedirs(DATA_DIR, exist_ok=True)
os.makedirs(UPLOADS_DIR, exist_ok=True)

# Initialize Flask app
app = Flask(__name__, static_folder=STATIC_DIR, static_url_path='')
CORS(app)

# Configure upload limits
app.config['MAX_CONTENT_LENGTH'] = 500 * 1024 * 1024  # 500 MB per request

# WSGI application
application = app


# ============== Helper Functions ==============

def generate_link(length=None):
    """Generate a cryptic link (20-27 characters)"""
    if length is None:
        length = random.randint(20, 27)
    chars = string.ascii_letters + string.digits
    return ''.join(random.choice(chars) for _ in range(length))


def generate_id():
    """Generate unique ID"""
    return datetime.now().strftime('%Y%m%d%H%M%S') + ''.join(random.choices(string.ascii_lowercase, k=6))


def load_json(filename):
    """Load JSON file from data directory"""
    filepath = os.path.join(DATA_DIR, filename)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            return json.load(f)
    return []


def save_json(filename, data):
    """Save data to JSON file"""
    filepath = os.path.join(DATA_DIR, filename)
    with open(filepath, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)


def get_days_remaining(created_at):
    """Calculate days remaining until deletion"""
    created = datetime.strptime(created_at, '%Y-%m-%d %H:%M:%S')
    expiry = created + timedelta(days=EXPIRY_DAYS)
    remaining = (expiry - datetime.now()).days
    return max(0, remaining + 1)


def get_comment_count(item_id):
    """Get number of comments for an item"""
    comments = load_json('comments.json')
    return sum(1 for c in comments if c['item_id'] == item_id)


def calculate_folder_size(folder_id):
    """Calculate total size of files in folder"""
    files = load_json('files.json')
    return sum(f['size'] for f in files if f['folder_id'] == folder_id)


def calculate_total_storage():
    """Calculate total storage used"""
    files = load_json('files.json')
    return sum(f['size'] for f in files)


def format_datetime():
    """Get current datetime string"""
    return datetime.now().strftime('%Y-%m-%d %H:%M:%S')


# ============== Routes ==============

@app.route('/')
def index():
    """Serve the main page"""
    return send_from_directory(STATIC_DIR, 'index.html')


@app.route('/api/create_folder', methods=['POST'])
def create_folder():
    """Create a new folder"""
    data = request.get_json() or request.form
    name = data.get('name', '').strip()

    if not name:
        return jsonify({'error': 'Name ist erforderlich'}), 400

    folders = load_json('folders.json')
    link = generate_link()

    folder = {
        'id': generate_id(),
        'name': name,
        'link': link,
        'created_at': format_datetime(),
        'permanent': False
    }

    folders.append(folder)
    save_json('folders.json', folders)

    # Create uploads directory for folder
    folder_dir = os.path.join(UPLOADS_DIR, folder['id'])
    os.makedirs(folder_dir, exist_ok=True)

    return jsonify({'success': True, 'folder': folder})


@app.route('/api/get_folder')
def get_folder():
    """Get folder by link"""
    link = request.args.get('link', '')

    if not link:
        return jsonify({'error': 'Link ist erforderlich'}), 400

    folders = load_json('folders.json')
    folder = next((f for f in folders if f['link'] == link), None)

    if not folder:
        return jsonify({'error': 'Ordner nicht gefunden'}), 404

    # Add computed properties
    folder = folder.copy()
    folder['size'] = calculate_folder_size(folder['id'])
    folder['days_remaining'] = get_days_remaining(folder['created_at'])
    folder['comment_count'] = get_comment_count(folder['id'])

    # Get files in folder
    files = load_json('files.json')
    folder_files = []
    for f in files:
        if f['folder_id'] == folder['id']:
            file_copy = f.copy()
            file_copy['days_remaining'] = get_days_remaining(f['created_at'])
            file_copy['comment_count'] = get_comment_count(f['id'])
            folder_files.append(file_copy)

    # Sort by newest first
    folder_files.sort(key=lambda x: x['created_at'], reverse=True)
    folder['files'] = folder_files

    return jsonify({'success': True, 'folder': folder})


@app.route('/api/upload', methods=['POST'])
def upload_file():
    """Upload a file to a folder"""
    folder_id = request.form.get('folder_id', '')
    relative_path = request.form.get('relative_path', '')

    if not folder_id:
        return jsonify({'error': 'Folder ID ist erforderlich'}), 400

    if 'file' not in request.files:
        return jsonify({'error': 'Keine Datei hochgeladen'}), 400

    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'Keine Datei ausgewählt'}), 400

    # Check storage limit
    file.seek(0, 2)  # Seek to end
    file_size = file.tell()
    file.seek(0)  # Seek back to start

    current_storage = calculate_total_storage()
    if current_storage + file_size > MAX_STORAGE_BYTES:
        return jsonify({'error': '6 GB Speicherlimit überschritten!'}), 400

    # Create folder directory if not exists
    folder_dir = os.path.join(UPLOADS_DIR, folder_id)
    os.makedirs(folder_dir, exist_ok=True)

    # Generate unique filename
    file_id = generate_id()
    original_name = secure_filename(file.filename) or 'unnamed'
    # Keep original name for display, use ID for storage
    ext = os.path.splitext(original_name)[1]
    stored_name = file_id + ext
    file_path = os.path.join(folder_dir, stored_name)

    # Save file
    file.save(file_path)
    actual_size = os.path.getsize(file_path)

    # Create file record
    files = load_json('files.json')
    file_record = {
        'id': file_id,
        'folder_id': folder_id,
        'name': file.filename,  # Original name with special chars
        'relative_path': relative_path,
        'stored_name': stored_name,
        'size': actual_size,
        'mime_type': file.content_type or 'application/octet-stream',
        'created_at': format_datetime(),
        'permanent': False,
        'is_new': True
    }

    files.append(file_record)
    save_json('files.json', files)

    # Add computed properties for response
    file_record['days_remaining'] = EXPIRY_DAYS
    file_record['comment_count'] = 0

    return jsonify({'success': True, 'file': file_record})


@app.route('/api/delete_file', methods=['POST'])
def delete_file():
    """Delete a file"""
    data = request.get_json() or request.form
    file_id = data.get('file_id', '')

    if not file_id:
        return jsonify({'error': 'File ID ist erforderlich'}), 400

    files = load_json('files.json')
    file_to_delete = None
    new_files = []

    for f in files:
        if f['id'] == file_id:
            file_to_delete = f
        else:
            new_files.append(f)

    if file_to_delete:
        # Delete physical file
        file_path = os.path.join(UPLOADS_DIR, file_to_delete['folder_id'], file_to_delete['stored_name'])
        if os.path.exists(file_path):
            os.remove(file_path)

        save_json('files.json', new_files)

        # Delete associated comments
        comments = load_json('comments.json')
        comments = [c for c in comments if c['item_id'] != file_id]
        save_json('comments.json', comments)

    return jsonify({'success': True})


@app.route('/api/toggle_permanent', methods=['POST'])
def toggle_permanent():
    """Toggle permanent flag on a file"""
    data = request.get_json() or request.form
    file_id = data.get('file_id', '')

    if not file_id:
        return jsonify({'error': 'File ID ist erforderlich'}), 400

    files = load_json('files.json')
    for f in files:
        if f['id'] == file_id:
            f['permanent'] = not f.get('permanent', False)
            break

    save_json('files.json', files)
    return jsonify({'success': True})


@app.route('/api/get_comments')
def get_comments():
    """Get comments for an item"""
    item_id = request.args.get('item_id', '')

    if not item_id:
        return jsonify({'error': 'Item ID ist erforderlich'}), 400

    comments = load_json('comments.json')
    item_comments = [c for c in comments if c['item_id'] == item_id]

    # Sort by newest first
    item_comments.sort(key=lambda x: x['created_at'], reverse=True)

    return jsonify({'success': True, 'comments': item_comments})


@app.route('/api/add_comment', methods=['POST'])
def add_comment():
    """Add a comment to an item"""
    data = request.get_json() or request.form
    item_id = data.get('item_id', '')
    text = data.get('text', '').strip()

    if not item_id or not text:
        return jsonify({'error': 'Item ID und Text sind erforderlich'}), 400

    comments = load_json('comments.json')
    comment = {
        'id': generate_id(),
        'item_id': item_id,
        'text': text,
        'created_at': format_datetime()
    }

    comments.append(comment)
    save_json('comments.json', comments)

    return jsonify({'success': True, 'comment': comment})


@app.route('/api/download')
def download_file():
    """Download a single file"""
    file_id = request.args.get('file_id', '')

    if not file_id:
        return jsonify({'error': 'File ID ist erforderlich'}), 400

    files = load_json('files.json')
    file_record = next((f for f in files if f['id'] == file_id), None)

    if not file_record:
        return jsonify({'error': 'Datei nicht gefunden'}), 404

    file_path = os.path.join(UPLOADS_DIR, file_record['folder_id'], file_record['stored_name'])

    if not os.path.exists(file_path):
        return jsonify({'error': 'Datei nicht auf Disk gefunden'}), 404

    return send_file(
        file_path,
        as_attachment=True,
        download_name=file_record['name']
    )


@app.route('/api/download_zip')
def download_zip():
    """Download entire folder as ZIP"""
    folder_id = request.args.get('folder_id', '')

    if not folder_id:
        return jsonify({'error': 'Folder ID ist erforderlich'}), 400

    folders = load_json('folders.json')
    folder = next((f for f in folders if f['id'] == folder_id), None)

    if not folder:
        return jsonify({'error': 'Ordner nicht gefunden'}), 404

    files = load_json('files.json')
    folder_files = [f for f in files if f['folder_id'] == folder_id]

    if not folder_files:
        return jsonify({'error': 'Keine Dateien im Ordner'}), 404

    # Create ZIP file
    temp_zip = tempfile.NamedTemporaryFile(delete=False, suffix='.zip')

    with zipfile.ZipFile(temp_zip.name, 'w', zipfile.ZIP_DEFLATED) as zf:
        for file in folder_files:
            file_path = os.path.join(UPLOADS_DIR, folder_id, file['stored_name'])
            if os.path.exists(file_path):
                zf.write(file_path, file['name'])

    return send_file(
        temp_zip.name,
        as_attachment=True,
        download_name=f"{folder['name']}.zip"
    )


@app.route('/api/poll')
def poll_folder():
    """Poll for live updates"""
    folder_id = request.args.get('folder_id', '')

    if not folder_id:
        return jsonify({'error': 'Folder ID ist erforderlich'}), 400

    folders = load_json('folders.json')
    folder = next((f for f in folders if f['id'] == folder_id), None)

    if not folder:
        return jsonify({'error': 'Ordner nicht gefunden'}), 404

    folder = folder.copy()
    folder['size'] = calculate_folder_size(folder['id'])
    folder['days_remaining'] = get_days_remaining(folder['created_at'])
    folder['comment_count'] = get_comment_count(folder['id'])

    # Get files
    files = load_json('files.json')
    folder_files = []
    for f in files:
        if f['folder_id'] == folder_id:
            file_copy = f.copy()
            file_copy['days_remaining'] = get_days_remaining(f['created_at'])
            file_copy['comment_count'] = get_comment_count(f['id'])
            folder_files.append(file_copy)

    folder_files.sort(key=lambda x: x['created_at'], reverse=True)
    folder['files'] = folder_files

    return jsonify({
        'success': True,
        'folder': folder,
        'timestamp': format_datetime()
    })


@app.route('/api/clear_new_badge', methods=['POST'])
def clear_new_badge():
    """Clear the 'new' badge from a file"""
    data = request.get_json() or request.form
    file_id = data.get('file_id', '')

    if not file_id:
        return jsonify({'error': 'File ID ist erforderlich'}), 400

    files = load_json('files.json')
    for f in files:
        if f['id'] == file_id:
            f['is_new'] = False
            break

    save_json('files.json', files)
    return jsonify({'success': True})


@app.route('/api/cleanup', methods=['POST'])
def cleanup():
    """Manual cleanup of expired files (can also be called via cron)"""
    expiry_time = datetime.now() - timedelta(days=EXPIRY_DAYS)

    files = load_json('files.json')
    folders = load_json('folders.json')
    comments = load_json('comments.json')

    deleted_files = 0
    deleted_file_ids = []
    new_files = []

    for f in files:
        created = datetime.strptime(f['created_at'], '%Y-%m-%d %H:%M:%S')
        if created < expiry_time and not f.get('permanent', False):
            # Delete physical file
            file_path = os.path.join(UPLOADS_DIR, f['folder_id'], f['stored_name'])
            if os.path.exists(file_path):
                os.remove(file_path)
            deleted_file_ids.append(f['id'])
            deleted_files += 1
        else:
            new_files.append(f)

    save_json('files.json', new_files)

    # Delete comments for deleted files
    comments = [c for c in comments if c['item_id'] not in deleted_file_ids]
    save_json('comments.json', comments)

    return jsonify({
        'success': True,
        'deleted_files': deleted_files
    })


@app.route('/api/seed', methods=['POST'])
def seed_data():
    """Create seed data for testing"""
    # Create example folder
    folder_id = 'seed_folder_001'
    folder_link = 'BeispielOrdnerXYZ2024abc'

    folders = [{
        'id': folder_id,
        'name': 'Beispiel-Projektordner',
        'link': folder_link,
        'created_at': format_datetime(),
        'permanent': False
    }]
    save_json('folders.json', folders)

    # Create folder directory
    folder_dir = os.path.join(UPLOADS_DIR, folder_id)
    os.makedirs(folder_dir, exist_ok=True)

    # Create example file
    file_id = 'seed_file_001'
    file_content = """Willkommen bei Kurzzeitparker! 🎉

Dies ist eine Beispieldatei, die zeigt, wie der Dienst funktioniert.

Funktionen:
• Dateien hochladen per Drag & Drop
• Dateien werden nach 7 Tagen automatisch gelöscht
• Mit dem 🔒 Button können Dateien dauerhaft gespeichert werden
• Kommentare können zu jeder Datei hinzugefügt werden
• Der gesamte Ordner kann als ZIP heruntergeladen werden

Viel Spaß beim Teilen! 🚀
"""

    file_path = os.path.join(folder_dir, file_id + '.txt')
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(file_content)

    files = [{
        'id': file_id,
        'folder_id': folder_id,
        'name': 'willkommen.txt',
        'relative_path': '',
        'stored_name': file_id + '.txt',
        'size': len(file_content.encode('utf-8')),
        'mime_type': 'text/plain',
        'created_at': format_datetime(),
        'permanent': False,
        'is_new': True
    }]
    save_json('files.json', files)

    # Create example comments
    comments = [
        {
            'id': 'seed_comment_001',
            'item_id': file_id,
            'text': 'Das ist ein Beispielkommentar! Hier können alle Nutzer Kommentare hinterlassen. 💬',
            'created_at': format_datetime()
        },
        {
            'id': 'seed_comment_002',
            'item_id': folder_id,
            'text': 'Willkommen in diesem gemeinsamen Ordner! Teilt diesen Link mit anderen, um Dateien auszutauschen.',
            'created_at': (datetime.now() - timedelta(hours=1)).strftime('%Y-%m-%d %H:%M:%S')
        }
    ]
    save_json('comments.json', comments)

    return jsonify({
        'success': True,
        'message': 'Seed-Daten erstellt',
        'folder_link': folder_link
    })


# Error handlers
@app.errorhandler(413)
def too_large(e):
    return jsonify({'error': 'Datei ist zu groß (max 500 MB pro Upload)'}), 413


@app.errorhandler(404)
def not_found(e):
    return jsonify({'error': 'Nicht gefunden'}), 404


@app.errorhandler(500)
def server_error(e):
    return jsonify({'error': 'Serverfehler'}), 500


# Development server
if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
