# Customer Authentication API (v1)

This document outlines the authentication flow for the storefront Customer. All requests should use the `Accept: application/json` header.

## Base URL

`/api/v1`

---

## 1. Register Customer

Create a new customer account and receive an access token.

- **Method**: `POST`
- **Endpoint**: `/register`
- **Auth Required**: No

### Request Body

| Field                   | Type   | Required | Description                         |
| :---------------------- | :----- | :------- | :---------------------------------- |
| `name`                  | string | Yes      | Full name of the customer.          |
| `email`                 | string | Yes      | Unique email address.               |
| `phone`                 | string | No       | Contact phone number.               |
| `date_of_birth`         | date   | No       | Format: `YYYY-MM-DD`.               |
| `gender`                | string | No       | Options: `male`, `female`, `other`. |
| `password`              | string | Yes      | Minimum 8 characters.               |
| `password_confirmation` | string | Yes      | Must match `password`.              |

### Success Response `201 Created`

```json
{
    "access_token": "1|AbCdeFGhiJkLmNoP...",
    "token_type": "Bearer",
    "customer": {
        "id": "uuid-string",
        "name": "John Doe",
        "email": "john@example.com",
        "phone": null,
        "date_of_birth": null,
        "gender": null,
        "is_active": true,
        "created_at": "2026-03-31T10:00:00.000000Z",
        "updated_at": "2026-03-31T10:00:00.000000Z"
    }
}
```

---

## 2. Login Customer

Authenticate an existing customer and receive an access token.

- **Method**: `POST`
- **Endpoint**: `/login`
- **Auth Required**: No

### Request Body

| Field      | Type   | Required | Description               |
| :--------- | :----- | :------- | :------------------------ |
| `email`    | string | Yes      | Registered email address. |
| `password` | string | Yes      |                           |

### Success Response `200 OK`

Returns the same structure as the Register response.

### Error Response `422 Unprocessable Entity`

Returned for invalid credentials or validation errors.

---

## 3. Get Customer Profile

Fetch the information of the currently authenticated customer.

- **Method**: `GET`
- **Endpoint**: `/customer`
- **Auth Required**: Yes (`Bearer Token`)

### Success Response `200 OK`

Returns the customer object.

---

## 4. Logout Customer

Revoke the current access token.

- **Method**: `POST`
- **Endpoint**: `/logout`
- **Auth Required**: Yes (`Bearer Token`)

### Success Response `200 OK`

```json
{
    "message": "Successfully logged out."
}
```

---

## Handling Authentication in Frontend

Once you receive the `access_token`, you must include it in the `Authorization` header for all protected requests:

```http
Authorization: Bearer <your_access_token>
```
