## ShopFlow - Retail Management System

ShopFlow is a simple web-based Retail Management System developed using PHP, HTML, CSS, JavaScript, and MySQL.

The system helps shop owners keep records of items, purchases, and sales.

---

### Features

- User signup
- User login
- Record items
- Record purchases
- Record sales
- View item list
- View purchase list
- View sales list
- User profile
- Password reset

---

### Technologies Used

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- Git & GitHub

#### Design Reference
https://www.figma.com/design/CgchNWRhxQBGLYQokhlwIV/Shopflow?node-id=0-1&t=1Yw67sJav26gozfg-1

---

### Project Structure

```text
shopflow/
│
├── login/
├── signup/
├── dashboard/
├── item-form/
├── purchase-form/
├── sale-form/
├── item-list/
├── purchase-list/
├── sale-list/
├── profile/
│
├── index.php
└── README.md
```

## Project Setup Guide

This guide explains how to set up the ShopFlow project on your computer and how team members should work on the project using Git and GitHub.

---

#### Cloning the Project
Open Terminal or Git Bash and run:

```bash
git clone https://github.com/Group8HQ/shopflow.git
```

Move into the project folder:

```bash
cd shopflow
```

---

#### Opening the Project

Open the project in VS Code:

```bash
code .
```

Or open the folder manually from your code editor.

---

## Switching to Your Branch

Before writing any code, switch to your branch.
**I repeat, first switch to your branch before writing any code**

Example:

```bash
git checkout -b login
```

Other examples:

```bash
git checkout -b item-form
git checkout -b purchase-form 
git checkout -b sale-list
```

Every team member must work on their own branch.
**Do not work** directly on the `main` branch.

---

## Working Inside Your Folder

Each person should only work inside their assigned folder.

Example folders:

```text
sale-form/
item-form/
profile/
dashboard/
```

Do not edit files inside another person's folder unless told to do so.

---

## Managing Your Assets

All your CSS, JavaScript, and image files should stay inside your folder.

Example:

```text
sale-form/
├── sale-form.php
├── sale-form.css
├── sale-form.js
└── images/
```

Do not create shared CSS or JavaScript files outside your folder.

---

## Setting Up the Database

Open phpMyAdmin and create a database called:

```text
shopflow 
```
```
encoding: utf8_general_ci
```

Then import the shopflow.sql database in this repo

```
./shopflow.sql
```

Because everyone is using localhost, each person will manage their own database connection.

Example database connection:

```php
<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "shopflow";

$conn = mysqli_connect($host, $user, $pass, $dbname);

?>
```

Change the password if your MySQL password is different.

---

## Saving Your Work

Before pushing your work, check your changes:

```bash
git status
```

Add your files:

```bash
git add .
```

Commit your work:

```bash
git commit -m "Added sales form"
```

Use clear commit messages explaining what you changed.

---

## Pushing Your Work

Push your branch to GitHub:

```bash
git push origin branch_name
```

Replace `branch_name` with the branch name you're working on.

Example:

```bash
git push origin item-list
```

---

## Pull Requests

After finishing your work:

1. Open the repository on GitHub
2. Click **Pull Requets and then New Pull request**
3. Create a pull request from your branch to the main branch
4. **Base: main** <- **Compare: Your branch**
3. Submit your Pull Request

---

## Important Rules

- Do not push directly to `main`
- Only work inside your assigned folder
- Keep your assets inside your folder
- Commit your work regularly
- Test your code before pushing
- Use clear commit messages
- Do not delete another person's files

---


## Team Allocation

The following are the assigned folders, branches, and screens for each team member.

Design Reference: https://www.figma.com/design/CgchNWRhxQBGLYQokhlwIV/Shopflow?node-id=0-1&t=1Yw67sJav26gozfg-1

| Team Member | Folder | Branch Name |
|---|---|---|
| Wandeya Fahad | `login/` | `login` |
| Muganwa Anthony | `signup/` | `signup` |
| Sekamatte Amos Kwagala | `dashboard/` | `dashboard` |
| Atukunda Blessing Faith | `item-form/` | `item-form` |
| Mugeni Oustine | `purchase-form/` | `purchase-form` |
| Kizito Felix | `sale-form/` | `sale-form` |
| Bwambale Steven | `item-list/` | `item-list` |
| Anguzu Walter | `purchase-list/` | `purchase-list` |
| Namuli Swafiah | `sale-list/` | `sale-list` |
| Ahishar Jeduthun Osire | `profile/` | `profile` |

---

## Important

Each team member should:

- Work only inside their assigned folder
- Keep all CSS, JS, and image files inside their folder
- Avoid editing another person's files
- Push work to their own branch only

Thank you for your contribution 
Signed_________________4m05

### Database 

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    supplier VARCHAR(150),
    description TEXT
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id VARCHAR(50) NOT NULL UNIQUE,
    date DATE NOT NULL,
    item_id VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (item_id) REFERENCES items(item_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id VARCHAR(50) NOT NULL UNIQUE,
    date DATE NOT NULL,
    item_id VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (item_id) REFERENCES items(item_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
```
