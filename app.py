import os
from flask import Flask, request, jsonify, render_template
from flask_cors import CORS
from groq import Groq

app = Flask(__name__)

# Appliquer CORS à toutes les routes
CORS(app)

# Initialize Groq client
GROQ_API_KEY = os.getenv("GROQ_API_KEY", "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")
client = Groq(api_key=GROQ_API_KEY)



@app.route("/")
def index():
    return render_template("index.html")  # Assurez-vous que le fichier existe dans le dossier `templates`

@app.route("/chat", methods=["POST"])
def chat():
    user_message = request.json.get("message", "")

    try:
        response = client.chat.completions.create(
            messages=[{"role": "user", "content": user_message}],
            model="llama-3.3-70b-versatile",
        )
        bot_reply = response.choices[0].message.content
    except Exception as e:
        bot_reply = f"Error: {e}"

    return jsonify({"reply": bot_reply})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)