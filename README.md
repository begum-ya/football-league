# 🏆 Football League Simulation

This project is a **football league simulation** with a **Laravel API backend** and a **React frontend**. It allows users to:
- Generate fixtures
- Play matches week by week or all at once
- Track standings and predictions dynamically

## 📂 Project Structure

```
/football-league
│── api/        # Laravel API (Backend)
│── web/        # React App (Frontend)
```

---

# ⚙️ **API (Backend)**
The API is built using **Laravel** and provides endpoints for managing teams, fixtures, simulations, and standings.

## 🚀 **Setup**
### 1️⃣ Install Dependencies
```sh
cd api
composer install
```

### 2️⃣ Set Up Environment Variables
Copy `.env.example` to `.env` and configure the database:
```sh
cp .env.example .env
php artisan key:generate
```
Edit `.env` to match your database settings:
```env
DB_DATABASE=football_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3️⃣ Run Database Migrations & Seed Data
```sh
php artisan migrate --seed
```

### 4️⃣ Start the Laravel Development Server
```sh
php artisan serve
```

## 🛠 **Available API Endpoints**
| Method | Endpoint | Description |
|--------|---------|-------------|
| **GET** | `/api/teams` | Get all teams |
| **POST** | `/api/fixtures/generate` | Generate league fixtures |
| **GET** | `/api/fixtures` | Get all fixtures |
| **POST** | `/api/simulation/play-week/{week_id}` | Play a specific week |
| **POST** | `/api/simulation/play-all-weeks` | Play all weeks |
| **GET** | `/api/standings` | Get current league standings |
| **GET** | `/api/fixtures/current-week` | Get the current match week |
| **GET** | `/api/standings/predictions` | Get championship predictions |
| **POST** | `/api/simulation/reset` | Reset the simulation |

---

# 🎨 **Web (Frontend)**
The **React frontend** allows users to interact with the simulation visually.

## 🚀 **Setup**
### 1️⃣ Install Dependencies
```sh
cd web
npm install
```

### 2️⃣ Configure Environment Variables
Create a `.env` file in `web/` and set the API URL:
```env
VITE_API_URL=http://127.0.0.1:8000/api
```

### 3️⃣ Start the Frontend Server
```sh
npm run dev
```

---

## 🖥️ **Features**
✅ **Home Page:** Displays options to start the simulation  
✅ **Teams & Fixtures:** List of teams and generated fixtures  
✅ **Play Week by Week:** Play each week manually or all at once  
✅ **Standings & Predictions:** View live standings and championship predictions  
✅ **Week-by-Week Results:** Displays match results after all weeks are played  

---

## 📌 **Frontend Components**
| Component | Description |
|-----------|-------------|
| `Teams.tsx` | Fetches and displays the list of teams |
| `FixturesList.tsx` | Lists fixtures and their results |
| `Simulation.tsx` | Main simulation logic (play weeks, standings, results) |

---

# 🔥 **How It Works**
### 1️⃣ **Generate Fixtures**
- Click the **"Generate Fixtures"** button
- The API creates matches for the league

### 2️⃣ **Play Matches**
- Click **"Play Next Week"** to simulate week-by-week
- Click **"Play All Weeks"** to run the entire season
- Matches are played and stored in the database

### 3️⃣ **Track Standings & Predictions**
- The **standings** update dynamically after each match
- The **championship predictions** show the chance of each team winning

### 4️⃣ **View Results**
- After all weeks are played, a **table of week-by-week results** is displayed
- Users can analyze how the season progressed

### 5️⃣ **Reset Data**
- Click **"Reset Simulation"** to clear all data and start fresh

---

## 🧪 Running Tests (PHP/Laravel)
This project includes **unit tests and feature tests** to ensure the correctness of the API.

### 1️⃣ Set Up Test Database
Modify your `.env.testing` file:
```env
DB_DATABASE=football_db_testing
DB_USERNAME=root
DB_PASSWORD=

```sh
php artisan migrate --env=testing
php artisan test
```
