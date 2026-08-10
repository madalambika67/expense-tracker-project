"""
Expense Tracker - Python Backend (Flask REST API)
Handles all data storage and business logic using SQLite.
The PHP frontend talks to this API over HTTP.
"""

from flask import Flask, jsonify, request
import sqlite3
import os
from datetime import datetime

app = Flask(__name__)

try:
    from flask_cors import CORS
    CORS(app)  # allow the PHP frontend (different host/port) to call this API
except ImportError:
    pass  # flask-cors not installed; not required since PHP calls this server-side via cURL

DB_PATH = os.path.join(os.path.dirname(__file__), 'expenses.db')


def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_db()
    conn.execute('''
        CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            amount REAL NOT NULL,
            category TEXT NOT NULL,
            date TEXT NOT NULL,
            created_at TEXT NOT NULL
        )
    ''')
    conn.commit()
    conn.close()


@app.route('/', methods=['GET'])
def health_check():
    return jsonify({'status': 'ok', 'message': 'Expense Tracker API is running'})


@app.route('/expenses', methods=['GET'])
def get_expenses():
    conn = get_db()
    rows = conn.execute('SELECT * FROM expenses ORDER BY date DESC, id DESC').fetchall()
    conn.close()
    return jsonify([dict(row) for row in rows])


@app.route('/expenses/<int:expense_id>', methods=['GET'])
def get_expense(expense_id):
    conn = get_db()
    row = conn.execute('SELECT * FROM expenses WHERE id = ?', (expense_id,)).fetchone()
    conn.close()
    if row is None:
        return jsonify({'error': 'Expense not found'}), 404
    return jsonify(dict(row))


@app.route('/expenses', methods=['POST'])
def add_expense():
    data = request.get_json(force=True, silent=True) or {}
    title = (data.get('title') or '').strip()
    amount = data.get('amount')
    category = (data.get('category') or 'Other').strip()
    date = data.get('date') or datetime.now().strftime('%Y-%m-%d')

    if not title or amount is None:
        return jsonify({'error': 'title and amount are required'}), 400

    try:
        amount = float(amount)
    except (TypeError, ValueError):
        return jsonify({'error': 'amount must be a number'}), 400

    conn = get_db()
    cur = conn.execute(
        'INSERT INTO expenses (title, amount, category, date, created_at) VALUES (?, ?, ?, ?, ?)',
        (title, amount, category, date, datetime.now().isoformat())
    )
    conn.commit()
    new_id = cur.lastrowid
    conn.close()
    return jsonify({'id': new_id, 'message': 'Expense added successfully'}), 201


@app.route('/expenses/<int:expense_id>', methods=['PUT'])
def update_expense(expense_id):
    data = request.get_json(force=True, silent=True) or {}
    conn = get_db()
    existing = conn.execute('SELECT * FROM expenses WHERE id = ?', (expense_id,)).fetchone()
    if existing is None:
        conn.close()
        return jsonify({'error': 'Expense not found'}), 404

    title = data.get('title', existing['title'])
    amount = data.get('amount', existing['amount'])
    category = data.get('category', existing['category'])
    date = data.get('date', existing['date'])

    try:
        amount = float(amount)
    except (TypeError, ValueError):
        conn.close()
        return jsonify({'error': 'amount must be a number'}), 400

    conn.execute(
        'UPDATE expenses SET title = ?, amount = ?, category = ?, date = ? WHERE id = ?',
        (title, amount, category, date, expense_id)
    )
    conn.commit()
    conn.close()
    return jsonify({'message': 'Expense updated successfully'})


@app.route('/expenses/<int:expense_id>', methods=['DELETE'])
def delete_expense(expense_id):
    conn = get_db()
    conn.execute('DELETE FROM expenses WHERE id = ?', (expense_id,))
    conn.commit()
    conn.close()
    return jsonify({'message': 'Expense deleted successfully'})


@app.route('/summary', methods=['GET'])
def summary():
    conn = get_db()
    rows = conn.execute(
        'SELECT category, SUM(amount) as total FROM expenses GROUP BY category ORDER BY total DESC'
    ).fetchall()
    total_row = conn.execute('SELECT SUM(amount) as total FROM expenses').fetchone()
    conn.close()
    return jsonify({
        'by_category': [dict(row) for row in rows],
        'total': total_row['total'] or 0
    })


if __name__ == '__main__':
    init_db()
    print('Expense Tracker API running at http://localhost:5000')
    app.run(host='0.0.0.0', port=5000, debug=True)
