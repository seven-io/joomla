<p align="center">
  <img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" alt="seven.io Logo" />
</p>

<h1 align="center">Joomla Plugin for seven.io</h1>

<p align="center">
  Send SMS and voice messages directly from your Joomla administration panel.
</p>

<p align="center">
  <a href="https://github.com/seven-io/joomla/releases/latest"><img src="https://img.shields.io/github/v/release/seven-io/joomla?style=flat-square" alt="Latest Release"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-teal.svg?style=flat-square" alt="MIT License"></a>
  <img src="https://img.shields.io/badge/Joomla-5.x%20|%206.x-blue?style=flat-square" alt="Joomla Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-purple?style=flat-square" alt="PHP Version">
</p>

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Authentication](#authentication)
- [Features](#features)
- [SMS Automation](#sms-automation-v31)
- [SMS Options](#sms-options)
- [Project Structure](#project-structure)
- [Support](#support)
- [License](#license)

---

## Requirements

| Requirement | Version |
|-------------|---------|
| Joomla | 5.x or 6.x |
| PHP | 8.1+ |
| VirtueMart | 4.x *(optional, for customer selection)* |

> **Looking for Joomla 3 support?** The legacy version is available in the [`legacy/joomla3` branch](https://github.com/seven-io/joomla/tree/legacy/joomla3) or as [v1.3 release](https://github.com/seven-io/joomla/releases/tag/v1.3).

---

## Installation

1. Download the [latest release](https://github.com/seven-io/joomla/releases/latest)
2. Log in to your Joomla administrator panel
3. Navigate to **System → Install → Extensions**
4. Upload the ZIP file
5. Configure authentication (see [Authentication](#authentication))
6. Start sending via **Components → seven.io API → Messages**

---

## Authentication

The plugin uses OAuth 2.0 with PKCE for secure authentication.

1. Go to **Components → seven.io API**
2. Click **Connect with seven.io**
3. Log in to your [seven.io](https://www.seven.io) account and authorize the plugin
4. Done! The plugin automatically handles token refresh.

**Benefits:**
- No manual API key handling
- Automatic token refresh
- Can be revoked anytime from your seven.io account

---

## Features

| Feature | Description |
|---------|-------------|
| **SMS Messaging** | Send SMS to single or multiple recipients |
| **Voice Calls** | Text-to-speech voice messages |
| **SMS Automation** | Automatic SMS on events *(v3.1+)* |
| **OAuth 2.0** | Secure authentication with automatic token refresh |
| **VirtueMart Integration** | Select recipients by country or shopper group |
| **Message History** | View all sent messages and their delivery status |

---

## SMS Automation (v3.1+)

Automatically send SMS notifications based on events.

### Supported Events

**VirtueMart Events:**
- Order Confirmed
- Order Status Changed
- Order Shipped
- Order Cancelled

**Joomla Events:**
- User Registration
- Content Saved

### Template Variables

Use `{variable}` placeholders in your message templates. Available variables depend on the trigger type.

#### VirtueMart Order Events

Available for: Order Confirmed, Order Status Changed, Order Shipped, Order Cancelled

| Variable | Description |
|----------|-------------|
| `{order_id}` | Internal order ID |
| `{order_number}` | Order number |
| `{customer_name}` | Customer's full name |
| `{customer_firstname}` | Customer's first name |
| `{customer_lastname}` | Customer's last name |
| `{customer_email}` | Customer's email address |
| `{customer_phone}` | Customer's phone number |
| `{total}` | Order total (formatted) |
| `{currency}` | Order currency |
| `{status}` | Current order status |
| `{payment_method}` | Payment method name |
| `{shipping_method}` | Shipping method name |
| `{shop_name}` | VirtueMart shop name |

**Additional variables for Order Status Changed:**

| Variable | Description |
|----------|-------------|
| `{old_status}` | Previous order status |
| `{new_status}` | New order status |

**Additional variables for Order Shipped:**

| Variable | Description |
|----------|-------------|
| `{tracking_number}` | Shipment tracking number |
| `{carrier}` | Shipping carrier name |

**Additional variables for Order Cancelled:**

| Variable | Description |
|----------|-------------|
| `{cancellation_reason}` | Reason for cancellation |

#### User Registration

| Variable | Description |
|----------|-------------|
| `{username}` | Username |
| `{name}` | Full name |
| `{email}` | Email address |
| `{user_id}` | User ID |
| `{registration_date}` | Registration date/time |
| `{site_name}` | Joomla site name |

#### Content Saved

| Variable | Description |
|----------|-------------|
| `{article_title}` | Article title |
| `{article_id}` | Article ID |
| `{author_name}` | Author's name |
| `{category}` | Category name |
| `{created_date}` | Creation date/time |
| `{is_new}` | "Ja" if new, "Nein" if updated |
| `{site_name}` | Joomla site name |

### Setup

1. Go to **Components → seven.io API → Automations**
2. Click **New** to create an automation
3. Select a trigger event
4. Write your message template using variables
5. Choose the recipient type (customer, admin, or custom)
6. Enable the automation

### Required Plugins

For automation to work, install and enable the included plugins:

| Plugin | Type | Purpose |
|--------|------|---------|
| `plg_system_sevensms` | System | Handles Joomla events |
| `plg_vmshopper_sevensms` | VirtueMart | Handles VirtueMart order events |

Enable plugins in **Extensions → Plugins**.

---

## SMS Options

| Option | Description |
|--------|-------------|
| **Flash** | Display message directly on screen without storing |
| **Unicode** | Force unicode encoding for special characters |
| **UTF-8** | Force UTF-8 encoding |
| **Delay** | Schedule messages for later delivery (timestamp or date string) |
| **TTL** | Set message validity period in minutes (default: 2880 = 48h) |
| **Performance Tracking** | Track URL clicks in messages |
| **No Reload** | Prevent automatic page reload after sending |
| **Foreign ID** | Custom identifier for tracking (max 64 chars) |
| **Label** | Custom label for message grouping (max 100 chars) |
| **UDH** | User Data Header for binary SMS (advanced) |

---

## Project Structure

```
├── pkg_seven.xml                 # Package manifest
├── script.php                    # Installation/update script
├── src/                          # Main component
│   ├── seven.xml                 # Component manifest
│   └── admin/
│       ├── forms/                # Form definitions (XML)
│       ├── language/             # Translations (en-GB, de-DE)
│       ├── services/             # Joomla DI provider
│       ├── sql/                  # Database schemas & updates
│       ├── tmpl/                 # View templates
│       │   ├── automations/      # Automation list view
│       │   ├── automation/       # Automation edit view
│       │   ├── messages/         # SMS list view
│       │   ├── message/          # SMS edit view
│       │   ├── voices/           # Voice list view
│       │   └── voice/            # Voice edit view
│       └── src/
│           ├── Controller/       # MVC Controllers
│           ├── Model/            # MVC Models
│           ├── View/             # MVC Views
│           ├── Table/            # Database table classes
│           ├── Service/          # Business logic
│           │   ├── OAuthService.php
│           │   ├── SevenApiClient.php
│           │   ├── AutomationService.php
│           │   └── TemplateProcessor.php
│           └── Helper/           # Utility functions
├── plugins/
│   ├── system/sevensms/          # System plugin (Joomla events)
│   └── vmshopper/sevensms/       # VirtueMart plugin (order events)
└── pkg_language/                 # Package translations
```

---

## Support

Need help? [Contact us](https://www.seven.io/en/company/contact/) or [open an issue](https://github.com/seven-io/joomla/issues).

---

## License

This project is licensed under the [MIT License](LICENSE).
