import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.metrics import accuracy_score

# Load the dataset
data = pd.read_csv("basedonnees.csv")

# Preprocess the data
# (You might need more sophisticated preprocessing steps depending on your data)
data['question'] = data['question'].apply(lambda x: x.lower())  # Convert to lowercase
data['question'] = data['question'].str.replace('[^\w\s]', '')  # Remove punctuation

# Feature Engineering
vectorizer = TfidfVectorizer()
X = vectorizer.fit_transform(data['question'])

# Split the data
X_train, X_test, y_train, y_test = train_test_split(X, data['department'], test_size=0.2, random_state=42)

# Train the model
model = MultinomialNB()
model.fit(X_train, y_train)

# Evaluate the model
y_pred = model.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print("Accuracy:", accuracy)

# Example prediction
new_question = "How do I apply for a vacation leave?"
new_question = new_question.lower().replace('[^\w\s]', '')  # Preprocess the new question
new_question_vectorized = vectorizer.transform([new_question])
predicted_department = model.predict(new_question_vectorized)
print("Predicted Department:", predicted_department)


