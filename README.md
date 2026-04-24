# 👟 ShoeStore - Full-Stack E-commerce Platform

An efficient, structured e-commerce solution for footwear, featuring a robust backend and a dynamic user interface. This project implements a complete shopping workflow, from product discovery to secure order management.

## 🚀 Project Overview
This platform is designed to handle two primary user roles:
* **Customers:** Browse the catalog, manage their cart, and place orders.
* **Administrators:** Full control over shoe inventory and order status tracking.

## 🛠️ Technical Stack
* **Frontend:** HTML, CSS, JavaScript
* **Backend:**  PHP
* **Database:** MySQL (Database Name: `Shoestore`)
* **Tool used:** Draw.io (to draw figures) , XAMPP (a local server environment to test websites using Apache, MySQL, and PHP)
* **IDE:** Visual Studio (for developing, debugging, and compiling professional-grade software and applications.)

## 📋 Key Features

### User Experience
* **Authentication:** Secure login and registration modules.
* **Shopping Cart:** Real-time updates to "Save" or "Update" items.
* **Order Flow:** Seamless transition from cart selection to order confirmation.

### Administrative Tools
* **Inventory Management:** Add, update, and delete shoe listings (CRUD).
* **Order Tracking:** Ability to update order status for customer transparency.
* **User Requests:** Confirm and manage user account requests.

## 📊 System Architecture
The system follows a Level 2 Data Flow Diagram (DFD) logic:
1.  **Process 1.0 (Login):** Validates credentials against the `Admin` and `User` data stores.
2.  **Process 3.0 (Manage Shoes):** Handles the product lifecycle within the `Products` database.
3.  **Process 4.0 (Buy Shoes):** Manages the logic between the `Cart`, `Products`, and `Orders` databases.

## 📂 Installation
1.  **Clone the Repo:**
    ```bash
    git clone [https://github.com/Sagar-wauu/ShoeStore.git](https://github.com/Sagar-wauu/ShoeStore.git)
    ```
2.  **Database Setup:**
    * Create a MySQL database named `Shoestore`.
    * Import the SQL schema provided in the `shoestore.sql` file.
3.  **Build & Run:**
    * Open the project in your Visual Studio.
    * Open the folder that you download.
    * Open XAMPP and 
    Activate: Provides the Apache web server and MySQL database services.
    Host: Projects are served locally via the htdocs folder.
    Interpret: Executes server-side logic (PHP/SQL) on the fly to deliver dynamic content to localhost.
    *Then you can sucessfully run this project
**Developed by 
Sagar Bhattarai and Dikshyant Khatri Chhetri
BCA 6th sem Student(2022 Batch)
Completed Date:2083-01-11, Friday
[Sagar-wauu](https://github.com/Sagar-wauu)**
