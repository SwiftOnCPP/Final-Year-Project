# Restaurant Management System

## Project Description
The **Restaurant Management System** is a PHP-based web application designed to streamline restaurant operations. It provides functionalities for menu management, user role-based access control, and session handling for secure access. The system is developed with simplicity and usability in mind, catering to both administrative staff and chefs.

## Features
- **Role-Based Access Control**: Separate views and functionalities for admins and chefs.
- **Menu Management**:
  - Add, update, and delete menu items.
  - Categorize menu items for better organization (e.g., Popular Menu, Drinks, Side Dishes).
- **Secure Authentication**:
  - Session handling.
  - Login/logout functionality.
- **Responsive Design**: Optimized for various screen sizes.
- **File Upload**: Upload images for menu items.

## Technology Stack
- **Backend**: PHP
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **Additional Libraries**:
  - Font Awesome for icons.

## Installation Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/SwiftOnCPP/Final-Year-Project.git
   ```
2. Navigate to the project directory:
   ```bash
   cd Final-Year-Project
   ```
3. Set up the database:
   - Import the provided SQL file (`database.sql`) into your MySQL server.
   - Update the `connection.php` file with your database credentials.
4. Start a local server (e.g., XAMPP or WAMP) and place the project folder in the server's root directory (`htdocs` for XAMPP).
5. Access the application in your browser:
   ```
   http://localhost/Final-Year-Project
   ```

## Usage
- **Admin Features**:
  - Log in to manage menu items.
  - Add new menu items with details such as name, description, price, category, and image.
  - View, edit, or delete existing menu items.
- **Chef Features**:
  - View the menu items categorized for ease of access.

## File Structure
- `/` : Main application files
- `/uploads` : Directory for uploaded menu item images
- `/css` : Stylesheets
- `/js` : JavaScript files
- `connection.php` : Database connection configuration

## Screenshots
*Coming soon!*

## Contribution
Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch for your feature or bug fix:
   ```bash
   git checkout -b feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add new feature"
   ```
4. Push to your branch:
   ```bash
   git push origin feature-name
   ```
5. Open a pull request.

## License
This project is open source and available under the [MIT License](LICENSE).

## Contact
For any queries or feedback, please contact the repository owner through GitHub.
