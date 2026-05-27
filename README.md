# Campus Lost and Found Web System

This project implements the Campus Lost and Found Web System described in `paper.docx`.
It uses the required stack from the report: HTML, CSS, JavaScript, PHP, MySQL, and XAMPP.

## Main Features

- Campus user registration and login
- Password hashing with PHP `password_hash`
- Lost/found item posting with structured item details
- UKM location dropdown for item posting and filtering
- Image upload for item reports
- Keyword search and filtering by category, date, location, type, color, shape, size, and weight
- Item details page with privacy-aware message form
- Messages page with Received and Sent message views
- My Posts page for users to edit or delete their own posts
- Admin category management: add, edit, and delete unused categories
- Admin moderation workflow: Pending, Approved, Rejected
- Admin post management: approve, reject, edit, delete
- Admin general report with status, type, category, and recent-post summaries
- Responsive layout for desktop and mobile browsers

## Installation With XAMPP

1. Copy the `campus-lost-found` folder into:

   `C:\xampp\htdocs\campus-lost-found`

2. Open XAMPP Control Panel and start:

   - Apache
   - MySQL

3. Open this URL:

   `http://localhost/campus-lost-found/install.php`

4. Click **Create Database and Tables**.

5. Wait for the success message. Do not open the login page until installation succeeds.

6. Login:

   - Admin email: `admin@ukm.edu.my`
   - Admin password: `admin123`

   Demo user:

   - User email: `student@siswa.ukm.edu.my`
   - User password: `user123`

## Configuration

Database settings are in:

`includes/config.php`

Default XAMPP settings:

```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_FALLBACK_PORTS', ['3307']);
define('DB_NAME', 'campus_lost_found');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Most XAMPP computers use MySQL port `3306`. The app also tries `3307`
automatically for computers where another MySQL service already uses `3306`.
If MySQL uses a different custom port, update `DB_PORT` in `includes/config.php`.

## Installation Troubleshooting

If the installer shows `Unable to connect to MySQL`, it means PHP cannot reach
the MySQL server yet. Open XAMPP Control Panel, start **MySQL**, then click
**Create Database and Tables** again.

If MySQL is already green in XAMPP but installation still fails, check the MySQL
port in XAMPP and set the same port in `includes/config.php`.

Campus email validation is configurable:

```php
define('CAMPUS_EMAIL_DOMAINS', ['siswa.ukm.edu.my', 'ukm.edu.my']);
```

The demo domains are `siswa.ukm.edu.my` and `ukm.edu.my`. Add or replace domains for the actual university.

## Database

The schema is stored at:

`database/schema.sql`

It creates these main tables:

- `users`
- `categories`
- `post_statuses`
- `items`
- `messages`

## Admin Category Management

Administrators can open:

`admin/categories.php`

They can add categories such as `Electronics`, `Documents`, `Keys`, `Bags`, and
`Clothing`, rename existing categories, and delete categories that are not used
by any item post.

## Project Mapping To Requirements

- FR1-FR2: `register.php`, `login.php`, `logout.php`
- FR3: `post_item.php`
- FR4: `my_posts.php`, `edit_item.php`, `delete_item.php`
- FR5-FR6: `index.php`
- FR7: `item.php`, `messages.php`
- FR8-FR9: `admin/posts.php`
- FR10: `admin/report.php`
- FR11: `database/schema.sql`, `includes/db.php`
