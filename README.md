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
It provides a seamless shopping experience with a modern UI, powerful backend and scalable architecture.
<img width="1672" height="941" alt="ChatGPT Image Apr 28, 2026, 07_27_54 AM" src="https://github.com/user-attachments/assets/f666b30a-f82c-48fe-bea3-0190fe662c4e" />

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


---

## 📁 Project Structure

```bash
ceylon-craft/
│
├── app/                    # Backend logic (Controllers, Models)
├── config/                 # Configuration files
├── database/               # Migrations & schema
├── resources/
│   ├── js/
│   │   ├── pages/          # Vue pages
│   │   ├── components/     # Reusable components
│   │   │── stores/         # Pinia state
│   │   └── router/         # Routing
│   └── css/                # Tailwind setup
│
├── routes/                 # API & web routes
├── public/                 # Public assets
├── tests/                  # Unit & feature tests
└── vite.config.js          # Build configuration
