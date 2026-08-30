# 🎓 Courses Platform (E-Learning Management System)

A full-featured E-Learning Web Application built with **PHP (OOP)**, **MySQL**, and **Bootstrap 5**. The platform provides dedicated dashboards and workflows for **Admins**, **Instructors**, and **Students**.

---

## 🚀 Key Features

### 🛠️ Admin Panel
* **Analytics Dashboard:** Real-time metrics for total revenue, active users, published courses, and categories.
* **User Management:** View all registered accounts, modify user roles (`student`, `teacher`, `admin`), and delete accounts with real-time search filtering.
* **Category Management:** Create, update (via modal), and delete course categories with live course count badges.
* **Course Moderation:** Monitor courses, instructors, prices, lessons, and student enrollments with direct preview links.
* **Review Moderation:** Track student feedback, star ratings, and delete inappropriate reviews.

### 👨‍🎓 Student Portal
* **Student Dashboard:** Track enrolled courses, completed lessons, overall learning progress, and registered tracks.
* **Course Player & Lesson Tracker:** Interactive course viewer with progress tracking and a "Mark as Completed" feature.
* **Quiz & Assessment Engine:** Dynamic quiz taking with instant evaluation, score calculation, and percentage breakdown.
* **Profile Management:** Update personal information, email validation, and secure password updates.

### 🔒 Core Architecture & Security
* **Object-Oriented Database Layer:** Reusable `connect` class handling CRUD operations (`insert`, `update`, `delete`, `select`, `customQuery`) using `mysqli`.
* **Role-Based Access Control (RBAC):** Session-driven route protection preventing unauthorized access across roles.
* **Relational Database Design:** Foreign key constraints with `ON DELETE CASCADE` across 9 core tables.

---

## 🛠️ Tech Stack

* **Backend:** PHP 8.x (OOP Paradigm)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla ES6)
* **UI Framework:** Bootstrap 5 & Bootstrap Icons / Font Awesome
* **Environment:** Apache / XAMPP

---

## 🗄️ Database Schema

The database consists of **9 relational tables**:

| Table | Description |
| :--- | :--- |
| `users` | Stores user credentials, profile data, and roles (`admin`, `teacher`, `student`). |
| `categories` | Course tracks and specialties. |
| `courses` | Course details, pricing, cover images, teacher links, and category relations. |
| `lessons` | Course content, lesson titles, body text, and sequencing. |
| `enrollments` | Student course registrations. |
| `progress` | Lesson completion tracking per student. |
| `quizzes` | Assessment questions and correct answers linked to courses. |
| `answers` | Student submissions and multiple-choice options. |
| `reviews` | Student ratings (1 to 5 stars) and comments for courses. |

---

## ⚙️ Installation & Setup

1. **Clone the Repository:**
   ```bash
   git clone [https://github.com/omar-mahmoud-2004/courses-platform.git](https://github.com/omar-mahmoud-2004/courses-platform.git)