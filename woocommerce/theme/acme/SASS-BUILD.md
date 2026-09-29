# SCSS Build Setup

Acme theme uses SCSS for stylesheet organization following the Storefront pattern.

## Setup

Install dependencies:
```bash
npm install
```

## Building

Compile SCSS to CSS:
```bash
npm run sass
```

Watch for changes during development:
```bash
npm run sass:watch
```

## Structure

```
assets/css/
├── main.scss           # Master file that imports all partials
├── sass/
│   ├── utils/          # Variables and mixins
│   ├── base/           # Global styles and layout
│   ├── components/     # Reusable UI components (buttons, header, footer, etc.)
│   ├── pages/          # Page-specific styles (checkout, cart, shop, etc.)
│   └── woocommerce/    # WooCommerce integration styles
└── style.css           # Compiled output (don't edit manually)
```

## Editing

- **Edit SCSS files** in `assets/css/sass/`
- **Don't edit** `style.css` directly — it's generated from SCSS
- Use Customizer > Additional CSS for theme customizations
