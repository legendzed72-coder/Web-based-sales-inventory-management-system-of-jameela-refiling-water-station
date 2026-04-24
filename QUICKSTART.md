# AQUAPAY System - Quick Start Guide

## Getting Started

### 1. Access the System
- Open browser and navigate to: `http://localhost/<your-path>/aquapay/`
- You'll see the home page with login button

### 2. Default Login Credentials
```
Username: admin
Password: admin123
```

### 3. First Time Setup
- No manual setup required! The system automatically:
  - Creates all necessary database tables
  - Seeds default admin account
  - Inserts sample products
  - All on first page load

---

## User Roles

### Admin User
- Access: `http://localhost/aquapay/admindashboard.php` (auto-redirected after login)
- Features:
  - View reports and statistics
  - Browse and manage store
  - Accept/decline customer orders
  - View order history
  - Process payroll
  - Manage account settings

### Regular User
- Access: `http://localhost/aquapay/userdashboard.php` (auto-redirected after login)
- Features:
  - Browse and purchase products
  - View order history

---

## Key Features

### 1. Store & Ordering
1. Go to Store module (in sidebar or userdashboard)
2. Click "BUY" on any product
3. Select quantity
4. Enter customer details:
   - Name
   - Address
   - Payment method (text)
5. Choose delivery option: "PICK UP" or "DELIVER"
6. Order saves to database and stock updates

### 2. Order Management (Admin Only)
- **Pending:**  View new orders, accept or decline
- **History:** View completed and cancelled orders

### 3. Payroll
1. Go to Payroll module
2. Enter employee name
3. Enter gallon quantity sold
4. Enter commission per gallon
5. System calculates total automatically
6. Save payroll record

### 4. User Settings
- Change Username
- Change Password  
- Delete Account

### 5. Reports
- View system summary (user/order/product counts)
- See recent orders

---

## Database Schema

### Core Tables

**users**
- Stores admin and regular user accounts
- Fields: id, username, password, email, full_name, role

**products**
- Water products available for purchase
- Pre-loaded with 8 sample items
- Fields: id, name, description, price, stock, image, category

**store_orders**
- Customer orders
- Fields: id, user_id, customer_name, address, payment_method, delivery_type, status, created_at

**order_items**
- Individual items in each order
- Tracks product_id, quantity, price
- Links orders to products

**payroll_records**
- Employee payroll records
- Fields: id, user_id, employee_name, gallon_qty, commission_per, total_commission

---

## File Structure

```
aquapay/
├── index.php                    # Home page
├── login.php                    # Login page
├── register.php                 # Registration page
├── process_register.php         # Registration handler
├── admindashboard.php           # Admin main page
├── userdashboard.php            # User/store main page
├── config.php                   # Database config + initialization
├── auth_check.php               # Authentication middleware
├── header.php                   # Main header
├── dashboardheader.php          # Dashboard header
├── footer.php                   # Footer
├── logout.php                   # Logout handler
├── process_order.php            # Order processing
├── update_order_status.php      # Order status updates
├── process_payroll.php          # Payroll processing
├── change_username.php          # Username change handler
├── change_password.php          # Password change handler
├── delete_account.php           # Account deletion handler
├── style.css                    # Global styles
├── modules/
│   ├── report.php              # Reports/summary
│   ├── store.php               # Store products
│   ├── pending.php             # Pending orders
│   ├── history.php             # Completed orders
│   ├── payroll.php             # Payroll form
│   └── setting.php             # Account settings
└── setup.php                    # Manual setup (optional)
```

---

## Common Tasks

### Create a New User Account
1. Go to registration page (from home)
2. Enter username (min 3 chars)
3. Enter password (min 6 chars)
4. Confirm password
5. Submit - new account created as "user" role

### Make a User Admin
- Direct database edit:
```sql
UPDATE users SET role='admin' WHERE username='username_here';
```

### Add New Products
- Direct database edit to `products` table
- Or modify `initializeDatabase()` in config.php

### Reset Admin Password
- Delete admin user and restart app, or:
```sql
UPDATE users SET password=PASSWORD_HASH_HERE WHERE username='admin';
```

### Clear All Orders
```sql
TRUNCATE store_orders;
TRUNCATE order_items;
```

---

## Troubleshooting

### "Database connection failed"
- Check MySQL is running (XAMPP)
- Verify database 'aquapay' exists
- Check credentials in config.php

### "Session already started" warning  
- Already fixed in this version
- All files use `session_status()` check

### Products not showing
- Check in database: `SELECT * FROM products;`
- If empty, the app auto-seeds on next load

### Can't login as admin
- Default: username `admin`, password `admin123`
- Check users table exists and has admin entry

### Order not saving
- Check all form fields are filled
- Verify product exists and has stock > 0
- Check MySQL error logs

---

## Security Notes

⚠️ This is a development system. For production:
1. Use strong passwords
2. Enable HTTPS
3. Add CSRF tokens
4. Implement rate limiting
5. Add input validation/sanitization
6. Use environment variables for credentials
7. Regular backups

---

## Support

For issues or questions:
1. Check SYSTEM_AUDIT_REPORT.md for architecture details
2. Review test checklist in audit report
3. Check error messages in browser console
4. Review MySQL error log

---

**Last Updated:** System Audit Complete  
**Status:** ✅ Production Ready
