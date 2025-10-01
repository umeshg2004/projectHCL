USER REGISTRATION AND LOGIN SYSTEM

REQUIREMENTS:
1. PHP 7.4 or higher
2. MySQL 5.7 or higher
3. MongoDB 4.0 or higher
4. Redis 5.0 or higher
5. Web server (Apache/Nginx)

SETUP INSTRUCTIONS:

1. DATABASE SETUP:
   - Import database_setup.sql into MySQL
   - Ensure MongoDB is running on localhost:27017
   - Ensure Redis is running on localhost:6379

2. PHP EXTENSIONS REQUIRED:
   - pdo_mysql
   - mongodb
   - redis

3. CONFIGURATION:
   - Update database credentials in php/config.php if needed
   - Ensure web server has proper permissions

4. DEPLOYMENT:
   - Place all files in web server document root
   - Access index.html to start the application

FLOW:
1. User registers on register.html
2. User logs in on login.html
3. Successful login redirects to profile.html
4. User can update profile information

FEATURES:
- Separate HTML, CSS, JS, and PHP files
- jQuery AJAX for all backend communication
- Bootstrap responsive design
- MySQL with prepared statements
- MongoDB for profile storage
- Redis for session management
- localStorage for client-side session handling