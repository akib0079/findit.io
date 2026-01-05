# 🔍 FindIt - Lost & Found & Food Waste Management Platform

**FindIt** is a dual-purpose community web application designed to solve two major campus and local community problems: connecting people with their lost belongings and reducing food waste from local restaurants.

## 🚀 About The Project

FindIt acts as a central hub where:
1.  **Community Members** can report lost items or post found items to help reunite owners with their property.
2.  **Restaurants** can post surplus food at discounted prices to prevent waste and offer affordable meals to students/locals.

The platform features secure role-based authentication (Users & Restaurants) and a seamless claim verification system.

## ✨ Key Features

### 📦 Lost & Found System
* **Report Items:** Users can post details about Lost or Found items (Electronics, IDs, Keys, etc.) including images and locations.
* **Smart Search:** Filter items by category, type (Lost/Found), and date.
* **Claiming System:** Users can submit claims for found items with proof of ownership.
* **Verification:** Item reporters/admins can review claims and approve or reject them.
* **Status Tracking:** Items are tracked as *Active*, *Pending*, or *Resolved*.

### 🍽️ Food Waste Management
* **Restaurant Portal:** Dedicated dashboard for restaurant owners to manage offers.
* **Post Offers:** Restaurants can list surplus food with original vs. discounted prices, pickup times, and expiration dates.
* **Public Offer Browsing:** Users can browse active food deals without logging in.
* **Smart Filters:** Filter food by Name, Price Range, and Availability.

### 🔐 User & Security
* **Role-Based Access:** Distinct features for Standard Users vs. Restaurant Accounts.
* **Secure Authentication:** User registration and login using PHP Sessions.
* **Security:** Passwords are hashed using the industry-standard **Bcrypt** algorithm.
* **Responsive Design:** Fully functional on mobile and desktop devices.

## 🛠️ Technology Stack

* **Frontend:** HTML5, CSS3, JavaScript (jQuery)
* **Backend:** Core PHP (Object-Oriented & Procedural)
* **Database:** MySQL / MariaDB
* **Server:** Apache (via XAMPP/WAMP)

## 📂 Database Structure

The project uses the following key tables:
* `tbl_users`: Stores user and restaurant login credentials.
* `tbl_items`: Stores lost/found item reports.
* `tbl_claims`: Manages claims made on items.
* `tbl_food`: Stores food offers posted by restaurants.
* `tbl_categories`: Manages item categories (Electronics, Documents, etc.).

## ⚙️ Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/yourusername/findit.git](https://github.com/yourusername/findit.git)
    ```

2.  **Move to Server Directory**
    * Move the project folder to your local server directory (e.g., `htdocs` in XAMPP or `www` in WAMP).

3.  **Database Setup**
    * Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
    * Create a new database named `lostandfound_db`.
    * Import the `database.sql` file provided in this repository.

4.  **Configure Connection**
    * Open `db_connect.php`.
    * Ensure the credentials match your local setup:
    ```php
    $servername = "localhost";
    $username = "root";
    $password = ""; // Default XAMPP password is empty
    $dbname = "lostandfound_db";
    ```

5.  **Run the App**
    * Open your browser and navigate to: `http://localhost/findit/index.php`

## 📸 Screenshots

*(Add your screenshots here. You can upload images to your repo and link them)*

| Home Page | Food Offers | User Profile |
|:---:|:---:|:---:|
| ![Home](path/to/image.png) | ![Food](path/to/image.png) | ![Profile](path/to/image.png) |

## 🤝 Contributing

Contributions are welcome! Please fork the repository and create a pull request for any feature updates or bug fixes.

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---
*Built with ❤️ by [Your Name]*
