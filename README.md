# 📝 BlogCMS - Console Edition

A robust, object-oriented Content Management System (CMS) built entirely in **PHP** for the Command Line Interface (CLI). 

This project demonstrates advanced PHP concepts including **Inheritance**, **Polymorphism**, **Encapsulation**, and **Data Structures** (Linked Lists/Trees for categories) without relying on external frameworks or databases.

---

## 🚀 Features

### 🔹 Article Management
- **CRUD Operations:** Create, Read, Update, and Delete articles.
- **Publishing Workflow:** Articles start as `draft` and must be published to be visible to visitors.
- **Formatted Output:** All data is displayed in clean, aligned CLI tables using `printf`.

### 🔹 Advanced Comment System
- **Moderation Pipeline:** 1. Visitors/Users post comments.
  2. Status is set to `pending`.
  3. Comments are **invisible** to the public until approved.
  4. Editors/Admins can `Approve` or `Refuse` comments.

### 🔹 Category Hierarchy
- **Tree Structure:** Supports Main Categories and infinite levels of Sub-Categories.
- **Recursive Display:** Visual tree representation of categories (e.g., `Techno |__ Coding |__ PHP`).

### 🔹 User & Role Management
- **Authentication:** Secure Login/Logout system.
- **Role-Based Access Control (RBAC):**
  - **Visitor:** Read public articles, post comments.
  - **Author:** CRUD on *own* articles.
  - **Editor:** Manage *all* articles, moderate comments, manage categories.
  - **Admin:** All Editor privileges + Manage Users (Add/Edit/Delete).

---

## 🛠️ Technical Architecture

The project is structured around key classes:

| Class | Description |
| :--- | :--- |
| **User** | Base class for all users. Handles authentication. |
| **Author** | Extends `User`. Can write articles. |
| **Editor** | Extends `User`. Can moderate content. |
| **Admin** | Extends `Editor`. Can manage system users. |
| **Article** | Entity containing title, content, status, and comments array. |
| **Category** | Entity supporting parent/child relationships. |
| **Collection** | The "Database" class. Manages arrays of Users, Articles, and Categories in memory. |

---

## 💻 Installation & Usage

### Prerequisites
- PHP 8.0 or higher installed on your machine.

### How to Run
1. Clone this repository or download the files.
2. Open your terminal in the project folder.
3. Run the main script:
   ```bash
   php dash.php

   
