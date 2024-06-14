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
import google.generativeai as genai
from IPython.display import Markdown
import textwrap

from sklearn.feature_extraction.text import TfidfVectorizer
from gensim import corpora, models, similarities
import json

app = Flask(__name__)

# Configure logging
if not os.path.exists('logs'):
    os.makedirs('logs')

logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s %(levelname)s %(name)s %(threadName)s : %(message)s',
                    handlers=[logging.FileHandler("logs/error.log"),
                              logging.StreamHandler()])



# NEEDS TO BE CHANGED IN PRODUCTION
# LARAVEL_STORAGE_BASE = "D:\\coding-project\\hr-ai\\hr-web\\storage\\app\\public"
# LARAVEL_STORAGE_BASE = os.getenv('LARAVEL_STORAGE_BASE', 'D:\\coding-project\\hr-ai\\hr-web\\storage\\app\\public')
LARAVEL_STORAGE_BASE = '/var/www/storage/app/public'
logging.info(f"LARAVEL_STORAGE_BASE is set to: {LARAVEL_STORAGE_BASE}")

# Preprocessing
nltk.download('stopwords')
stop_words = set(stopwords.words('english'))
nltk.download('punkt')

json_format = """
Format JSON ini tidak pakem, tergantung berapa CV yang diproses. Satu CV adalah satu objek yang berbeda dan terdapat 1 objek inti yaitu comparison.

Formatnya adalah sebagai berikut

{
  "evaluation": {
    "candidate1": {
      "relevant_experience": {
        "score": 8,
        "feedback": "Candidate1 has extensive experience in data analysis and project management, which aligns well with the job requirements."
      },
      "career_aspirations": {
        "score": 7,
        "feedback": "Shows ambition for leadership roles and continuous professional development, though clarity is needed on long-term career goals."
      },
      "education_and_degrees": {
        "score": 9,
        "feedback": "Candidate1 holds a relevant degree from a reputable university with additional certifications in data analytics."
      },
      "skills_and_competencies": {
        "score": 8,
        "feedback": "Demonstrates proficiency in programming languages and data visualization tools, essential for the role."
      },
      "achievements_and_awards": {
        "score": 7,
        "feedback": "Received recognition in data science competitions, indicating strong analytical and problem-solving abilities."
      },
      "red_flags": {
        "score": 6,
        "feedback": "No significant red flags, though some employment gaps need clarification."
      },
      "personal_information_and_professionalism": {
        "score": 9,
        "feedback": "CV is well-organized and professionally presented, with accurate contact information."
      },
      "cultural_fit_and_language_proficiency": {
        "score": 8,
        "feedback": "Language proficiency and alignment with company culture are satisfactory."
      }
    },
    "candidate2": {
      // Evaluation criteria and scores for candidate2
    },
    "candidate3": {
      // Evaluation criteria and scores for candidate3
    }
  },
  "comparison": {
    "strengths": {
      "candidate1": "Has extensive experience in data analysis.",
      "candidate2": "Shows strong leadership potential.",
      "candidate3": "Highly proficient in multiple programming languages."
    },
    "areas_for_improvement": {
      "candidate1": "Needs clarity on long-term career goals.",
      "candidate2": "Lacks certifications in data analytics.",
      "candidate3": "Has some employment gaps that need explanation."
    },
    "overall_comparison": "Candidate1 has the most relevant experience, while Candidate2 demonstrates the highest potential for leadership. Candidate3 is highly skilled but needs to address employment gaps."
  }
}


"""

#####################################################################################
# def query_expansion(query, word2vec_model, top_n=3):
#     query_terms = query.split()
#     expanded_query_terms = set(query_terms)
#     for term in query_terms:
#         if term in word2vec_model.wv:
#             similar_terms = word2vec_model.wv.most_similar(term, topn=top_n)
#             expanded_query_terms.update([sim[0] for sim in similar_terms])
#     return ' '.join(expanded_query_terms)

# def rank_documents(query, bm25, lsi_model, lsi_matrix, dictionary, word2vec_model):
#     expanded_query = query_expansion(query, word2vec_model)
#     query_bm25 = expanded_query.split()
#     bm25_scores = bm25.get_scores(query_bm25)
#     query_bow = dictionary.doc2bow(expanded_query.split())
#     query_lsi = lsi_model[query_bow]
#     query_lsi_vec = corpus2csc([query_lsi]).transpose()
#     lsi_scores = cosine_similarity(query_lsi_vec, lsi_matrix)[0]
#     lambda_param = 0.5
#     combined_scores = lambda_param * bm25_scores + (1 - lambda_param) * lsi_scores
#     return combined_scores

# @app.route('/process', methods=['POST'])
# def process():
#     data = request.get_json()
#     app.logger.debug(f"Received data: {data}")

#     if not data or 'cvs' not in data or 'user_input' not in data:
#         return jsonify({"error": "Invalid data"}), 400

#     user_input = data['user_input']

#     # Read and preprocess the PDF content
#     cvs = data.get('cvs', [])
#     cv_texts = []
#     for cv in cvs:
#         pdf_content = read_pdf_content(cv['file_path'])
#         if pdf_content:
#             processed_content = preprocess(pdf_content)
#             cv_texts.append(processed_content)
#         else:
#             cv_texts.append("")

#     # Ensure the corpus is not empty before initializing models
#     tokenized_corpus = [doc.split() for doc in cv_texts if doc]
#     if not tokenized_corpus:
#         return jsonify({"error": "No valid CV content found"}), 400

#     # Prepare the model input
#     bm25 = BM25Okapi(tokenized_corpus)
#     dictionary = Dictionary(tokenized_corpus)
#     corpus = [dictionary.doc2bow(doc) for doc in tokenized_corpus]
#     lsi_model = LsiModel(corpus, id2word=dictionary, num_topics=300)
#     lsi_corpus = lsi_model[corpus]
#     lsi_matrix = corpus2csc(lsi_corpus).transpose()
#     word2vec_model = Word2Vec(sentences=tokenized_corpus, vector_size=100, window=5, min_count=1, workers=4)

#     # Rank the documents
#     combined_scores = rank_documents(user_input, bm25, lsi_model, lsi_matrix, dictionary, word2vec_model)

#     for i, cv in enumerate(cvs):
#         if i < len(combined_scores):
#             cv['combined_score'] = combined_scores[i]
#         else:
#             cv['combined_score'] = 0
#         app.logger.debug(f"Processed CV: {cv}")

#     sorted_cvs = sorted(cvs, key=lambda x: x['combined_score'], reverse=True)
#     app.logger.debug(f"Sorted CVs: {sorted_cvs}")

#     response = {
#         'batch_id': data.get('batch_id'),
#         'sorted_cvs': sorted_cvs
#     }
#     app.logger.debug(f"Response: {response}")

#     return jsonify(response), 200
#################################################################################

def to_markdown(text):
    text = text.replace('•', '  *')
    return Markdown(textwrap.indent(text, '> ', predicate=lambda _: True))

def generate_summary(api_key, base_prompt):
    genai.configure(api_key=api_key)
    model = genai.GenerativeModel('gemini-1.5-flash')
    response = model.generate_content(base_prompt)
    # Log the raw response from the model for debugging
    # app.logger.debug(f"Raw model response: {response.text}")
    return response.text

def preprocess(text):
    text = re.sub(r'\W', ' ', text)
    text = re.sub(r'\s+', ' ', text)
    text = text.lower()
    text = ' '.join([word for word in text.split() if word not in stop_words])
    return text

def read_pdf_content(file_path):
    # Ensure the file path is correctly combined with the base directory
    full_path = os.path.join(LARAVEL_STORAGE_BASE, file_path.lstrip('/'))
    logging.info(f"fullpath nya ADALAH {full_path}")
    # full_path = os.path.join(LARAVEL_STORAGE_BASE, file_path)
    content = ""
    try:
        with open(full_path, 'rb') as file:
            reader = PdfReader(file)
            for page in reader.pages:
                content += page.extract_text()
    except Exception as e:
        app.logger.error(f"Error reading {file_path}: {e}")
    return content


def query_likelihood_model(query, corpus, smoothing=1):
    query_tokens = preprocess(query)
    doc_scores = []
    total_vocab = set([word for doc in corpus for word in doc])
    total_vocab_size = len(total_vocab)
    for doc in corpus:
        doc_token_count = len(doc)
        score = 1
        for term in query_tokens:
            term_frequency = doc.count(term)
            score *= (term_frequency + smoothing) / (doc_token_count + smoothing * total_vocab_size)
        doc_scores.append(score)
    return doc_scores

def get_bm25_scores(query, bm25):
    query_tokens = preprocess(query).split()  # Tokenize the query
    scores = bm25.get_scores(query_tokens)
    return scores

def get_lsi_scores(query, dictionary, lsi_model, index):
    query_tokens = preprocess(query).split()  # Tokenize the query
    query_bow = dictionary.doc2bow(query_tokens)
    query_lsi = lsi_model[query_bow]
    scores = index[query_lsi]
    return scores


def rank_documents(scores):
    return np.argsort(scores)[::-1]

def mmr(documents, query, lambda_param=0.5):
    tfidf_vectorizer = TfidfVectorizer()
    doc_embeddings = tfidf_vectorizer.fit_transform(documents)
    query_embedding = tfidf_vectorizer.transform([query])

    similarity_to_query = cosine_similarity(query_embedding, doc_embeddings)[0]
    similarity_between_docs = cosine_similarity(doc_embeddings)

    selected_docs = []
    unselected_docs = list(range(len(documents)))

    while unselected_docs:
        if selected_docs:
            mmr_score = lambda_param * similarity_to_query[unselected_docs] - (1 - lambda_param) * np.max(similarity_between_docs[unselected_docs][:, selected_docs], axis=1)
        else:
            mmr_score = lambda_param * similarity_to_query[unselected_docs]

        next_doc = unselected_docs[np.argmax(mmr_score)]
        selected_docs.append(next_doc)
        unselected_docs.remove(next_doc)

    return selected_docs

# @app.route('/process', methods=['POST'])
# def process():
#     data = request.get_json()
#     app.logger.debug(f"Received data: {data}")

#     if not data or 'cvs' not in data or 'user_input' not in data:
#         return jsonify({"error": "Invalid data"}), 400

#     user_input = data['user_input']

#     # Read and preprocess the PDF content
#     cvs = data.get('cvs', [])
#     cv_texts = []
#     for cv in cvs:
#         pdf_content = read_pdf_content(cv['file_path'])
#         if pdf_content:
#             processed_content = preprocess(pdf_content)
#             cv_texts.append(processed_content.split())  # Tokenize the processed content
#         else:
#             cv_texts.append("")

#     # Ensure the corpus is not empty before initializing models
#     tokenized_corpus = [doc for doc in cv_texts if doc]
#     if not tokenized_corpus:
#         return jsonify({"error": "No valid CV content found"}), 400

#     # Prepare the models
#     bm25 = BM25Okapi(tokenized_corpus)
#     dictionary = corpora.Dictionary(tokenized_corpus)
#     corpus_bow = [dictionary.doc2bow(doc) for doc in tokenized_corpus]
#     lsi_model = models.LsiModel(corpus_bow, id2word=dictionary, num_topics=100)
#     corpus_lsi = lsi_model[corpus_bow]

#     index = similarities.MatrixSimilarity(lsi_model[corpus_bow])

#     # Rank the documents using BM25 and LSI
#     bm25_scores = get_bm25_scores(user_input, bm25)
#     lsi_scores = get_lsi_scores(user_input, dictionary, lsi_model, index)

#     bm25_ranked_docs = rank_documents(bm25_scores)
#     lsi_ranked_docs = rank_documents(lsi_scores)

#     # Combine rankings using MMR
#     bm25_top_docs = [(' '.join(cv_texts[i]), i) for i in bm25_ranked_docs[:10] if i < len(cv_texts)]  # Track original indices
#     lsi_top_docs = [(' '.join(cv_texts[i]), i) for i in lsi_ranked_docs[:10] if i < len(cv_texts)]  # Track original indices
#     combined_docs = bm25_top_docs + lsi_top_docs

#     combined_texts = [doc for doc, idx in combined_docs]  # Extract only texts for MMR
#     mmr_ranked_indices = mmr(combined_texts, user_input)
#     final_ranked_docs = [combined_docs[i] for i in mmr_ranked_indices if i < len(combined_docs)]

#     # Assign rank as combined_score
#     sorted_cvs = [{'cv': cvs[idx], 'combined_score': rank} for rank, (doc, idx) in enumerate(final_ranked_docs, 1)]

#     app.logger.debug(f"Sorted CVs: {sorted_cvs}")

#     response = {
#         'batch_id': data.get('batch_id'),
#         'sorted_cvs': sorted_cvs
#     }
#     app.logger.debug(f"Response: {response}")

#     return jsonify(response), 200


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
        if 'file_path' in cv:
            pdf_content = read_pdf_content(cv['file_path'])
            if pdf_content:
                processed_content = preprocess(pdf_content)
                cv_texts.append(processed_content.split())  # Tokenize the processed content
            else:
                cv_texts.append("")
        else:
            cv_texts.append("")

    # Ensure the corpus is not empty before initializing models
    tokenized_corpus = [doc for doc in cv_texts if doc]
    if not tokenized_corpus:
        return jsonify({"error": "No valid CV content found"}), 400

    # Prepare the models
    bm25 = BM25Okapi(tokenized_corpus)
    dictionary = corpora.Dictionary(tokenized_corpus)
    corpus_bow = [dictionary.doc2bow(doc) for doc in tokenized_corpus]
    lsi_model = models.LsiModel(corpus_bow, id2word=dictionary, num_topics=100)
    corpus_lsi = lsi_model[corpus_bow]

    index = similarities.MatrixSimilarity(lsi_model[corpus_bow])

    # Rank the documents using BM25 and LSI
    bm25_scores = get_bm25_scores(user_input, bm25)
    lsi_scores = get_lsi_scores(user_input, dictionary, lsi_model, index)

    bm25_ranked_docs = rank_documents(bm25_scores)
    lsi_ranked_docs = rank_documents(lsi_scores)

    # Combine rankings using MMR
    bm25_top_docs = [(' '.join(cv_texts[i]), i) for i in bm25_ranked_docs[:10] if i < len(cv_texts)]  # Track original indices
    lsi_top_docs = [(' '.join(cv_texts[i]), i) for i in lsi_ranked_docs[:10] if i < len(cv_texts)]  # Track original indices
    combined_docs = bm25_top_docs + lsi_top_docs

    combined_texts = [doc for doc, idx in combined_docs]  # Extract only texts for MMR
    mmr_ranked_indices = mmr(combined_texts, user_input)

    # Use a set to track added indices to avoid duplicates
    added_indices = set()
    final_ranked_docs = []
    for i in mmr_ranked_indices:
        if i < len(combined_docs):
            doc, idx = combined_docs[i]
            if idx not in added_indices:
                final_ranked_docs.append((doc, idx))
                added_indices.add(idx)

    # Assign rank as combined_score
    sorted_cvs = [dict(cvs[idx], combined_score=rank) for rank, (doc, idx) in enumerate(final_ranked_docs, 1)]

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

    if len(cv_texts) < 3:
        return jsonify({"error": "Insufficient CV content"}), 400

    base_prompt = f"""
As an AI-based hiring manager, evaluate the provided CVs based on criteria including relevant experience, career aspirations, education and degrees, skills and competencies, achievements and awards, red flags (such as gaps or inconsistencies), personal information and professionalism, and cultural fit and language proficiency. 
Provide detailed feedback and scores for each criterion, ensuring clarity on how well each candidate aligns with the job role and company culture. Summarize the evaluation in JSON format, detailing strengths, areas for improvement, and comparisons between candidates. 
Ensure all fields in the JSON format are populated appropriately, handling cases of missing data by marking them as 'Unknown'. Format JSON as follows: {json_format}. First CV: {cv_texts[0]}, second CV: {cv_texts[1]}, third CV: {cv_texts[2]}. Display only the JSON results, no other outputs.
"""
    summary = generate_summary(api_key, base_prompt)
    # response = {"summaries": [summary.strip()]} 
    # response = {"summarization": json.loads(summary.strip())} 
    
    return jsonify(summary=summary), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)