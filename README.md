# 🎓 CourseHub — E-Learning Platform

A role-based e-learning web application designed for interactive online education and seamless course management.

---

CourseHub is a role-based e-learning web application that helps students discover courses, follow lessons, and monitor their learning progress. Teachers can create and manage course content, while administrators oversee users, categories, courses, and reviews from a dedicated dashboard.

The project is built with PHP and MySQL and is designed to run locally with XAMPP.

## ✨ Features

### 👨‍🎓 For students
* Create an account and sign in to a personal dashboard.
* Browse course categories and explore available courses.
* Enroll in courses and access their lessons.
* Mark lessons as completed and view learning progress.
* Take course quizzes and receive an instant score.
* View and update profile information.

### 👨‍🏫 For teachers
* Access a teacher dashboard with course and student statistics.
* Create, edit, and manage their own courses.
* Upload course images.
* Add, edit, order, and remove lessons.
* View the courses they have created and their enrolled students.

### 🛡️ For administrators
* View platform-wide statistics from the admin dashboard.
* Manage user accounts and roles.
* Create and manage course categories.
* Review and manage all courses.
* Moderate student reviews and ratings.

## 💻 Technology Stack

* **Backend:** PHP
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript
* **UI:** Bootstrap 5, Bootstrap Icons, and Font Awesome
* **Local server:** Apache through XAMPP

## 📁 Project Structure

```text
courses-platform/
├── admin/        # Admin dashboard and management pages
├── auth/         # Registration, login, and logout
├── categories/   # Course category pages
├── courses/      # Course creation, listing, and editing
├── lessons/      # Lesson management
├── student/      # Student dashboard, course player, quizzes, and profile
├── teacher/      # Teacher dashboard and student overview
├── progress/     # Lesson-completion tracking
├── upload/       # Uploaded course and category images
├── assets/       # CSS, JavaScript, and Bootstrap assets
├── connect.php   # Database connection and query helper class
└── index.php     # Public landing page