# Azure deployment (App Service + Azure Database for MySQL)

1) **Create MySQL (Flexible Server)**
- In Azure Portal, create *Azure Database for MySQL - Flexible Server*.
- Note host, admin, password, and DB name (create a DB `medrex`). Allow public access **or** VNet integration with App Service.
- Import `db/schema.sql` and `db/extras.sql` via MySQL Workbench or `mysql` CLI.

2) **Create App Service (Linux, PHP)**
- Runtime stack: PHP 8.x on Linux.
- Plan: Free (F1) for testing.
- After creation, go to **Configuration → Application settings** and add:
  - `DB_HOST`, `DB_PORT` (default `3306`), `DB_NAME`, `DB_USER`, `DB_PASS`
  - `CORS_ORIGIN` (e.g., your app URL)
  - `TOKEN_TTL_HOURS` (e.g., `72`)

3) **Deploy code**
- Easiest: ZIP deploy.
  - Build a zip of this folder and upload under **Deployment → Deployment Center → Zip deploy**.
- Or **GitHub Actions** (add the workflow provided if you prefer).

4) **Test**
- Browse to your app URL `/index.php`.
- Use the demo accounts from README.
