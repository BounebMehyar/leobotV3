from flask import Flask, request, jsonify
from transformers import get_scheduler
from torch.optim import AdamW
import numpy as np
from transformers import AutoTokenizer, BertForSequenceClassification, Trainer, TrainingArguments
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
import torch

app = Flask(__name__)

# Load the data
file_path = 'questions_departements.csv'
data = pd.read_csv(file_path)

# Initialize the tokenizer and model from a pretrained BERT model
tokenizer = AutoTokenizer.from_pretrained("asafaya/bert-base-arabic")
model = BertForSequenceClassification.from_pretrained(
    'asafaya/bert-base-arabic',
    num_labels=len(data['departement'].unique()),
    hidden_dropout_prob=0.2,  # Adjust dropout probability
    attention_probs_dropout_prob=0.2
)


# Preprocess data
label_encoder = LabelEncoder()
data['departement_encoded'] = label_encoder.fit_transform(data['departement'])
questions = data['question'].tolist()
labels = data['departement_encoded'].tolist()
train_questions, test_questions, train_labels, test_labels = train_test_split(questions, labels, test_size=0.2)


# Tokenization
train_encodings = tokenizer(train_questions, truncation=True, padding=True, max_length=128)
test_encodings = tokenizer(test_questions, truncation=True, padding=True, max_length=128)

# Convert to torch tensors
class Dataset(torch.utils.data.Dataset):
    def __init__(self, encodings, labels):
        self.encodings = encodings
        self.labels = labels

    def __getitem__(self, idx):
        item = {key: torch.tensor(val[idx]) for key, val in self.encodings.items()}
        item['labels'] = torch.tensor(self.labels[idx])
        return item

    def __len__(self):
        return len(self.labels)

train_dataset = Dataset(train_encodings, train_labels)
test_dataset = Dataset(test_encodings, test_labels)

# Training arguments
training_args = TrainingArguments(
    output_dir='./results',          # output directory
    num_train_epochs=3,              # number of training epochs
    per_device_train_batch_size=8,   # batch size for training
    per_device_eval_batch_size=16,   # batch size for evaluation
    warmup_steps=500,                # number of warmup steps for learning rate scheduler
    weight_decay=0.01,               # strength of weight decay
    logging_dir='./logs',            # directory for storing logs
    logging_steps=10,
)

# Initialize Trainer
trainer = Trainer(
    model=model,
    args=training_args,
    train_dataset=train_dataset,
    eval_dataset=test_dataset
)

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    question = data['question']
    inputs = tokenizer(question, return_tensors="pt", padding=True, truncation=True, max_length=128)
    with torch.no_grad():
        logits = model(**inputs).logits
    predicted_index = logits.argmax().item()
    predicted_department = label_encoder.inverse_transform([predicted_index])[0]
    return jsonify({'department': predicted_department})

@app.route('/train', methods=['POST'])
def train_model():
    trainer.train()
    trainer.save_model('./results')  # Save the trained model
    return jsonify({'status': 'training completed'})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
