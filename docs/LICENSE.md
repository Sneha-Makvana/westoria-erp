# License system (CodeCanyon-style)

This ERP includes **domain-bound licensing** similar to typical Envato / CodeCanyon products:

- **Regular** — one production domain per purchase code  
- **Extended** — multiple domains (default 5, configurable)  
- **Development** — localhost / `.test` / `.local` only  

## Security model

| Layer | Purpose |
|--------|---------|
| Encrypted file | License stored in `storage/app/.license/` (not in `.env`) |
| HMAC signature | Tampering with license data invalidates the install |
| Installation binding | License file is tied to `APP_KEY` — cannot be copied to another server |
| Global middleware | Blocks web UI and API when unlicensed |
| Periodic verify | Daily `license:verify` + remote re-check every 24h |
| Grace period | 72h offline grace if license server is temporarily down |

> No PHP license is unbreakable if someone edits source code. This system matches marketplace expectations and stops casual piracy (copying to another domain, sharing one purchase on many sites).

---

## For buyers (after purchase)

1. Deploy the ERP on the buyer’s domain.  
2. Open **`/license`** (e.g. `https://erp.client.com/license`).  
3. Enter **Purchase Code** and **Envato username**.  
4. On success, sign in at `/`.

### CLI activation

```bash
php artisan license:activate --purchase-code=XXXX --buyer=envato_username
```

### Offline key (if author sends a key)

Set in `.env`:

```env
LICENSE_MODE=offline
```

Paste the key on `/license` or:

```bash
php artisan license:activate --key="PASTE_TOKEN_HERE"
```

---

## For you (author / seller)

### 1. Generate a strong secret (same on server + all buyer builds)

```env
LICENSE_SECRET=your-random-64-char-secret-here
LICENSE_PRODUCT_CODE=FABLEAD_ERP
```

Use the **same** `LICENSE_SECRET` on your license server and in the product you ship.

### 2. Deploy license API

Host this project (or only `routes/license-api.php` + related classes) at:

**`https://license-erp.fableadtech.in`**

Buyers set:

```env
LICENSE_MODE=remote
LICENSE_SERVER_URL=https://license-erp.fableadtech.in
LICENSE_SERVER_API_KEY=optional-api-key-for-server
```

**Local testing** (`php artisan serve`): set `LICENSE_SERVER_URL` to the same value as `APP_URL` (e.g. `http://127.0.0.1:8000`). The app automatically uses in-process activation instead of HTTP (the built-in PHP server cannot call itself over HTTP).

Run migration on the **license server** database:

```bash
php artisan migrate
```

### 3. Register each sale

```bash
php artisan license:register-purchase "PURCHASE-CODE-HERE" "envato_username" --type=regular
php artisan license:register-purchase "CODE" "user" --type=extended --max-domains=5
php artisan license:register-purchase "CODE" "user" --type=development
```

### 4. Revoke / suspend

Update `product_licenses.status` to `revoked` or `suspended` in the license server DB. Next verify call will block the client.

### 5. Offline license key (manual / reseller)

```bash
php artisan license:generate "PURCHASE-CODE" "buyer" --type=extended --domains=client.com,staging.client.com
```

Send the printed token to the buyer (offline mode).

### 6. Your own development

Buyer ERP builds always enforce licensing. For localhost, register a **development** purchase code on the license server, or work in the `fablead-license` project with `LICENSE_ENABLED=false` on that host only.

---

## API reference (license server)

| Endpoint | Method | Body |
|----------|--------|------|
| `/api/v1/license/activate` | POST | `product_code`, `purchase_code`, `buyer`, `domain`, `installation_id` |
| `/api/v1/license/verify` | POST | `product_code`, `purchase_code_hash`, `buyer`, `domain`, `installation_id` |
| `/api/v1/license/add-domain` | POST | extended only — `purchase_code`, `buyer`, `domain`, `installation_id` |

Header (optional): `X-License-Api-Key: {LICENSE_SERVER_API_KEY}`

---

## Envato integration (optional)

This repo does **not** call Envato API automatically. You register codes when you confirm sales (manual, webhook, or your own Envato API script). That is how most CodeCanyon authors implement licensing.

To automate: add a webhook that runs `license:register-purchase` when Envato notifies you of a sale.
