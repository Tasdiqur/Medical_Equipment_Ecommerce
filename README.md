# MedRex (PHP + MySQL + Bootstrap)

Minimal medical e-commerce with two roles: **customer** and **shopowner**.
- Shop owners: CRUD their products via REST JSON API.
- Customers: browse, add to cart, and place orders.

## 1) Local setup (XAMPP)

1. Create a MySQL database named `medrex` and import `db/schema.sql` then `db/extras.sql`.
2. Copy `.env.example` to `.env` and update DB credentials.
3. Put the whole `medrex-php` folder under your XAMPP web root (e.g., `htdocs/`).
4. Visit `http://localhost/medrex-php/index.php`.

Demo accounts (password for all is `Admin@123`):
- Shop owner: `dr_smith@example.com`
- Customer: `john_doe@example.com`

## 2) API base

The API is under `/api`. Example:
- `POST /api/auth/register` JSON: `{ "full_name": "...", "email": "...", "password": "...", "role": "customer|shopowner" }`
- `POST /api/auth/login` JSON: `{ "email": "...", "password": "..." }` → returns auth `token`
- `GET  /api/products` (public)
- `POST /api/products` (shopowner) JSON with `name, description, price, stock, category, image_url`
- `PUT  /api/products/{id}` (shopowner)
- `DELETE /api/products/{id}` (shopowner)
- `POST /api/orders` (customer) JSON `{ items: [{ product_id, quantity }] }`
- `GET  /api/orders` (customer, own orders)

Send the token in header: `X-Auth-Token: <token>`.

## 3) Azure (quick outline)

- Create **App Service (Linux)** — PHP runtime. The *Free (F1)* plan works for testing.
- Create **Azure Database for MySQL - Flexible Server** (use the free 12-month limits if eligible).
- Set **Configuration → Application settings** on your Web App: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `CORS_ORIGIN`, `TOKEN_TTL_HOURS`.
- Deploy by GitHub Actions or ZIP deploy. Ensure `/api/.htaccess` is deployed.

See `azure-deploy.md` for step-by-step.

