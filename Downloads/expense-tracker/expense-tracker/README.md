# Expense Tracker (PHP frontend + Python backend)

A simple full-stack expense tracker:

- **Backend**: Python (Flask) REST API + SQLite database — handles all data storage and logic.
- **Frontend**: PHP — renders HTML pages and talks to the backend over HTTP using cURL.

## Project structure

```
expense-tracker/
├── backend/
│   ├── app.py            # Flask API (routes for CRUD + summary)
│   ├── requirements.txt  # Python dependencies
│   └── expenses.db       # SQLite DB (auto-created on first run)
└── frontend/
    ├── config.php        # API base URL + cURL helper
    ├── index.php         # Dashboard: list expenses + totals
    ├── add.php           # Add expense form
    ├── edit.php          # Edit expense form
    ├── delete.php         # Delete handler
    └── style.css          # Styling
```

## 1. Run the Python backend

Requires Python 3.8+.

```bash
cd backend
python -m venv venv
source venv/bin/activate      # Windows: venv\Scripts\activate
pip install -r requirements.txt
python app.py
```

The API starts at **http://localhost:5000**. It auto-creates `expenses.db` on first run.

### API endpoints

| Method | Endpoint            | Description                |
|--------|----------------------|-----------------------------|
| GET    | /expenses             | List all expenses          |
| GET    | /expenses/<id>        | Get one expense            |
| POST   | /expenses             | Create expense (JSON body) |
| PUT    | /expenses/<id>        | Update expense             |
| DELETE | /expenses/<id>        | Delete expense              |
| GET    | /summary               | Total + totals by category |

Example POST body:
```json
{ "title": "Groceries", "amount": 42.50, "category": "Food", "date": "2026-07-27" }
```

## 2. Run the PHP frontend

Requires PHP 7.4+ with the `curl` extension enabled (enabled by default in most installs).

```bash
cd frontend
php -S localhost:8000
```

Open **http://localhost:8000** in your browser.

> Make sure the Python backend (step 1) is running first — the PHP pages call it via cURL. If you deploy the backend elsewhere, update `API_BASE_URL` in `frontend/config.php`.

## Notes

- Data is stored in SQLite (`backend/expenses.db`) — no external database setup needed.
- CORS is enabled on the Flask API so the frontend can call it directly if you ever fetch it from JavaScript too.
- This is intentionally minimal/beginner-friendly — no authentication, meant as a learning/starter project.
