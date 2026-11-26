> **IMPORTANT:** This is for the **`student_record_system`** branch and **`student_record_system`** folder. You only need to download **THAT** folder (`student_record_system`) for these instructions to work.

## INSTRUCTIONS READ AND UNDERSTAND THIS CAREFULLY!

1. Download the "student_record_system" folder
2. Copy and Paste it inside `C:\xampp\htdocs`
3. Launch XAMPP > enable APACHE and MYSQL
4. YOU HAVE 2 OPTIONS
   - **OPTION 1:** Go to phpmyadmin(open xampp and click "Admin" in the MYSQL row), click IMPORT, click CHOOSE FILE, select the "school_db.sql" file and IMPORT
   - **OPTION 2:** OPEN the "school_db.sql" file, COPY EVERYTHING INSIDE THE FILE, Go to phpmyadmin(http://localhost/phpmyadmin/), click SQL, PASTE(CTRL + V) the copied text, click GO
5. Enter `http://localhost/student_record_system/form.html` in your browser and try the System.
6. TEST the ADD STUDENT, EDIT(UPDATE), and DELETE

---

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
