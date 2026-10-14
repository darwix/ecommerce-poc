# ecommerce-poc

Proof-of-concept rebuild of the Acme fashion & beauty store on WordPress + WooCommerce.

## What's here

- `theme/acme/` – custom WooCommerce theme (`zip.py` packages it for upload).
- `compose.yaml` – local podman stack: WordPress + MariaDB.
- `seed/` – setup scripts (pages, menu, i18n, DOKU, host setup).
- `deploy/` – host deploy files and product CSV.

## Run locally

```sh
podman compose up -d   # WordPress + MariaDB on http://localhost:8080
```

## Payments

DOKU Checkout (sandbox) via the official `doku-payment` plugin. Credentials are never committed.

## Docs

- [DESIGN.md](DESIGN.md) – design rules
- [DEPLOYMENT.md](DEPLOYMENT.md) – hosting setup and gotchas
- [TESTPLAN.md](TESTPLAN.md) – test plan
