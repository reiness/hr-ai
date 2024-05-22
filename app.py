from flask import Flask, request, jsonify
import os
import logging

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Base directory for the Laravel storage
LARAVEL_STORAGE_BASE = "D:\\coding-project\\hr-ai\\hr-web\\storage\\app\\public"

def get_pdf_size(relative_path):
    try:
        # Remove any leading slashes in relative_path
        relative_path = relative_path.lstrip('/')
        full_path = os.path.join(LARAVEL_STORAGE_BASE, relative_path)
        app.logger.debug(f"Checking size for: {full_path}")

        # Check if the constructed path is correct
        if os.path.exists(full_path):
            app.logger.debug(f"File exists: {full_path}")
            size = os.path.getsize(full_path)
            app.logger.debug(f"Size of {full_path}: {size}")
            return size
        else:
            app.logger.error(f"File not found: {full_path}")
            return 0
    except Exception as e:
        app.logger.error(f"Error getting size for {full_path}: {e}")
        return 0

@app.route('/process', methods=['POST'])
def process():
    # Extract data from request
    data = request.get_json()
    app.logger.debug(f"Received data: {data}")

    if not data or 'cvs' not in data:
        return jsonify({"error": "Invalid data"}), 400

    cvs = data.get('cvs', [])
    app.logger.debug(f"CVs before processing: {cvs}")

    # Process the data: sort PDFs by their size
    for cv in cvs:
        cv['size'] = get_pdf_size(cv['file_path'])
        app.logger.debug(f"Processed CV: {cv}")

    sorted_cvs = sorted(cvs, key=lambda x: x['size'])
    app.logger.debug(f"Sorted CVs: {sorted_cvs}")

    # Prepare response
    response = {
        'batch_id': data.get('batch_id'),
        'sorted_cvs': sorted_cvs
    }
    app.logger.debug(f"Response: {response}")

    return jsonify(response), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
