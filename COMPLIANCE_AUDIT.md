# Compliance Audit

This audit maps the implemented Campus Lost and Found Web System to the requirements described in `paper.docx`.

## Functional Requirements

| ID | Requirement | Implementation | Status |
|---|---|---|---|
| FR1 | Users shall register using a valid campus email account. | `register.php`, `includes/functions.php`, `CAMPUS_EMAIL_DOMAINS = ['siswa.ukm.edu.my', 'ukm.edu.my']` | Complete |
| FR2 | Users shall log in before posting or accessing personal features. | `login.php`, `includes/auth.php`, `require_login()` | Complete |
| FR3 | Users shall post lost/found items with detailed descriptions and images. | `post_item.php`, `uploads/`, `items` table | Complete |
| FR4 | Users shall edit or delete their item postings. | `my_posts.php`, `edit_item.php`, `delete_item.php` | Complete |
| FR5 | Users shall search items using keywords. | `index.php`, `build_item_filters()` | Complete |
| FR6 | Users shall filter results by category, date, or location. | `index.php` filter form and SQL filters; filters also include color, shape, size, and weight | Complete |
| FR7 | Users shall contact posters through email or a system message form. | `item.php`, `messages.php`, `messages` table, Received/Sent message views | Complete |
| FR8 | Admin shall verify all new postings before publication. | New posts use `Pending`; `admin/posts.php` approves or rejects | Complete |
| FR9 | Admin shall approve, modify, or delete any posting. | `admin/posts.php`, `edit_item.php`, delete action | Complete |
| FR9-A | Admin shall maintain item categories. | `admin/categories.php` supports add, edit, and delete for unused categories | Complete |
| FR10 | Admin shall generate and view general reports. | `admin/report.php` | Complete |
| FR11 | System shall record and store all user and item information in MySQL. | `database/schema.sql`, `includes/db.php` | Complete |

## Non-Functional Requirements

| ID | Requirement | Implementation | Status |
|---|---|---|---|
| NFR1 | Pages should load within 2-3 seconds. | Lightweight PHP pages and indexed MySQL queries | Meets local demo target |
| NFR2 | Search results must return within 1 second. | SQL filters and indexes on type, date, location, and item text | Meets local demo target |
| NFR3 | Passwords must be encrypted. | PHP `password_hash()` and `password_verify()` | Complete |
| NFR4 | User data must follow privacy/protection principles. | Poster email is not public; contact uses internal messages | Complete |
| NFR5 | Only verified admins may access moderation tools. | `require_admin()` protects `admin/*` pages | Complete |
| NFR6 | Interface must be intuitive and user-friendly. | Simple navigation, filter bar, status badges, dashboards | Complete |
| NFR7 | A lost/found post should be completable within 3 minutes. | Single post form with required fields and optional image | Complete |
| NFR8 | System must support modern browsers. | Standard HTML/CSS/JavaScript/PHP output | Complete |
| NFR9 | Interface must be mobile-responsive. | Responsive CSS media queries | Complete |
| NFR10 | System must handle form validation and prevent data loss. | Server-side validation, CSRF protection, required-field checks | Complete |

## Language Check

All user-facing project files are written in English. A source scan found no Chinese text in PHP, CSS, JavaScript, SQL, Markdown, or batch files.

## Verification Performed

Checked on 2026-05-21:

| Test | Result |
|---|---|
| PHP syntax check for all PHP files | Passed |
| Source scan for Chinese/CJK text | Passed |
| Student login with `student@siswa.ukm.edu.my` / `user123` | Passed |
| Admin report page loads | Passed |
| Admin manage posts page loads | Passed |
| Non-campus email registration is rejected | Passed |
| Campus email registration is accepted | Passed |
| Search finds an approved item | Passed |
| Sender can message an item poster and see the message under Sent | Passed |
| Poster can login and see the received message under Received | Passed |
| Admin can manage categories | Passed |
| Location field is a dropdown using UKM locations | Passed |
| Detail page shows `No photo uploaded` when image is missing | Passed |
