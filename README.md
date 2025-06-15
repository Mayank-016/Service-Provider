# 🔧 Service Booking API - Laravel 12

A RESTful API built using Laravel 12 for managing service bookings between users and providers with role-based access, booking lifecycle management, availability scheduling, notifications (Mail + Pusher), and reporting.

---

## 📦 Requirements

- PHP 8.2+
- Laravel 12
- MySQL
- Redis (for queues)
- Mail service (e.g. Mailtrap, SMTP)
- Pusher (for real-time notifications)

---

## 🚀 Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone https://your-repo-url.git
   cd your-project

2. **Install Dependencies**

   ```bash
   composer install
   ```

3. **Environment Configuration**
   Copy `.env.example` to `.env` and update:

   ```env
   DB_DATABASE=your_db
   DB_USERNAME=root
   DB_PASSWORD=

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   MAIL_FROM_ADDRESS=admin@example.com
   MAIL_FROM_NAME="Booking App"

   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_key
   PUSHER_APP_SECRET=your_secret
   PUSHER_HOST=
   PUSHER_PORT=443
   PUSHER_SCHEME=https
   ```

4. **Generate App Key**

   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**

   ```bash
   php artisan migrate
   ```
6. **Run Seeder**
    for initial Categories and Services
   ```bash
   php artisan db:seed --class=CategoryServiceSeeder    
   ```

7. **Queue Worker (for Notifications)**

   ```bash
   php artisan queue:work
   ```

8. **Run Server**

   ```bash
   php artisan serve
   ```

---

## 🔐 Authentication

### 🔑 User Registration

`POST /api/auth/register`

```json
{
  "name": "User 1",
  "email": "user1@test.com",
  "password": "12345678"
}
```

### 🛠 Supplier Registration

`POST /api/auth/register`

```json
{
  "name": "Supplier 1",
  "email": "supplier1@test.com",
  "password": "12345678",
  "is_supplier": true
}
```

### 🔓 Login

`POST /api/auth/login`

```json
{
  "email": "user1@test.com",
  "password": "12345678"
}
```

### 🚪 Logout

`POST /api/auth/logout`

---

## 🔐 Admin Registration

> Admins require special headers or CLI:

### 🔑 Via Header

Set `.env` keys:

```env
ADMIN_TOKEN=s2e29nd209!3223@@dwd
ADMIN_TOKEN_KEY=is_admin_user!##
```

Headers:

```
is_admin_user!##: s2e29nd209!3223@@dwd
```

### 🔧 Via CLI

```bash
php artisan app:register-new-admin
```

---

## 📚 API Endpoints

### 📂 Categories & Services

* `GET /api/all_categories`
* `GET /api/all_services`
* `GET /api/all_category_service`

> Admin Only:

* `POST /api/add_category`

> Provider Only:

* `POST /api/add_service`
* `POST /api/manage_service`

### ⏰ Availability

> Provider Only:

* `POST /api/manage_availability`

* `GET /api/provider_availability?provider_id=4&date=2025-06-16`

---

## 📅 Bookings

### 🧾 Booking

* `POST /api/book_service`

```json
{
  "provider_id": 4,
  "service_id": 9,
  "date": "2025-06-16",
  "start_time": "10:00:00"
}
```

* `POST /api/cancel_booking`

```json
{
  "booking_id": 1
}
```

### 📋 Booking Lists

Paginated (optional `?page=1`):

* `GET /api/future_bookings`
* `GET /api/all_bookings`
* `GET /api/booking_history`

---

## 📊 Reporting

### 🔍 Get Reporting

`GET /api/reporting`

* For both users and providers.
* Includes: total bookings, bookings today, most requested service, earnings/spending, etc.

---

## 📢 Real-Time Notifications

Pusher is used for broadcasting booking confirmation events.

Channels:

* `private-user{userId}`
* `private-provider{providerId}`

Events:

* `booking.confirmed`
* `booking.cancelled`

To listen on frontend:

```js
Echo.private('user1').listen('.booking.confirmed', (e) => {
  console.log(e.message);
});
```

---

## 📨 Emails

Emails are sent using Laravel Notifications when a booking is confirmed:

* User receives a booking confirmation
* Provider receives a confirmation notice

Ensure `queue:work` is running to send emails if queued.

---
## ✅ Status Labels

Booking `status` is returned as a readable string in API responses:

```json
"status": 0,
"status_label": "confirmed"
```

Internally, status is still stored as an integer in DB.

---
Here’s a cleaner, well-formatted, **copyable** version of your Postman Collection share, with improved readability and markdown-friendly formatting:

---

## 📮🧑‍💻📬 Postman Collection

🔗 **Service Provider API Collection**
Click the link below to open in Postman:

[Open Postman Collection](https://www.postman.com/science-participant-50153919/service-provider-api/collection/uj16tiu/serviceprovider?action=share&creator=27419417&active-environment=27419417-73a2a75c-4452-449a-8a12-e4da055f1cf7)

---