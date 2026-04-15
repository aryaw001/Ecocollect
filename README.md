♻️ E-Waste Management System

The E-Waste Management System is a web-based application designed to streamline the collection, management, and disposal of electronic waste. The platform enables users to register e-waste items, schedule pickups, and contribute to environmentally responsible recycling practices.

This project aims to promote sustainable waste management by providing an organized and efficient digital solution.

🚀 Features
📋 User-friendly interface for e-waste submission
🗂️ Categorization of electronic waste items
📅 Pickup scheduling system
📊 Data storage and management using a database
🔐 Secure and structured backend system
🛠️ Tech Stack
Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Server: Apache (via XAMPP)
⚙️ System Requirements

To run this project properly, the following setup is mandatory:

✅ XAMPP (Apache + MySQL must be running)
✅ PHP support (comes with XAMPP)
✅ MySQL Database
✅ Ngrok (for exposing the local server)

⚠️ Important Note:
This system will only function correctly if:

Apache and MySQL services are running through XAMPP
The project is hosted on the local server (htdocs)
Ngrok is used to expose the localhost server

Without these, the application (especially external access via Ngrok) will not work.

🧩 Installation & Setup

Step 1: Clone the Repository

git clone https://github.com/aryaw001/Ecocollect

Step 2: Move to XAMPP Directory

Copy the project folder to:
C:\xampp\htdocs\

Step 3: Start Services

Open XAMPP Control Panel
Start:
Apache
MySQL

Step 4: Database Setup

Open phpMyAdmin
Create a new database
Import the provided .sql file

Step 5: Run the Project

Open browser and go to:
http://localhost/your-project-folder

🌐 Using Ngrok (Important)

To make your local server accessible online:

Install Ngrok
Run the following command:
ngrok http 80
Use the generated public URL

⚠️ Ensure Apache is running before starting Ngrok.

🎯 Objective

The primary goal of this project is to:

Reduce improper disposal of electronic waste
Encourage responsible recycling habits
Provide a centralized system for e-waste handling
🤝 Contribution

Contributions are welcome!
Feel free to fork this repository and submit pull requests.

📄 License

This project is for educational purposes and can be modified or used accordingly.
