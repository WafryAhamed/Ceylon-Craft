# 🛍️ Ceylon Craft

![Laravel](https://img.shields.io/badge/Laravel-12-red?style=for-the-badge&logo=laravel)
![Vue](https://img.shields.io/badge/Vue-3-green?style=for-the-badge&logo=vue.js)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4-blue?style=for-the-badge&logo=tailwind-css)
![License](https://img.shields.io/badge/License-Apache_2.0-blue?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)

> 🌴 A modern full-stack e-commerce platform for showcasing and selling Sri Lankan handmade products.

---

## 📌 Overview

**Ceylon Craft** is a full-stack web application designed as a marketplace for authentic Sri Lankan handmade products.  
It provides a seamless shopping experience with a modern UI, powerful backend, and scalable architecture.

---

## 🏗️ Tech Stack

### 🔹 Backend
- Laravel 12 (PHP 8.2+)
- Laravel Sanctum (Authentication)
- SQLite / MySQL / PostgreSQL
- PHPUnit (Testing)

### 🔹 Frontend
- Vue 3 (Composition API)
- Pinia (State Management)
- Vue Router
- Tailwind CSS 4
- Vite

### 🔹 Tools
- Axios (HTTP requests)
- Faker (Seeding)
- Laravel Pint (Linting)
- Docker (Laravel Sail)

---

## 📁 Project Structure

```
ceylon-craft/
│
├── app/                    # Backend logic (Controllers, Models)
├── config/                 # Configuration files
├── database/               # Migrations & schema
├── resources/
│   ├── js/
│   │   ├── pages/         # Vue pages
│   │   ├── components/    # Reusable components
│   │   ├── stores/        # Pinia state
│   │   └── router/        # Routing
│   └── css/               # Tailwind setup
│
├── routes/                 # API & web routes
├── public/                 # Public assets
├── tests/                  # Unit & feature tests
└── vite.config.js         # Build configuration
```

---

## ✨ Features

### 🛒 E-Commerce
- Product listing & filtering
- Category management
- Shopping cart system
- Checkout & payments
- Order tracking

### 👤 User System
- Authentication (Login/Register)
- Profile management
- Wishlist
- Reviews & ratings

### 🛠️ Admin Panel
- Product CRUD
- Order management
- User management
- Dashboard analytics

### 📄 Additional Pages
- Blog system
- Contact page
- FAQ
- Privacy policy & Terms

---

## 🔐 Authentication

- Token-based authentication using **Laravel Sanctum**
- Roles:
  - `Admin`
  - `Customer`

---

## 📡 API Endpoints (Sample)

```bash
POST   /api/auth/login
GET    /api/products
POST   /api/cart/add
POST   /api/orders
GET    /api/admin/dashboard
```

---

## ⚙️ Installation

### 1️⃣ Clone Repository

```bash
git clone https://github.com/WafryAhamed/Ceylon-Craft.git
cd Ceylon-Craft
```

### 2️⃣ Backend Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 3️⃣ Frontend Setup

```bash
npm install
npm run dev
```

### 4️⃣ Run Server

```bash
php artisan serve
```

---

## 🧪 Testing

```bash
composer test
```

---

## 📦 Build for Production

```bash
npm run build
```

---

## 🌐 Environment Variables

```env
APP_NAME=CeylonCraft
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=sqlite
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## 📊 Project Info

- 📁 **Repository**: Ceylon Craft
- ⚙️ **Status**: Active Development
- 📅 **Last Updated**: April 2026
- 📜 **License**: Apache 2.0

---

## 👨‍💻 Author

**Mohamed Aroos**

- 🔗 LinkedIn: [https://www.linkedin.com/in/rmaroos/](https://www.linkedin.com/in/rmaroos/)
- 📧 Email: rmaroos2001@gmail.com

---

## ⭐ Contributing

Contributions are welcome!  
Feel free to fork the repository and submit a pull request.

---

## 📄 License

This project is licensed under the **Apache 2.0 License**.

---

## 💡 Future Improvements

- Mobile app (Flutter)
- AI product recommendations
- Real-time order tracking
- Payment gateway enhancements

---

## 🌟 If you like this project, don't forget to star it!

---

## 💬 Additional Customizations Available

I can also:
- ✨ Add **GitHub banner design**
- 📊 Create **architecture diagram (AWS style)**
- 🖼️ Add **screenshots section**
- 💼 Optimize for **LinkedIn showcase**

Just let me know! 👍
