# BULLER LEATHER — INTERNATIONAL E-COMMERCE PLATFORM

> **Generational Leather Craftsmanship & Heritage Luxury**  
> Complete Production-Ready International Multi-Country & Multi-Currency E-Commerce Application.

---

## 1. Quick Start & Local Execution

The website is fully functional, connected to a normalized **MySQL** database (`buller_leather_db`), and running with real luxury product photography and dynamic commerce engines.

### Environment Details
- **PHP**: 8.0+ with `pdo_mysql`, `curl`, `mbstring`, `openssl` enabled.
- **Database**: MySQL / MariaDB (XAMPP).
- **Composer Dependencies**: `razorpay/razorpay`, `phpmailer/phpmailer`.
- **Local Dev Server URL**: **`http://127.0.0.1:8080/`**

To launch the development server at any time:
```powershell
php -S 127.0.0.1:8080 router.php
```

---

## 2. Authentication & Demo Accounts

Pre-seeded accounts are ready for testing with a single click via the interactive demo credentials box on the Login page:

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@bullerleather.com` | `BullerAdmin@2026!` | **Global Unrestricted**: Dashboard, Country Provisioning, Admin Management (RBAC), Global Master Catalog, Global Orders, Audit Logs. |
| **India Admin** | `admin.in@bullerleather.com` | `IndiaAdmin@2026!` | **Scoped to India (IN)**: INR (₹) Pricing, Discounts, Local Stock, Orders & Blue Dart dispatch. |
| **West Admin** | `admin.west@bullerleather.com` | `WestAdmin@2026!` | **Scoped to US (USD $) & UK (GBP £)**: Multi-market switcher, Local Pricing, DHL Express dispatch. |
| **Patron Client** | `customer@example.com` | `Customer@2026!` | **Storefront Customer**: Bag, Wishlist, Multi-step Checkout, Order Tracking & Invoices. |

---

## 3. Core Architecture & Highlights

### 3.1 Multi-Country & Multi-Currency Engine
- **Preconfigured Markets**:
  - 🇮🇳 **India**: INR (`₹`), 18% GST, Domestic Flat Shipping ₹250, Free shipping threshold ₹5,000, Razorpay integration.
  - 🇺🇸 **United States**: USD (`$`), 7.5% Sales Tax, Flat Shipping $15, Free shipping threshold $150, International Card Gateway.
  - 🇬🇧 **United Kingdom**: GBP (`£`), 20% VAT, Flat Shipping £12, Free shipping threshold £120, International Card Gateway.
  - 🇦🇪 **United Arab Emirates**: AED (`AED`), 5% VAT, Flat Shipping 35 AED, Free shipping threshold 350 AED.
- **Dynamic Regional Pricing Matrix (`product_country_pricing`)**:
  - Each product has independent base prices, promotional discounts, discount start & end dates, inventory counts, and availability flags per country.
- **Market Switcher Modal**:
  - Changing the regional market instantly re-renders the catalog with localized currency, prices, taxes, and shipping rates.

### 3.2 Strict Role-Based Access Control (RBAC) & Country Scoping
- **Zero URL-Tampering Vulnerability**: Backend enforces permissions using `Auth::enforceCountryAccess($countryId)`.
- If an admin attempts to manipulate `?country_id=` or API payloads to access an unassigned country, the PHP backend halts execution with `403 Forbidden` and creates an immutable security violation entry in `audit_logs`.
- Super Admin can dynamically provision new administrators and assign one or multiple countries with custom permission checkboxes.

### 3.3 Super Admin Country Management
- Super Admin can provision new international markets directly from the UI without modifying PHP application code.
- Auto-populates catalog pricing records for the new country.

### 3.4 Luxury UI/UX Design System
- Custom color palette: Deep Espresso Brown (`#120b06`), Champagne Gold (`#c5a880`), Warm Cognac (`#8c4a2f`), and Ivory (`#fdfbf7`).
- Typography: *Playfair Display* & *Cinzel* editorial serifs with *Plus Jakarta Sans* body text.
- Interactive Offcanvas Cart Drawer with dynamic live subtotal calculation and free shipping progress tracker.
- Real high-resolution luxury product imagery for jackets, executive briefcases, weekend duffels, slim bifold wallets, English bridle belts, Chelsea boots, and watch rolls.

---

## 4. Key Endpoints & Routes

| Path | Description | Access |
| :--- | :--- | :--- |
| `/` | Luxury Boutique Homepage | Public |
| `/shop` | Master Catalog with Category & Tag Filtering | Public |
| `/product?slug={slug}` | Product Stage with Photo Viewer & Tabs | Public |
| `/cart` | Shopping Bag Overview | Public |
| `/checkout` | International Multi-Step Checkout | Public |
| `/login` | Client & Administrative Sign In | Public |
| `/register` | Patron Registration | Public |
| `/customer/dashboard` | Patron Profile & Order History | Customer |
| `/superadmin/dashboard` | Global Executive Dashboard & KPIs | Super Admin |
| `/superadmin/countries` | International Market Provisioning | Super Admin |
| `/superadmin/admins` | Administrator Governance & RBAC | Super Admin |
| `/admin/dashboard` | Scoped Country Dashboard | Country Admin |
| `/admin/products` | Regional Pricing & Stock Matrix | Country Admin |
| `/admin/orders` | Scoped Order Fulfillment & Tracking | Country Admin |
| `/api/cart.php` | AJAX Cart Add / Update / Remove | Public API |
| `/api/market.php` | AJAX Country & Currency Switcher | Public API |
| `/api/wishlist.php` | AJAX Wishlist Toggle | Public API |

---

## 5. Security & Verification Summary
- **SQL Injection**: Zero concatenated queries; 100% prepared PDO statements.
- **CSRF**: Synchronizer token verification across all forms.
- **XSS**: Strict output escaping with `e()` (`htmlspecialchars`).
- **Authorization**: Backend-enforced role and country validation.
