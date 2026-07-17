# FRS — Facility Request System
## RAMSAM Consultancy & General Service Corp.

A PHP-based web application for managing facility requests, job orders, inventory, and user accounts.

---

## 🚀 Features

- **User Authentication** — Login with role-based access (Admin, Staff, User)
- **Dashboard** — Overview of system activity
- **Job Orders** — Create and track job orders
- **Inventory Management** — Manage inventory items with image support
- **Request Management** — User request submission and tracking
- **Reports** — View and export reports
- **Account Management** — Admin user management
- **QR Code Generation** — Built-in QR code support

---

## 🛠️ Tech Stack

- **Backend:** PHP (vanilla)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Dependencies:** AdminLTE / Bootstrap (via CDN)

---

## ⚙️ Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/FRS.git
cd FRS
```

### 2. Configure the database
```bash
cp include/db_connect.example.php include/db_connect.php
```

Edit `include/db_connect.php` with your database credentials:
```php
$servername = "localhost";
$dbuser     = "your_username";
$dbpass     = "your_password";
$dbname     = "userdb";
```

### 3. Import the database
```bash
mysql -u root -p userdb < userdb.sql
```

### 4. Serve with PHP
```bash
php -S localhost:8000
```
Then open [http://localhost:8000](http://localhost:8000)

---

## 🔐 Environment Variables

See `.env.example` for all required variables.

| Variable | Required | Description |
|----------|----------|-------------|
| `DB_HOST` | ✅ | Database host (e.g., `localhost`) |
| `DB_USER` | ✅ | Database username |
| `DB_PASS` | ✅ | Database password |
| `DB_NAME` | ✅ | Database name (default: `userdb`) |

---

## 📁 Project Structure

```
FRS/
├── index.php              # Login page (entry point)
├── dashboard.php          # Admin dashboard
├── inventory.php          # Inventory management
├── job_orders.php         # Job orders
├── request.php            # User requests
├── include/
│   ├── db_connect.php     # DB connection (not committed)
│   ├── db_connect.example.php  # DB connection template
│   ├── sidebar.php        # Navigation sidebar
│   └── ...
├── userdb.sql             # Database schema
└── .env.example           # Environment variable template
```

---

## 📄 License

Private — RAMSAM Consultancy & General Service Corp.
