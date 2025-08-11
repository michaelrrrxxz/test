# 🛠 Test Application

A **full-stack** Laravel + Vue 3 (Vite) application with:
- **Backend** (`back`) → Laravel API
- **Frontend** (`front`) → Vue 3 + Vite + Shadcn UI

---

## 🚀 Installation & Setup

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/michaelrrrxxz/test.git

2️⃣ Backend Setup (Laravel)
cd back
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
cd ../front
npm install


cd to the root folder then npm run start
Frontend will run at:
http://localhost:5173

Backend will run at:
http://localhost:8000

🌐 API Base URL
All API endpoints are prefixed with:
http://localhost:8000/api/v1


📌 API Routes
Customers
Method	Endpoint	Controller Action
GET	/customers	CustomerController@index
POST	/customers	CustomerController@store
GET	/customers/{customer}	CustomerController@show
PUT/PATCH	/customers/{customer}	CustomerController@update
DELETE	/customers/{customer}	CustomerController@destroy
GET	/customers/{customer}/quotations	QuotationController@indexByCustomer
POST	/customers/{customer}/quotations	QuotationController@storeForCustomer