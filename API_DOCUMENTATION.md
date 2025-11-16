# API Documentation - ReBox Backend

## Base URL
```
http://192.168.1.28:8000/api
```

## Response Format

Semua API response menggunakan format konsisten:

### Success Response
```json
{
    "success": true,
    "message": "Optional message",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... } // Optional validation errors
}
```

---

## Authentication

### Register
**POST** `/register`

**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "user": { ... },
        "token": "1|xxxxx..."
    }
}
```

### Login
**POST** `/login`

**Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": { ... },
        "token": "1|xxxxx..."
    }
}
```

### Logout
**POST** `/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### Get Current User
**GET** `/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        ...
    }
}
```

---

## Boxes

**All box endpoints require authentication**

### Get All Boxes
**GET** `/boxes`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Box Dapur",
            "description": "Peralatan dapur",
            "user_id": 1,
            "created_at": "...",
            "updated_at": "...",
            "items": [...]
        }
    ]
}
```

### Get Single Box
**GET** `/boxes/{id}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Box Dapur",
        ...
        "items": [...]
    }
}
```

### Create Box
**POST** `/boxes`

**Body:**
```json
{
    "name": "Box Kamar",
    "description": "Barang kamar tidur",
    "location": "Lantai 2",
    "color": "#FF5733"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Box berhasil dibuat",
    "data": { ... }
}
```

### Update Box
**PUT** `/boxes/{id}`

**Body:**
```json
{
    "name": "Box Kamar Updated",
    "description": "Updated description"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Box berhasil diupdate",
    "data": { ... }
}
```

### Delete Box
**DELETE** `/boxes/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Box berhasil dihapus"
}
```

---

## Items

**All item endpoints require authentication**

### Get All Items
**GET** `/items`

**Query Parameters:**
- `box_id` (optional) - Filter by box
- `category_id` (optional) - Filter by category
- `search` (optional) - Search by name

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Panci",
            "description": "Panci besar",
            "box_id": 1,
            "category_id": 2,
            "quantity": 3,
            "box": { ... },
            "category": { ... }
        }
    ]
}
```

### Get Single Item
**GET** `/items/{id}`

**Response (200):**
```json
{
    "success": true,
    "data": { ... }
}
```

### Create Item
**POST** `/items`

**Body:**
```json
{
    "box_id": 1,
    "category_id": 2,
    "name": "Sendok",
    "description": "Sendok makan",
    "quantity": 10,
    "image": "file" // optional, multipart/form-data
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Item created successfully",
    "data": { ... }
}
```

### Update Item
**PUT** `/items/{id}`

**Body:**
```json
{
    "name": "Sendok Updated",
    "quantity": 12
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": { ... }
}
```

### Delete Item
**DELETE** `/items/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Item deleted successfully"
}
```

---

## Categories

### Get All Categories
**GET** `/categories`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Elektronik",
            "description": "Barang elektronik",
            "items_count": 5
        }
    ]
}
```

### Get Single Category
**GET** `/categories/{id}`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Elektronik",
        "items": [...]
    }
}
```

### Create Category
**POST** `/categories`

**Body:**
```json
{
    "name": "Furniture",
    "description": "Perabot rumah tangga",
    "icon": "chair"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Category created successfully",
    "data": { ... }
}
```

### Update Category
**PUT** `/categories/{id}`

**Body:**
```json
{
    "name": "Furniture Updated",
    "description": "Updated description"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Category updated successfully",
    "data": { ... }
}
```

### Delete Category
**DELETE** `/categories/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Category deleted successfully"
}
```

**Error (400)** - If category has items:
```json
{
    "success": false,
    "message": "Cannot delete category with existing items"
}
```

---

## Error Codes

- **200** - OK
- **201** - Created
- **400** - Bad Request
- **401** - Unauthenticated
- **403** - Unauthorized (forbidden)
- **404** - Not Found
- **422** - Validation Error

## Validation Errors Example

**Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

---

## Notes

1. **Authentication**: Semua endpoint kecuali `/register` dan `/login` memerlukan Bearer Token
2. **CORS**: Sudah dikonfigurasi untuk allow all origins
3. **Rate Limiting**: Default Laravel rate limiting apply
4. **Image Upload**: Endpoint `/items` support multipart/form-data untuk upload gambar
