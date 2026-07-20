This `README.md` is designed to help your developer get the environment running quickly and understand exactly where the "Kajabi-Laravel Bridge" logic lives so they can begin working on the API integration.

---

# OHC Member Portal (Laravel)

This is the custom Member Portal for OHC Trade Room. It acts as the "Member Operating System," while Kajabi remains the source of truth for payments and course content.

## 🛠 Tech Stack

- **Framework:** Laravel 11
- **Frontend:** Blade Templates + Tailwind CSS + Custom Institutional CSS
- **Database:** MySQL
- **Authentication:** Laravel Breeze (Modified for Kajabi Bridge)

---

## 🚀 Quick Start / Installation

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Frontend dependencies
npm install
```

### 2. Environment Setup

Copy the example environment file and generate your app key:

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

1. Create a MySQL database named `ohc_portal`.
2. Update your `.env` file with your database credentials.
3. **Crucial:** Add your Kajabi API credentials to the `.env`:

```env
KAJABI_API_KEY=your_key_here
KAJABI_API_SECRET=your_secret_here
KAJABI_SITE_URL=https://courses.ohctraderoom.com
```

### 4. Run Migrations & Seeders

This will set up the custom `users` table (First/Last name) and the `member_entitlements` table, then create the test users (**Carrick Jones** and **Sarah Investor**).

```bash
php artisan migrate:fresh --seed
```

### 5. Start the Application

```bash
# Start the Laravel server
php artisan serve

# Start the Vite assets (for Tailwind)
npm run dev
```

## 🧪 Testing Accounts

```bash
# Start the Laravel server
Email: carrickjones.cj@gmail.com
Pass: 12345678

Email: sarah@example.com
Pass: 12345678

```

---

## 🔑 API & Kajabi Integration (Developer Guide)

The core of this project is the **Member Synchronization Layer**. Since we are on a Kajabi Basic plan, we use a **Just-In-Time (JIT) Sync** via a Signed Bridge.

### Core API Files

If you are working on the API or the Sync logic, these are the files you need to focus on:

#### 1. The Service Layer: `app/Services/KajabiService.php`

- **Purpose:** Handles all outgoing requests to the Kajabi API v1.
- **Logic:** Contains the `getKajabiMember($email)` method which queries Kajabi to verify a member's existence and status.

#### 2. The Bridge Controller: `app/Http/Controllers/Auth/KajabiBridgeController.php`

- **Purpose:** The entry point for members coming from Kajabi.
- **Logic:**
    - Receives an email and a security signature.
    - Verifies the signature using the `KAJABI_API_SECRET`.
    - If the user doesn't exist in the local MySQL, it triggers the `KajabiService` to fetch data and create the user on the fly.

#### 3. The Webhook Handler: `app/Http/Controllers/KajabiWebhookController.php`

- **Purpose:** (Optional/Future) Designed to receive POST requests from Kajabi for real-time updates.
- **Note:** Currently bypassed in favor of the JIT Bridge due to plan restrictions, but ready for implementation.

#### 4. The Configuration: `config/services.php`

- **Purpose:** Maps the `.env` keys into the Laravel config system.
- **Key:** Look for the `'kajabi'` array at the bottom of the file.

#### 5. API Routes: `routes/api.php`

- **Purpose:** Defines the endpoint for incoming webhooks.
- **Endpoint:** `POST /api/kajabi-webhook`

---

## 📂 Database Architecture

We have modified the default Laravel `users` table to match the OHC Spec:

- **`users`**: Stores `first_name`, `last_name`, `kajabi_user_id`, and `current_path`.
- **`member_entitlements`**: Stores specific product ownership. This is used by the `hasAccessTo()` helper in the `User` model to determine "Locked/Unlocked" states on the frontend.

## 🎨 UI & Styling

- **`resources/css/style.css`**: Contains the universal "Institutional" design system (Navy/Teal/Glassmorphism).
- **`resources/css/courses.css`**: Specific styles for the Course Library grid and locked states.

---

## 🧪 Testing the Bridge

To test the login handshake locally:

1. Use **Ngrok** to expose your local port 8000.
2. Use the following URL format to simulate a redirect from Kajabi:
   `https://your-ngrok-url.app/auth/kajabi-bridge?email=carrickjones.cj@gmial.com&signature=[HASH]`
