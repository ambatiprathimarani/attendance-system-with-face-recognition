## ⚙️ Deployment & Setup Guide (Ubuntu Server)

This section explains how to deploy the **LAN-Based Attendance System (PHP + Python Face Recognition)** using **Apache**, **MySQL**, and **mod_wsgi** on Ubuntu.

---

### 🔹 1. Install Apache, MySQL, and PHP

```bash
sudo apt update
sudo apt install apache2
sudo systemctl enable apache2
sudo systemctl start apache2
sudo apt install mysql-server
sudo mysql_secure_installation
sudo apt install php libapache2-mod-php php-mysql

🔹 2. Copy Project to Web Directory
bash
 
sudo cp -r attendance /var/www/html/
Now the project is located at: 
/var/www/html/attendance

🔹 3. Configure MySQL Database
bash
 
sudo mysql -u root -p
Inside the MySQL prompt:

sql
 
CREATE DATABASE attendance;

CREATE USER 'db_username'@'localhost' IDENTIFIED BY 'db_password';
GRANT ALL PRIVILEGES ON attendance.* TO 'db_username'@'localhost';
FLUSH PRIVILEGES;
EXIT;
(Optional: Export the database)

bash
 
mysqldump -u root -p attendance > attendance.sql
🔹 4. Configure Apache Virtual Host for PHP Attendance System
bash
 
sudo nano /etc/apache2/sites-available/attendance.conf
Add the following configuration:

apache
 
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/attendance

    <Directory /var/www/html/attendance>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
Save & close (Ctrl + O, Enter, Ctrl + X)

Then enable and restart the site:

bash
 
sudo a2ensite attendance.conf
sudo systemctl reload apache2
sudo systemctl restart apache2

🔹 5. Access PHP Web App
From same system:
👉 http://localhost/attendance

From other systems on LAN:
👉 http://<system_ip>/attendance

Default Admin Login:

makefile
 
Email: admin@gmail.com
Password: admin123


🧠 Face Recognition Attendance Setup (Python + Flask)
🔹 6. Install Python and Dependencies
bash
 
sudo apt install python3 python3-pip python3-venv
sudo apt install libapache2-mod-wsgi-py3
sudo apt install cmake g++ make
(If required for OpenCV or face_recognition)

bash
 
sudo apt install build-essential cmake \
libopenblas-dev liblapack-dev \
libx11-dev libgtk-3-dev \
libboost-python-dev python3-dev

🔹 7. Configure Apache for Face Attendance (Flask)
bash
 
sudo nano /etc/apache2/sites-available/face_attendance.conf
Add:

apache
 
<VirtualHost *:8085>
    ServerName localhost

    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/cert.pem
    SSLCertificateKeyFile /etc/apache2/ssl/key.pem

    # WSGI Configuration
    WSGIDaemonProcess face_attendance_app \
        python-home=/var/www/html/attendance/face_attendance/env \
        python-path=/var/www/html/attendance/face_attendance \
        threads=1

    WSGIProcessGroup face_attendance_app
    WSGIScriptAlias / /var/www/html/attendance/face_attendance/app.wsgi

    <Directory /var/www/html/attendance/face_attendance>
        Require all granted
    </Directory>

    Alias /css /var/www/html/attendance/face_attendance/css
    <Directory /var/www/html/attendance/face_attendance/css>
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/face_attendance_error.log
    CustomLog ${APACHE_LOG_DIR}/face_attendance_access.log combined
</VirtualHost>
Enable and restart Apache:

bash
 
sudo a2ensite face_attendance.conf
sudo systemctl reload apache2
sudo systemctl restart apache2
🔹 8. Setup Flask Environment
bash
 
cd /var/www/html/attendance/face_attendance
python3 -m venv env
source env/bin/activate
pip install -r requirements.txt
deactivate
🔹 9. Generate SSL Certificates
bash
 
mkdir /etc/apache2/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
-keyout /etc/apache2/ssl/key.pem -out /etc/apache2/ssl/cert.pem

🔹 10. Access Face Attendance System
Accessible within LAN using: 
https://<system_ip>:8085

✅ System Overview

PHP handles manual login, attendance logs, and reports

Python Flask handles webcam-based face recognition attendance

Both run on the same Apache server within the LAN

Secure access using SSL + IP restriction

