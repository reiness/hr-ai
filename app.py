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

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Base directory for the Laravel storage
LARAVEL_STORAGE_BASE = "D:\\coding-project\\hr-ai\\hr-web\\storage\\app\\public"

# Preprocessing
nltk.download('stopwords')
stop_words = set(stopwords.words('english'))

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

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
