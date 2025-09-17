# 📚 Hệ thống Quản Lý Khóa Học

---

### 🧑 User

- `id` (INT, PK)
- `fullName` (VARCHAR 200)
- `email` (VARCHAR 100)
- `phone` (VARCHAR 50)
- `address` (VARCHAR 500)
- `forget_token` (VARCHAR 500)
- `active_token` (VARCHAR 500)
- `status` (INT: 1/0)
- `permission` (TEXT: id khóa học)
- `group_id` → **Group**
- `created_at`, `updated_at`

---

### 🔑 Token_login

- `id` (INT, PK)
- `user_id` → **User**
- `token` (VARCHAR 200)
- `create_at`, `update_at`

---

### 📘 Course

- `id` (INT, PK)
- `name` (VARCHAR 100)
- `slug` (VARCHAR 100)
- `category_id` → **Course_category**
- `description` (TEXT)
- `price` (INT)
- `thumbnail` (VARCHAR 200)
- `create_at`, `update_at`

---

### 🗂️ Course_category

- `id` (INT, PK)
- `name` (VARCHAR 100)
- `slug` (VARCHAR 100)
- `create_at`, `update_at`

---

### 👥 Group

- `id` (INT, PK)
- `name` (VARCHAR 100)
- `create_at`, `update_at`

---

> ### 📌 Permission _(tham khảo)_
>
> - `create_at`, `update_at`
