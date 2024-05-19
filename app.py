from flask import Flask, request, jsonify
from pdfminer.high_level import extract_text
 

app = Flask(__name__)
def extract_text_from_pdf(pdf_path):
    return extract_text(pdf_path)





@app.route('/predict', methods=['POST'])
def predict():
    # Extract data from request
    data = request.get_json()


    A = int(data.get('A'))
    B = int(data.get('B'))
    C = int(data.get('C'))

    prediction = A + B + C

    # Prepare response
    response = {'prediction': prediction}

    return jsonify(response), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
