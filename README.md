# JobCompass

JobCompass is a web-based career guidance and job resource platform that helps learners and job seekers discover relevant job opportunities and learning materials based on their career interests.  
It is designed with modular PHP and MySQL architecture so that AI-based recommendations and automation can be integrated in the future without major structural changes.

---

## Tech Stack

**Frontend:**  
- HTML5  
- CSS3  

**Backend:**  
- PHP 8 (Procedural PHP, modularized for easy upgrade to MVC or AI APIs)  
- MySQL (via XAMPP phpMyAdmin)  

**Server:**  
- Apache (XAMPP)  

**Tools Used:**  
- Visual Studio Code  
- GitHub for version control  
- phpMyAdmin for database management  

---

## Setup Instructions

### Step 1: Project Location
Copy the project to your XAMPP directory:
C:\xampp\htdocs\jobcompass


### Step 2: Start XAMPP
1. Launch **XAMPP Control Panel**  
2. Start **Apache** and **MySQL**

### Step 3: Create Database
1. Visit [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
2. Create a new database named:
jobcompass



### Step 4: Import Sample Data
1. Select the `jobcompass` database  
2. Go to **Import → Choose File → seed.sql → Go**  

This file sets up tables:
- `users`
- `jobs`
- `resources`

### Step 5: Verify Database Config
In **`db.php`**, check:
php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jobcompass";

$conn = new mysqli($host, $user, $pass, $dbname);
Step 6: Run in Browser
Visit:


http://localhost/jobcompass/
You can now register, log in, and explore the dashboard, jobs, and resources pages.

Folder Structure

jobcompass/
├─ pages/
│  ├─ register.php        # User registration
│  ├─ login.php           # User login
│  ├─ dashboard.php       # Dashboard after login
│  ├─ profile.php         # User profile
│  ├─ jobs.php            # Job listings and filters
│  ├─ resources.php       # Learning resources
├─ server.php             # Handles authentication logic and form actions
├─ db.php                 # Database connection
├─ index.php              # Landing page
├─ styles.css             # Global styles
├─ seed.sql               # Tables and test data

Code Organization
server.php – Handles register/login logic and can later be extended to connect with AI or external APIs.
db.php – Centralized database connection for easier migration to cloud or ORM.
pages/ – Contains all feature pages in a modular format.
seed.sql – Includes initial data for quick testing.

This modular structure allows backend enhancements or AI integrations without breaking the current system.

Seed Data Usage
seed.sql includes:

One demo user
Example job listings across skill categories
Sample learning resources

You can log in using:

Email: demo@jobcompass.com
Password: demo123

This allows you to explore all existing modules and test navigation.

Environment Configuration
Setting	Value
PHP Version	≥ 8.0
Database	MySQL (via XAMPP)
Localhost URL	http://localhost/jobcompass/
Database Name	jobcompass
Charset	utf8mb4

If your MySQL setup has a password, update it in db.php.

Future Enhancements (Phase 2)
The system has been structured for easy integration of intelligent and user-friendly features in the next phase.

How the Current Design Supports Expansion
Tables jobs and resources already store skill data in structured fields, which can easily feed future logic.

The PHP structure is modular, allowing new features (Career Chat, AI APIs, or Resume Analyzer) to be added as independent modules.

The database schema is clean and extendable for analytics or ML integration.

Frontend layout uses basic HTML/CSS for easy enhancement into responsive or dynamic components later.

Developed By
Team CivivSentinel – Hackathon Project 2025
Built using PHP, MySQL, and XAMPP.
Designed for smooth future integration with AI-driven features and multilingual support.
