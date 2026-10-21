# Web setup wizard (for customers)

Buyers install the ERP in **one browser session** — no manual `.env` editing required.

## Customer steps (live server)

1. Upload the project to hosting (point domain to the `public` folder).
2. Create a MySQL database and note host, name, username, password.
3. Visit **`https://your-erp-domain.com/setup`**
4. Complete the wizard:
   - **Step 1** — Server requirements
   - **Step 2** — Database (use *Test connection*)
   - **Step 3** — Application URL (your live ERP URL)
   - **Step 4** — License (purchase code, Envato username, license server URL from seller)
   - **Step 5** — Click **Install now**
5. Log in at `/` with `admin@gmail.com` / `12345678` and change the password.

After install, `/setup` is disabled. The file `storage/app/.installed` marks completion.

## What the wizard runs automatically

- Writes `.env` (database, `APP_URL`, `ImagePath`, license server URL)
- `php artisan key:generate` (if needed)
- `php artisan migrate --seed`
- `php artisan passport:install`
- `php artisan storage:link`
- License activation (remote or offline)
- Creates install lock file

## Author: prepare the zip before selling

Before uploading to CodeCanyon, set these in **`.env.example`** (buyers inherit them on install):

```env
LICENSE_SECRET=same-64-char-secret-on-license-server-and-product
LICENSE_SERVER_URL=https://license-erp.fableadtech.in
LICENSE_PRODUCT_CODE=FABLEAD_ERP
LICENSE_MODE=remote
# License enforcement is always on (not in .env)
```

On **your license server** host:

```bash
php artisan migrate
php artisan license:register-purchase "BUYER-CODE" "envato_username" --type=regular
```

Register each purchase when you confirm the sale.

## Reinstall (development only)

Delete `storage/app/.installed` and visit `/setup?reinstall=1`.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Redirected to `/setup` forever | Finish the wizard or delete `storage/app/.installed` if reinstalling |
| License fails on install | Confirm purchase is registered on license server; check `LICENSE_SERVER_URL` |
| CSS broken after install | Wizard sets `ImagePath` from your URL; ensure it ends with `/` |
| 500 during install | Check `storage/logs/laravel.log`; fix folder permissions |

See also [LICENSE.md](LICENSE.md).
