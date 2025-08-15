# And I Qoute

## Laravel + Vue.js Project

A modern web application built with Laravel backend and Vue.js frontend.

## 🛠️ Tech Used

**Backend:**
- Laravel 12
- PHP 8.2+

**Frontend:**
- Vue 3 (via Vite)
- Tailwind CSS
- ShadCN UI Components

**Development Tools:**
- Axios (HTTP client)
- Postman (API testing)
- Chrome Dev Tools
- Vue Dev Tools (Chrome Extension)



## 🚀 Prerequisites

- [Node.js](https://nodejs.org/) (v22.x or higher recommended)
- [Composer](https://getcomposer.org/) (for PHP dependencies)
- PHP (v8.2 or higher)


## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/michaelrrrxxz/test.git
   cd test

2. **Install PHP dependencies**
    ```bash
    composer install
    ```
3. **Install Node.js dependencies**
    ```
    npm install
    ```
4. **Copy the .env.example and rename it to .env**

5. **generate key (if needed)**
    ```
    php artisan key:generate
    ```
6. **Migrate**
    ```
    php artisan migrate
    ```
## ⚡ Running the Application

```
npm run start
```
**This will run application php artisan serve and npm run dev:**

Frontend: http://localhost:5174/

Backend: http://127.0.0.1:8000/



📂 Project Structure
<pre style="font-family: monospace; line-height: 1.5; padding: 15px; border-radius: 5px;">
project-root/
├── <strong>app/</strong>               # Laravel application core
├── <strong>resources/</strong>
│   └── <strong>js/</strong>            # Vue.js
│   └── <strong>views/welcome</strong>  #Entry Point
├── <strong>routes/api</strong>         # Application routes
├── <strong>vendor/</strong>            # Composer dependencies
└── <strong>node_modules/</strong>      # NPM dependencies
</pre>


## API's

```http
# Customer Routes
GET|HEAD   api/v1/customers                   customers.index › Api\CustomerController@index
POST       api/v1/customers                   customers.store › Api\CustomerController@store
GET|HEAD   api/v1/customers/{customer}        customers.show › Api\CustomerController@show
PUT|PATCH  api/v1/customers/{customer}        customers.update › Api\CustomerController@update
DELETE     api/v1/customers/{customer}        customers.destroy › Api\CustomerController@destroy
GET|HEAD   api/v1/customers/{customer}/edit   customers.edit › Api\CustomerController@edit

# Quotation Routes
GET|HEAD   api/v1/customers/{customer}/quotations  Api\QuotationController@byCustomer
POST       api/v1/customers/{customer}/quotations  Api\QuotationController@store
PUT|PATCH  api/v1/quotations/{quotation}      quotations.update › Api\QuotationController@update
DELETE     api/v1/quotations/{quotation}      quotations.destroy › Api\QuotationController@destroy
POST       api/v1/quotations/{quotation}/send-email  Api\QuotationController@sendEmail

```
