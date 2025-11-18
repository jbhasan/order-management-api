# Postman Collection Setup Guide

This guide will help you import and use the Postman collection for the E-Commerce Order Management API.

## Import Collection

1. Open Postman
2. Click **Import** button (top left)
3. Select the `postman_collection.json` file
4. The collection will be imported with all endpoints organized in folders

## Import Environment

1. In Postman, click the **Environments** tab (left sidebar)
2. Click **Import**
3. Select the `postman_environment.json` file
4. Select the imported environment from the dropdown (top right)

## Configuration

### Base URL

The default base URL is set to `http://127.0.0.1:8000`. To change it:

1. Select the environment from the dropdown
2. Click the **eye icon** next to the environment name
3. Edit the `base_url` variable
4. For production, change it to your production URL

### Authentication Flow

1. **Register or Login**:

    - Use the `Register` or `Login` endpoint
    - The access token and refresh token will be automatically saved to environment variables
    - The Login endpoint has a test script that saves tokens automatically

2. **Using Tokens**:

    - All protected endpoints use `Bearer {{access_token}}` in the Authorization header
    - Tokens are automatically included from environment variables

3. **Refresh Token**:
    - Use the `Refresh Token` endpoint when your access token expires
    - Update the `access_token` variable manually after refresh

## API Endpoints Overview

### Authentication

-   `POST /api/v1/register` - Register new user
-   `POST /api/v1/login` - Login user (auto-saves tokens)
-   `POST /api/v1/refresh` - Refresh access token
-   `POST /api/v1/logout` - Logout user
-   `GET /api/v1/me` - Get authenticated user

### Products

-   `GET /api/v1/products` - List products (paginated)
-   `POST /api/v1/products` - Create product (Admin/Vendor)
-   `GET /api/v1/products/{id}` - Get product details
-   `PUT /api/v1/products/{id}` - Update product (Admin/Vendor)
-   `DELETE /api/v1/products/{id}` - Delete product (Admin/Vendor)
-   `GET /api/v1/products/search?q={query}` - Search products
-   `POST /api/v1/products/bulk-import` - Bulk import from CSV (Admin/Vendor)

### Orders

-   `GET /api/v1/orders` - List orders (filtered by role)
-   `POST /api/v1/orders` - Create order
-   `GET /api/v1/orders/{id}` - Get order details
-   `POST /api/v1/orders/{id}/confirm` - Confirm order (Admin/Vendor)
-   `POST /api/v1/orders/{id}/cancel` - Cancel order
-   `PUT /api/v1/orders/{id}/status` - Update order status (Admin/Vendor)

### Inventory

-   `GET /api/v1/inventory` - List inventory items (Admin/Vendor)
-   `PUT /api/v1/inventory/{productId}` - Update inventory (Admin/Vendor)
-   `GET /api/v1/inventory/low-stock` - Get low stock items (Admin/Vendor)

## Role-Based Access

### Admin

-   Full access to all endpoints
-   Can manage all products and orders
-   Can access inventory management

### Vendor

-   Can manage own products
-   Can view and manage own orders
-   Can access inventory for own products
-   Can bulk import products

### Customer

-   Can view products
-   Can create and view own orders
-   Can cancel own orders
-   Cannot access inventory or product management

## Testing Workflow

1. **Start the API server**:

    ```bash
    php artisan serve
    ```

2. **Register a test user**:

    - Use the Register endpoint
    - Or use Login if user already exists

3. **Create a product** (Admin/Vendor):

    - Use Create Product endpoint
    - Note the product ID for later use

4. **Create an order**:

    - Use Create Order endpoint
    - Include product IDs in the items array

5. **Manage order**:
    - Confirm order (Admin/Vendor)
    - Update status (Admin/Vendor)
    - Cancel order (if needed)

## Example Requests

### Register User

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer"
}
```

### Create Product

```json
{
    "name": "Sample Product",
    "description": "Product description",
    "sku": "PROD-001",
    "price": 99.99,
    "quantity": 100,
    "low_stock_threshold": 10
}
```

### Create Order

```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ],
    "shipping_address": "123 Main St, City, State 12345",
    "billing_address": "123 Main St, City, State 12345",
    "tax": 10.0,
    "shipping_cost": 5.0
}
```

## Troubleshooting

### 401 Unauthorized

-   Check if access token is set in environment variables
-   Try logging in again to get a new token
-   Verify the token hasn't expired

### 403 Forbidden

-   Check user role (Admin/Vendor required for some endpoints)
-   Verify you have permission for the requested resource

### 422 Validation Error

-   Check request body format
-   Verify all required fields are present
-   Check data types match expected format

### 500 Server Error

-   Check server logs
-   Verify database migrations are run
-   Ensure queue worker is running for async jobs

## Notes

-   The Login endpoint automatically saves tokens to environment variables
-   All protected endpoints require `Bearer {{access_token}}` header
-   Some endpoints require specific roles (Admin/Vendor)
-   Bulk import requires a CSV file upload
-   Order status values: `pending`, `processing`, `shipped`, `delivered`, `cancelled`
