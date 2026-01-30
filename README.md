Implementation Guide for Recruitment Portal System
A. System Requirements
Hardware Requirements
•	Operating System: Windows / macOS / Linux
•	RAM: Minimum 4 GB (8 GB recommended)
•	Storage: Minimum 1 GB free disk space
•	Processor: Intel i3 or equivalent and above
•	Graphics: Not required
________________________________________
B. Software Requirements
•	Web Server: XAMPP / WAMP / LAMP
•	Programming Languages:
o	PHP (Version 7.4 or above)
o	HTML5, CSS3, JavaScript
•	Database: MySQL / MariaDB
•	Browser: Google Chrome / Mozilla Firefox
•	IDE/Editors: VS Code / Sublime Text / Notepad++
________________________________________
C. Project Directory Structure
The complete project must be placed inside the htdocs directory as shown below:
Xampp
└──htdocs
 └── ONGC
    		 ├── Front_End
     		 ├── Back_End
		 ├── uploads

All source code files, assets, and uploads are contained inside the ONGC folder.
________________________________________
D. Setup and Installation
1. Installation of Required Software
•	Install XAMPP on the system.
•	Open XAMPP Control Panel.
•	Start the Apache and MySQL services.
________________________________________
2. Importing Project Files
•	Copy the complete project folder.
•	Paste it inside the htdocs directory of XAMPP.
•	The final path should be:
C:\xampp\htdocs\ONGC
________________________________________
 3.Database Setup
•	Open a web browser and go to phpMyAdmin using the following URL:
http://localhost/phpmyadmin
•	Create a new database (you may choose any name).
•	Open the SQL folder provided with the project.
Select the SQL file shared with the project.
In phpMyAdmin:
o	Select the created database
o	Click on the Import tab
o	Upload and import the SQL file
•	This process automatically creates all required tables and inserts the necessary data.
•	Note:
Simply copying the SQL file into any folder will not create the database.
The SQL file must be imported using phpMyAdmin
________________________________________
•	Database Configuration
•	Open the db.php file present inside the ONGC ->BackEnd  folder.
•	Update the database credentials according to your local setup
•	• Replace your_database_name with the database name you created in phpMyAdmin.
•	• Ensure the database name in db.php exactly matches the database name created in phpMyAdmin.
If the database name or credentials do not match, the project will not function correctly.
________________________________________
5. Running the Project
Open a web browser.
Enter the following URL:
http://localhost/ONGC/home_Page.php
The Recruitment Portal homepage will be displayed.

