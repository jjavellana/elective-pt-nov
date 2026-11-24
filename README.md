# Student Records Mini-System

A simple PHP and MySQL CRUD web application for managing student records (`BSCS`, `BSIT`, and `BSRTCS`).

## Setup & Installation Instructions

1. **Place Folder**
   Extract `student_system` from the ZIP into `C:\xampp\htdocs\` (overwrite existing files when prompted).

2. **Import Database**
   - Start **Apache** and **MySQL** in the XAMPP Control Panel.
   - Open `http://localhost/phpmyadmin` in your browser.
   - Click **Import**, select or drag `school_db.sql` from the `student_system` folder, and click **Import** (no additional settings needed).

3. **Test in Browser**
   - Open `http://localhost/student_system/form.html` in your browser.
   - Add a student and confirm it redirects to `view.php` with the confirmation banner and working **Update** and **Delete** links.

## Project Files

- `db.php` – Database connection script (`school_db`)
- `form.html` – Form for adding a new student record
- `process.php` – Validates, sanitizes, and inserts new records into MySQL
- `view.php` / `index.php` – Displays all student records with Update and Delete actions
- `edit.php` – Form and handler for updating an existing student record
- `delete.php` – Deletes a student record by ID
- `school_db.sql` – SQL script to create `school_db`, the `students` table, and sample rows

---

*Feel free to modify or improve the code.*
