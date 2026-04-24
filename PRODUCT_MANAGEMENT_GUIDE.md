# Product Management Feature - Admin Guide

## Overview
Admins can now fully manage products in the AQUAPAY system including editing all product values and uploading product images.

---

## Features

### 1. Product List
- View all products with thumbnail images
- See product name, price, stock, category, and description
- Quick action buttons (Edit, Delete)

### 2. Add New Product
- Create new products from scratch
- Set product name, description, price, stock, category
- Upload product image (JPEG, PNG, GIF, WebP)

### 3. Edit Product
- Modify any product field (name, description, price, stock, category)
- Update product image or keep existing one
- All changes saved to database

### 4. Delete Product
- Remove products from catalog
- Associated images are automatically deleted
- Cannot undo (optional confirmation added)

---

## Access

**Location:** Admin Dashboard → Products menu

**Requirements:**
- Must be logged in as an admin user
- Role must be set to 'admin' in database

---

## How to Use

### Add New Product

1. Click **Admin Dashboard** → **Products** (in sidebar)
2. Click **+ Add New Product** button
3. Fill in the following fields:
   - **Product Name** (required) - e.g., "Water Jug 20L"
   - **Description** (optional) - product details
   - **Price (PHP)** (required) - selling price
   - **Stock Quantity** (required) - available units
   - **Category** (optional) - e.g., "containers", "bottles"
   - **Product Image** (optional) - click to upload JPEG/PNG/GIF/WebP
4. Click **Save Product**
5. Success message appears, product added to list

### Edit Product

1. Go to **Products** module
2. Find the product in the list
3. Click **Edit** button
4. Modify any fields:
   - Name, description, price, stock, category
   - Current image displays
   - Upload new image to replace (or leave blank to keep current)
5. Click **Update Product**
6. Success message appears, changes saved

### Delete Product

1. Go to **Products** module
2. Find the product in the list
3. Click **Delete** button
4. Confirm when prompted
5. Product and image removed from system

---

## Image Upload Details

### Supported Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)

### File Size
- No specific limit set (configure in php.ini if needed)
- Recommended: Keep under 5MB per image

### Storage Location
- Images saved to: `/uploads/products/`
- Auto-renamed to prevent conflicts: `product_[timestamp]_[hash].ext`
- Old images deleted when replaced

### Security
- Only image files allowed in uploads directory
- PHP execution disabled (.htaccess protection)
- Directory listing disabled

---

## Database Fields

Each product stores:
- **id** - Unique product ID
- **name** - Product name
- **description** - Product details
- **price** - Selling price (DECIMAL 10,2)
- **stock** - Available quantity (INT)
- **image** - Path to image file
- **category** - Product category
- **created_at** - Date created
- **updated_at** - Last modified date

---

## Usage in Store

Products added/edited here appear in:
- **Admin Store Module** - preview all products
- **User Dashboard** - customers see and can purchase
- **Inventory** - stock managed here

When customers order:
1. Stock automatically decreases
2. Order items record price at purchase time
3. Out-of-stock items don't show to customers

---

## Tips & Best Practices

### Image Requirements
- Use clear, high-quality images
- 300x300px or larger recommended
- Square aspect ratio works best (1:1)
- Include product from multiple angles if possible

### Product Information
- Keep names concise but descriptive
- Include size/volume in name if applicable
- Add detailed description for customer reference
- Use consistent category names

### Stock Management
- Update stock after orders are completed
- Set to 0 to hide product temporarily
- Monitor low stock items regularly

### Pricing
- Include PHP symbol in customer-facing display
- Set competitive prices
- Consider discounts (add later if needed)

---

## Troubleshooting

### Image Upload Fails
- Check file format (JPEG, PNG, GIF, WebP only)
- Verify file isn't corrupted
- Ensure `/uploads/products/` directory is writable
- Check server disk space

### Changes Not Saved
- Verify all required fields filled (name, price, stock)
- Check for database connection errors
- Use valid numeric values for price and stock

### Images Not Displaying
- Verify image was uploaded (check edit form)
- Check file path is correct
- Ensure image file still exists in `/uploads/products/`
- Clear browser cache (Ctrl+F5)

### Can't Access Products Module
- Verify logged in as admin user
- Check user role in database is 'admin'
- Ensure not accessing as regular user

---

## Database Queries

### View All Products
```sql
SELECT * FROM products ORDER BY name ASC;
```

### Find Product by ID
```sql
SELECT * FROM products WHERE id = 5;
```

### Update Product Stock
```sql
UPDATE products SET stock = 50 WHERE name = 'Water Jug 20L';
```

### List Low Stock Items
```sql
SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC;
```

### Get Product Count
```sql
SELECT COUNT(*) as total FROM products;
```

---

## File Locations

- **Module Code:** `/modules/products.php`
- **Save Handler:** `/save_product.php`
- **Delete Handler:** `/delete_product.php`
- **Upload Directory:** `/uploads/products/`
- **Styles:** `/style.css` (look for "Product Management Styles")

---

## Related Features

- **Store Module** - View how products appear to customers
- **Order Management** - Track orders and adjust stock
- **Reports** - See product count statistics
- **User Dashboard** - Check how customers see products

---

**Last Updated:** March 1, 2026  
**Feature Status:** ✅ Active and Functional
