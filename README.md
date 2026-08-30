# CourseHub — E-Learning Platform

CourseHub is a role-based e-learning web application that helps students discover courses, follow lessons, and monitor their learning progress. Teachers can create and manage course content, while administrators oversee users, categories, courses, and reviews from a dedicated dashboard.

The project is built with PHP and MySQL and is designed to run locally with XAMPP.

## Features

### For students

- Create an account and sign in to a personal dashboard.
- Browse course categories and explore available courses.
- Enroll in courses and access their lessons.
- Mark lessons as completed and view learning progress.
- Take course quizzes and receive an instant score.
- View and update profile information.

### For teachers

- Access a teacher dashboard with course and student statistics.
- Create, edit, and manage their own courses.
- Upload course images.
- Add, edit, order, and remove lessons.
- View the courses they have created and their enrolled students.

### For administrators

- View platform-wide statistics from the admin dashboard.
- Manage user accounts and roles.
- Create and manage course categories.
- Review and manage all courses.
- Moderate student reviews and ratings.

## Technology Stack

- **Backend:** PHP
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **UI:** Bootstrap 5, Bootstrap Icons, and Font Awesome
- **Local server:** Apache through XAMPP

## Project Structure

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
```

## Database Overview

The application uses the following main entities:

| Entity | Purpose |
| --- | --- |
| `users` | Stores accounts and roles: `student`, `teacher`, and `admin`. |
| `categories` | Organizes courses by learning topic. |
| `courses` | Stores course details, prices, images, categories, and teachers. |
| `lessons` | Stores the lessons that belong to each course. |
| `enrollments` | Connects students with their enrolled courses. |
| `progress` | Records completed lessons for each student. |
| `quizzes` | Stores course quiz questions and answers. |
| `reviews` | Stores student ratings and comments for courses. |

## Getting Started

1. Install and start **Apache** and **MySQL** using XAMPP.
2. Place the project inside your XAMPP `htdocs` directory:

   ```text
   C:/xampp/htdocs/courses-platform
   ```

3. Create a MySQL database named `courses-platform`.
4. Create or import the tables required by the application, including `users`, `categories`, `courses`, `lessons`, `enrollments`, `progress`, `quizzes`, and `reviews`.
5. Check the database settings in `connect.php` and update them if your MySQL credentials differ:

   ```php
   private const host_name = "localhost";
   private const user_name = "root";
   private const password = "";
   private const db = "courses-platform";
   ```

6. Open the application in your browser:

   ```text
   http://localhost/courses-platform/
   ```

## User Roles

| Role | Main responsibilities |
| --- | --- |
| Student | Explore courses, learn lessons, track progress, and take quizzes. |
| Teacher | Create courses and lessons, and manage their teaching content. |
| Admin | Manage users, categories, courses, and reviews across the platform. |

## Notes

- Uploaded images are stored in the `upload/` directory.
- The application uses PHP sessions to keep users signed in and direct them to the appropriate dashboard.
- Ensure that the web server has write permission for the `upload/` directory when enabling image uploads.