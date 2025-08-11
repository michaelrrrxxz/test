# 🛠 Test Application

A **full-stack** Laravel + Vue 3 (Vite) application with:
- **Backend** (`back`) → Laravel API
- **Frontend** (`front`) → Vue 3 + Vite + Shadcn UI

---

## 🚀 Installation & Setup

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/michaelrrrxxz/test.git
```
2️⃣ Backend Setup (Laravel)
cd back
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

3. FRontend Setep (Vue)
cd ../front
npm install


cd to the root folder then npm run start
Frontend will run at:
http://localhost:5173

Backend will run at:
http://localhost:8000


🌐 API Base URL

http://localhost:8000/api/v1
📌 API Routes – Customers

GET → http://localhost:8000/api/v1/customers
CustomerController@index

POST → http://localhost:8000/api/v1/customers
CustomerController@store

GET → http://localhost:8000/api/v1/customers/{customer}
CustomerController@show

PUT / PATCH → http://localhost:8000/api/v1/customers/{customer}
CustomerController@update

DELETE → http://localhost:8000/api/v1/customers/{customer}
CustomerController@destroy

GET → http://localhost:8000/api/v1/customers/{customer}/quotations
QuotationController@indexByCustomer

POST → http://localhost:8000/api/v1/customers/{customer}/quotations
QuotationController@storeForCustomer