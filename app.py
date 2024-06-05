from flask import Flask, request, jsonify
import os
import logging
import re
import nltk
from nltk.corpus import stopwords
from sklearn.metrics.pairwise import cosine_similarity
from gensim.models import LsiModel, Word2Vec
from gensim.corpora import Dictionary
from gensim.matutils import corpus2csc
import numpy as np
from rank_bm25 import BM25Okapi
from PyPDF2 import PdfReader

# gemini
import pathlib
import textwrap
import google.generativeai as genai
from IPython.display import display
from IPython.display import Markdown



app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Base directory for the Laravel storage
LARAVEL_STORAGE_BASE = "D:\\coding-project\\hr-ai\\hr-web\\storage\\app\\public"

# Preprocessing
nltk.download('stopwords')
stop_words = set(stopwords.words('english'))




json_format = """
Format JSON ini tidak pakem, tergantung berapa CV yang diproses. Satu CV adalah satu objek yang berbeda dan terdapat 1 objek inti yaitu comparison.

Formatnya adalah sebagai berikut

{
  "comparison": {
    "aditya_pratama": {
      "experience": "Stronger technical experience with focus on software development.",
      "education": "Bachelor's degree in Computer Science.",
      "skills": "Strong technical skills in programming languages, databases, and frameworks.",
      "language": "Fluent in English.",
      "overall": "Strong candidate for technical roles."
    },
    "siti_aminah": {
      "experience": "Strong administrative experience with focus on office management.",
      "education": "Bachelor's degree in Business Administration.",
      "skills": "Proficient in Microsoft Office and administration tasks.",
      "language": "Intermediate English.",
      "overall": "Strong candidate for administrative roles."
    }
  },
  "aditya_pratama": {
    "personal_information": {
      "name": "Aditya Pratama",
      "birth_place": "Jakarta",
      "birth_date": "10 Maret 1990",
      "address": "Jl. Sudirman No. 45, Jakarta Selatan",
      "phone": "0812-3456-7890",
      "email": "aditya.pratama@example.com"
    },
    "education": [
      {
        "institution": "Universitas Indonesia",
        "degree": "Sarjana Teknik Informatika",
        "year": "2010-2014"
      },
      {
        "institution": "SMA Negeri 8 Jakarta",
        "year": "2007-2010"
      }
    ],
    "experience": [
      {
        "company": "PT. Teknologi Nusantara",
        "position": "Software Engineer",
        "year": "2016-sekarang",
        "description": "Mengembangkan aplikasi web dan mobile, bekerja dengan tim dalam proyek pengembangan sistem informasi"
      },
      {
        "company": "PT. Solusi Digital",
        "position": "Junior Programmer",
        "year": "2014-2016",
        "description": "Membantu dalam pengembangan aplikasi internal perusahaan, menangani maintenance dan perbaikan bug"
      }
    ],
    "skills": {
      "programming_languages": "Java, Python, JavaScript",
      "database": "MySQL, PostgreSQL",
      "framework": "Spring, Django, React"
    },
    "certification": "Sertifikasi Java Programming (Oracle)",
    "language": {
      "indonesia": "Bahasa Ibu",
      "english": "Lancar"
    }
  },
  "siti_aminah": {
    "personal_information": {
      "name": "Siti Aminah",
      "birth_place": "Bandung",
      "birth_date": "25 Juli 1992",
      "address": "Jl. Merdeka No. 22, Bandung",
      "phone": "0812-9876-5432",
      "email": "siti.aminah@example.com"
    },
    "education": [
      {
        "institution": "Universitas Padjadjaran",
        "degree": "Sarjana Administrasi Bisnis",
        "year": "2011-2015"
      },
      {
        "institution": "SMA Negeri 3 Bandung",
        "year": "2008-2011"
      }
    ],
    "experience": [
      {
        "company": "PT. Global Admin",
        "position": "Administrasi Kantor",
        "year": "2017-sekarang",
        "description": "Mengelola dokumen dan arsip perusahaan, menangani korespondensi dan komunikasi internal"
      },
      {
        "company": "PT. Sukses Bersama",
        "position": "Staff Administrasi",
        "year": "2015-2017",
        "description": "Membantu dalam pengelolaan data kepegawaian, menyusun laporan keuangan bulanan"
      }
    ],
    "skills": {
      "microsoft_office": "Word, Excel, PowerPoint",
      "administration": "Manajemen Arsip dan Dokumen, Korespondensi Bisnis"
    },
    "certification": "Sertifikasi Manajemen Administrasi (LSP)",
    "language": {
      "indonesia": "Bahasa Ibu",
      "english": "Menengah"
    }
  }
}

"""
def to_markdown(text):
  text = text.replace('•', '  *')
  return Markdown(textwrap.indent(text, '> ', predicate=lambda _: True))


def generate_summary(api_key, base_prompt):
    
    genai.configure(api_key=api_key)   
    model = genai.GenerativeModel('gemini-1.5-flash')
    response = model.generate_content(base_prompt)
    # resp = to_markdown(response.text)
    return response.text

def preprocess(text):
    text = re.sub(r'\W', ' ', text)
    text = re.sub(r'\s+', ' ', text)
    text = text.lower()
    text = ' '.join([word for word in text.split() if word not in stop_words])
    return text

def read_pdf_content(file_path):
    full_path = os.path.join(LARAVEL_STORAGE_BASE, file_path.lstrip('/'))
    content = ""
    try:
        with open(full_path, 'rb') as file:
            reader = PdfReader(file)
            for page in reader.pages:
                content += page.extract_text()
    except Exception as e:
        app.logger.error(f"Error reading {file_path}: {e}")
    return content

def query_expansion(query, word2vec_model, top_n=3):
    query_terms = query.split()
    expanded_query_terms = set(query_terms)
    for term in query_terms:
        if term in word2vec_model.wv:
            similar_terms = word2vec_model.wv.most_similar(term, topn=top_n)
            expanded_query_terms.update([sim[0] for sim in similar_terms])
    return ' '.join(expanded_query_terms)

def rank_documents(query, bm25, lsi_model, lsi_matrix, dictionary, word2vec_model):
    expanded_query = query_expansion(query, word2vec_model)
    query_bm25 = expanded_query.split()
    bm25_scores = bm25.get_scores(query_bm25)
    query_bow = dictionary.doc2bow(expanded_query.split())
    query_lsi = lsi_model[query_bow]
    query_lsi_vec = corpus2csc([query_lsi]).transpose()
    lsi_scores = cosine_similarity(query_lsi_vec, lsi_matrix)[0]
    lambda_param = 0.5
    combined_scores = lambda_param * bm25_scores + (1 - lambda_param) * lsi_scores
    return combined_scores

@app.route('/process', methods=['POST'])
def process():
    data = request.get_json()
    app.logger.debug(f"Received data: {data}")

    if not data or 'cvs' not in data or 'user_input' not in data:
        return jsonify({"error": "Invalid data"}), 400

    user_input = data['user_input']

    # Read and preprocess the PDF content
    cvs = data.get('cvs', [])
    cv_texts = []
    for cv in cvs:
        pdf_content = read_pdf_content(cv['file_path'])
        if pdf_content:
            processed_content = preprocess(pdf_content)
            cv_texts.append(processed_content)
        else:
            cv_texts.append("")

    # Ensure the corpus is not empty before initializing models
    tokenized_corpus = [doc.split() for doc in cv_texts if doc]
    if not tokenized_corpus:
        return jsonify({"error": "No valid CV content found"}), 400

    # Prepare the model input
    bm25 = BM25Okapi(tokenized_corpus)
    dictionary = Dictionary(tokenized_corpus)
    corpus = [dictionary.doc2bow(doc) for doc in tokenized_corpus]
    lsi_model = LsiModel(corpus, id2word=dictionary, num_topics=300)
    lsi_corpus = lsi_model[corpus]
    lsi_matrix = corpus2csc(lsi_corpus).transpose()
    word2vec_model = Word2Vec(sentences=tokenized_corpus, vector_size=100, window=5, min_count=1, workers=4)

    # Rank the documents
    combined_scores = rank_documents(user_input, bm25, lsi_model, lsi_matrix, dictionary, word2vec_model)

    for i, cv in enumerate(cvs):
        if i < len(combined_scores):
            cv['combined_score'] = combined_scores[i]
        else:
            cv['combined_score'] = 0
        app.logger.debug(f"Processed CV: {cv}")

    sorted_cvs = sorted(cvs, key=lambda x: x['combined_score'], reverse=True)
    app.logger.debug(f"Sorted CVs: {sorted_cvs}")

    response = {
        'batch_id': data.get('batch_id'),
        'sorted_cvs': sorted_cvs
    }
    app.logger.debug(f"Response: {response}")

    return jsonify(response), 200





@app.route('/gemini', methods=['POST'])
def gemini():
    data = request.get_json()
    app.logger.debug(f"Received data for Gemini: {data}")

    if not data or 'cvs' not in data or 'api_key' not in data:
        return jsonify({"error": "Invalid data"}), 400

    api_key = data['api_key']
    cvs = data['cvs']
    cv_texts = []


    for cv in cvs:
        pdf_content = read_pdf_content(cv['file_path'])
        if pdf_content:
            cv_texts.append(pdf_content)
    print("DEBUGGG[0]", cv_texts[0])
    print("DEBUGGG[1]", cv_texts[1])
    print("DEBUGGG[2]", cv_texts[2])

    base_prompt = f"""
Ringkaslah kumpulan CV berikut ini serta komparasikan mereka.
Bentuklah dalam sebuah JSON agar mudah diterima oleh website. 
CV pertama:{cv_texts[0]}, CV kedua {cv_texts[1]}, CV ketiga {cv_texts[2]}. 
Format JSON nya adalah sebagai berikut: {json_format} .
Jika ada kasus data tidak ada, maka buat nilai dalam JSON tersebut Unknown.
JANGAN MENAMPILKAN HASIL LAIN SELAIN JSON!
"""
    # Process with Google Gemini (using main.ipynb logic)
    summaries = []
    summary = generate_summary(api_key, base_prompt)  # Pass the API key to the function
    summaries.append(summary)

    response = {
        'summaries': summaries
    }

    # return jsonify(response), 200
    return response



if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
