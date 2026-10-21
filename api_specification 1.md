# Fablead ERP Software - Full Database Synchronization REST API Specification

This document defines the **Full Database Synchronization API** specification for **Fablead Technolab ERP Software**.
Instead of sending single item requests, the desktop app syncs the entire database snapshot in a single unified API call.

---

## 1. General Conventions & Headers

- **Base URL**: `https://your-domain.com/api/v1`
- **Headers**:
  ```http
  Content-Type: application/json
  Accept: application/json
  Authorization: Bearer <access_token>
  ```

---

## 2. Full Database Push API (Upload Full Local DB to Server)

When the desktop app clicks **"Sync Database"** or comes back online, it uploads the entire local database dataset in a single JSON payload.

- **Endpoint**: `POST /sync/full-push`
- **Content-Type**: `application/json`
- **Request Body**:
  ```json
  {
    "deviceId": "DESKTOP-MAC-01",
    "timestamp": "2026-08-11T13:05:00Z",
    "categories": [
      { "id": 1, "name": "Mobiles", "isDeleted": false },
      { "id": 2, "name": "Electronics", "isDeleted": false }
    ],
    "brands": [
      { "id": 1, "name": "Apple", "isDeleted": false },
      { "id": 2, "name": "Samsung", "isDeleted": false }
    ],
    "units": [
      { "id": 1, "unitName": "Pcs", "isDelete": false },
      { "id": 2, "unitName": "Kg", "isDelete": false }
    ],
    "products": [
      {
        "id": 101,
        "name": "iPhone 13",
        "sku": "IP13-128",
        "categoryId": 1,
        "category": "Mobiles",
        "brandId": 1,
        "brand": "Apple",
        "unitId": 1,
        "unit": "Pcs",
        "hsnCode": "85171200",
        "barcode": "890123456789",
        "price": 50000.00,
        "purchasePrice": 42000.00,
        "quantity": 15.0,
        "status": "active",
        "availability": "in_stock",
        "description": "Apple iPhone 13 128GB",
        "itemType": "Normal Product",
        "isRentAvailable": false,
        "isDeleted": false
      }
    ],
    "productVariants": [
      {
        "id": 501,
        "productId": 101,
        "size": "128GB",
        "color": "Midnight",
        "qty": 15,
        "price": 50000.00,
        "purchasePrice": 42000.00,
        "barcode": "890123456790"
      }
    ],
    "vendors": [],
    "purchases": [],
    "sales": []
  }
  ```

- **Response (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Full database synced successfully",
    "syncedAt": "2026-08-11T13:05:01Z",
    "recordsProcessed": {
      "categories": 2,
      "brands": 2,
      "units": 2,
      "products": 1,
      "productVariants": 1,
      "vendors": 0,
      "purchases": 0,
      "sales": 0
    }
  }
  ```

---

## 3. Full Database Pull API (Download Entire Server DB to Local App)

The desktop app calls this endpoint to download the latest state of all tables from the server in one request:

- **Endpoint**: `GET /sync/full-pull`
- **Response (200 OK)**:
  ```json
  {
    "success": true,
    "syncedAt": "2026-08-11T13:05:01Z",
    "data": {
      "categories": [
        { "id": 1, "name": "Mobiles", "isDeleted": false }
      ],
      "brands": [
        { "id": 1, "name": "Apple", "isDeleted": false }
      ],
      "units": [
        { "id": 1, "unitName": "Pcs", "isDelete": false }
      ],
      "products": [
        {
          "id": 101,
          "name": "iPhone 13",
          "sku": "IP13-128",
          "categoryId": 1,
          "category": "Mobiles",
          "brandId": 1,
          "brand": "Apple",
          "unitId": 1,
          "unit": "Pcs",
          "hsnCode": "85171200",
          "barcode": "890123456789",
          "price": 50000.00,
          "purchasePrice": 42000.00,
          "quantity": 15.0,
          "status": "active",
          "availability": "in_stock",
          "description": "Apple iPhone 13 128GB",
          "imageUrl": "https://your-domain.com/uploads/products/iphone13.jpg",
          "itemType": "Normal Product",
          "isRentAvailable": false,
          "isDeleted": false,
          "variants": [
            {
              "id": 501,
              "productId": 101,
              "size": "128GB",
              "color": "Midnight",
              "qty": 15,
              "price": 50000.00,
              "purchasePrice": 42000.00,
              "barcode": "890123456790"
            }
          ]
        }
      ],
      "vendors": [],
      "purchases": [],
      "sales": []
    }
  }
  ```

---

## 4. Full Database Image Sync API (Batch Image Upload)

For product photos, images are uploaded in a batch archive or multipart endpoint:

- **Endpoint**: `POST /sync/images-batch`
- **Content-Type**: `multipart/form-data`
- **Form Fields**:
  - `images[]`: Multiple image files
  - `mapping`: JSON string mapping product ID to image file name:
    ```json
    { "101": "iphone13.jpg" }
    ```
