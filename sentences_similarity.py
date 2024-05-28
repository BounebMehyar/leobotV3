from flask import Flask, request, jsonify
from transformers import AutoTokenizer, AutoModel
import torch
import pandas as pd
from sklearn.model_selection import train_test_split

app = Flask(__name__)

# Load the data
file_path = 'questions_departements.csv'
corpus = pd.read_csv(file_path)

# Split the dataset into training and testing sets
train_data, test_data = train_test_split(corpus, test_size=0.2, random_state=42)

# Initialize the tokenizer and model from a pretrained BERT model
tokenizer = AutoTokenizer.from_pretrained("asafaya/bert-large-arabic")
model = AutoModel.from_pretrained("asafaya/bert-large-arabic")

# Define the function to get BERT embeddings
def get_bert_embeddings(sentences):
    """Function to get BERT embeddings for a list of sentences."""
    encoded_input = tokenizer(sentences, padding=True, truncation=True, max_length=512, return_tensors='pt')
    with torch.no_grad():
        model_output = model(**encoded_input)
    embeddings = model_output.last_hidden_state
    attention_masks = encoded_input['attention_mask']
    mask_expanded = attention_masks.unsqueeze(-1).expand(embeddings.size()).float()
    sum_embeddings = torch.sum(embeddings * mask_expanded, 1)
    sum_mask = torch.clamp(mask_expanded.sum(1), min=1e-9)
    return sum_embeddings / sum_mask

def cosine_similarity(embedding1, embedding2):
    """Calculate the cosine similarity between two embeddings."""
    return torch.nn.functional.cosine_similarity(embedding1, embedding2, dim=1)

# Preprocess the training corpus
def preprocess_corpus(data):
    corpora = {}
    departments = data['departement'].unique()
    for dept in departments:
        corpora[dept] = data.question[data['departement'] == dept].tolist()
    return corpora

train_corpora = preprocess_corpus(train_data)
test_corpora = preprocess_corpus(test_data)
departments = list(train_corpora.keys())

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    question = data['question']
    target_embedding = get_bert_embeddings([question])[0]

    # Calculate the average similarity for each corpus
    corpus_similarities = []
    for dept in departments:
        embeddings = get_bert_embeddings(train_corpora[dept])
        similarities = cosine_similarity(target_embedding.unsqueeze(0), embeddings)
        average_similarity = similarities.mean().item()
        corpus_similarities.append(average_similarity)

    # Identify the most similar corpus
    most_similar_corpus_index = corpus_similarities.index(max(corpus_similarities))
    predicted_department = departments[most_similar_corpus_index]

    return jsonify({'department': predicted_department, 'similarity': max(corpus_similarities)})

@app.route('/evaluate', methods=['POST'])
def evaluate():
    correct_predictions = 0
    total_questions = 0

    for dept, questions in test_corpora.items():
        for question in questions:
            target_embedding = get_bert_embeddings([question])[0]
            corpus_similarities = []
            for train_dept in departments:
                embeddings = get_bert_embeddings(train_corpora[train_dept])
                similarities = cosine_similarity(target_embedding.unsqueeze(0), embeddings)
                average_similarity = similarities.mean().item()
                corpus_similarities.append(average_similarity)
            predicted_department = departments[corpus_similarities.index(max(corpus_similarities))]
            if predicted_department == dept:
                correct_predictions += 1
            total_questions += 1
    
    accuracy = correct_predictions / total_questions if total_questions > 0 else 0

    return jsonify({'accuracy': accuracy, 'correct_predictions': correct_predictions, 'total_questions': total_questions})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
