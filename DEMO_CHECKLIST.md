# Demo Checklist

Open:

`http://127.0.0.1/campus-lost-found/login.php`

## Accounts

- Admin: `admin@ukm.edu.my` / `admin123`
- Student: `student@siswa.ukm.edu.my` / `user123`

## User Flow

1. Login as `student@siswa.ukm.edu.my`.
2. Open **Post Item**.
3. Submit a lost or found item with category, location, date, description, and optional image.
4. Open **My Posts** and confirm the post status is `Pending`.
5. Edit the post and confirm it returns to administrator verification.
6. Open an item without an uploaded image and confirm the detail page shows **No photo uploaded**.

## Admin Flow

1. Login as `admin@ukm.edu.my`.
2. Open **Admin**.
3. Open **Manage Posts**.
4. Approve or reject pending item posts.
5. Open **Manage Categories** and add, edit, or delete an unused category.
6. Open **General Report** and show status, type, category, and recent-post summaries.

## Public Item Flow

1. After admin approval, open **Items**.
2. Search using a keyword.
3. Filter by type, category, location, color, shape, size, weight, and date.
4. Open an item detail page.
5. Send a contact message to the poster.
6. Open **Messages** and confirm the message appears under **Sent Messages**.
7. Login as the poster and confirm the message appears under **Received Messages**.

## Implemented Requirements

- FR1-FR2: Register and login.
- FR3: Post lost/found items with details and images.
- FR4: Edit or delete own posts.
- FR5-FR6: Search and filter item records.
- FR7: Contact poster through an in-system message form with received and sent message views.
- FR8-FR9: Admin verification and post management.
- Admin category management is available through **Manage Categories**.
- FR10: Admin general report.
- FR11: MySQL database storage.
