# SoleOrigine — Luxury WordPress Theme

Premium dark-themed WooCommerce WordPress theme for a luxury Italian footwear brand.

## Theme Directory

`C:\Users\hammad\Downloads\worldpress\soleorigine-theme\`

## Design Tokens

| Token | Value | Usage |
|---|---|---|
| `--color-primary-dark` | `#1a1a1a` | Page bg, dark sections |
| `--color-secondary-dark` | `#111` | Cards, dropdowns, overlays |
| `--color-accent` | `#b8860b` | Buttons, highlights |
| `--color-accent-hover` | `#d4a843` | Hover states |
| `--color-gold` | `#c9a96e` | Headings, borders |
| `--color-light-gold` | `#e8d5a3` | Subtle gold |
| `--color-text` | `#f5f5f5` | Body text |
| `--color-text-muted` | `#999` | Secondary text |
| `--color-border` | `#333` | Borders, dividers |
| `--font-heading` | `Playfair Display` | H1–H6 |
| `--font-body` | `Inter` | Body, nav, buttons |

## File Structure

```
soleorigine-theme/
├── style.css              # Theme metadata + base CSS (1473 lines)
├── screenshot.png         # 1200×900 customizer preview
├── functions.php          # Theme setup, CPTs, requires
├── header.php / footer.php / index.php / front-page.php
├── page.php / single.php / archive.php / search.php / 404.php
├── sidebar.php / comments.php / searchform.php / home.php
├── inc/
│   ├── customizer.php     # Customizer sections & live preview
│   └── widgets.php        # 3 custom widgets (Social, About, Newsletter)
├── page-templates/
│   ├── template-about.php / template-contact.php
│   └── template-gallery.php / template-full-width.php
├── template-parts/
│   ├── content.php / product-card.php / footer-widgets.php
│   └── hero.php / features.php / about.php
├── woocommerce/
│   ├── archive-product.php / single-product.php
│   ├── content-product.php / content-single-product.php
│   ├── simple.php / variable.php
│   └── cart/cart.php / checkout/checkout.php / myaccount/my-account.php
├── js/
│   ├── navigation.js / customizer.js / custom.js
├── css/
│   ├── woocommerce.css / responsive.css / admin.css
├── admin/
│   └── theme-info.php     # Admin info page
└── languages/
    └── soleorigine.pot    # Translation template
```

## Registered Features

- **Post types:** Collections (`collection`), Styles (`style`), Brands (`brand`)
- **Taxonomies:** Collection Category, Style Category, Brand Category
- **Nav menus:** Primary Menu, Footer Menu
- **Widget areas:** Sidebar, Footer 1/2/3
- **WooCommerce support:** Yes (template overrides active)

## Skill Loading Order

When starting work, load skills in this priority order:

1. **Core theme:** `wp-theme-development` (primary WordPress skill)
2. **WooCommerce:** `wp-woocommerce-dev` (product templates, cart, checkout)
3. **Design system:** `wpds` + `web-design-guidelines` + `brand-guidelines` + `brandkit` (design tokens, WP conventions, brand consistency)
4. **Design polish:** `ui-ux-pro-max-skill` + `color-expert` + `theme-factory` + `impeccable-design-polish` + `taste-skill-v1` (premium look, color harmony, theme palettes)
5. **Animations:** `gsap-core` + `gsap-scrolltrigger` + `gsap-plugins` (scroll-triggered reveals, micro-interactions, hero animations)
6. **Marketing & Content:** `marketing-psychology` + `brand` + `copywriting` + `ecommerce-image-workflow` (conversion optimization, brand storytelling, product images)
7. **Quality & Review:** `design-review` + `wp-security` (final polish, code audit, accessibility check)

## Available Skills (`.agents/skills/`)

### WordPress Core (from wordpress-skills)
`wp-theme-development` `wp-woocommerce-dev` `wp-block-development` `wp-cli` `wp-cron` `wp-custom-post-types` `wp-database` `wp-debug` `wp-hooks` `wp-local-env` `wp-multisite` `wp-performance` `wp-rest-api` `wp-security` `wp-seo` `wp-testing` `wp-translations` `wp-user-management` `wp-gutenberg` `wp-mu-plugin`

### Design & Brand (from open-design + uiux)
`ui-ux-pro-max-skill` `web-design-guidelines` `brand-guidelines` `brand-guidelines-anthropic` `brand-guidelines-community` `color-expert` `theme-factory` `wpds` `impeccable-design-polish` `taste-skill-v1` `platform-design` `redesign-skill` `design-review` `design-brief` `brand-extract` `brandkit` `ecommerce-image-workflow` `mockup-device-3d` `copywriting`

### GSAP Animation (from open-design)
`gsap-core` `gsap-scrolltrigger` `gsap-plugins` `gsap-timeline` `gsap-utils` `gsap-frameworks` `gsap-react` `gsap-performance`

### Marketing
`marketing-psychology` `brand` `ad-creative` `content-strategy` `cold-email` `paid-ads`

## MCP Servers

Configured in `opencode.json` at project root:

- **Context7** — Live library/framework docs (WordPress, WooCommerce, PHP, GSAP)
- **Headroom MCP** — WordPress project auditing, PHP linting, template hierarchy checks
- **Serena MCP** — Design-to-code conversion, screen capture, visual regression

## Common Commands

- Test theme: Visit `wp-admin/themes.php`, activate SoleOrigine
- Verify WooCommerce: Visit a shop page, single product, cart, checkout
- Check CPTs: Visit `/wp-admin/edit.php?post_type=collection` (or style, brand)
- Update .pot: `wp i18n make-pot . languages/soleorigine.pot`
- Check template hierarchy: `wp eval 'var_dump(get_page_template());'`
- Lint PHP: `php -l` on any modified file
- Lint CSS: Check `style.css` `:root` variables match design tokens

## SASS/CSS Conventions

- CSS custom properties in `:root` in `style.css`
- WooCommerce overrides in `css/woocommerce.css`
- Responsive breakpoints in `css/responsive.css`: 992px, 576px, 400px
- SVG icons only (no font icons)
- No comments in code output (AI generation)
- Animations use GSAP (loaded via enqueued `custom.js`)
