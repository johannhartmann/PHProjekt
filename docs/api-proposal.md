# API Enhancement Proposal for React Migration

**Date:** 2025-11-16
**Status:** Proposed
**Purpose:** Enhance PHProjekt backend with new JSON-only API endpoints for React frontend

---

## Executive Summary

This document proposes minimal backend additions to support the React migration without modifying existing Dojo endpoints. All new endpoints will be JSON-only and follow RESTful conventions.

**Key Principles:**
- ✅ **Non-breaking:** Existing Dojo endpoints remain unchanged
- ✅ **JSON-only:** New endpoints return structured JSON, not HTML
- ✅ **RESTful:** Follow REST best practices (proper HTTP methods, status codes)
- ✅ **Documented:** Clear TypeScript types + JSDoc comments

---

## Current State Analysis

### ✅ Well-Supported Endpoints (No Changes Needed)

These endpoints already return clean JSON and are ready for React:

**Timecard Module:**
- `GET /Timecard/index/jsonDayList` - Day bookings ✅
- `GET /Timecard/index/jsonGetFavoritesProjects` - Favorites ✅
- `GET /Timecard/index/jsonGetRunningBookings` - Running timer ✅
- `POST /Timecard/index/jsonSave` - Save booking ✅

**Project Module:**
- `GET /Project/index/jsonList` - Project list ✅
- `GET /Project/index/jsonTree` - Project tree ✅
- `GET /Project/index/jsonDetail` - Project details ✅
- `POST /Project/index/jsonSave` - Save project ✅

**Cross-Module:**
- `GET /Default/Tag/jsonGetTags` - All tags ✅
- `GET /Default/Search/jsonSearch` - Full-text search ✅
- `GET /Default/index/jsonGetConfigurations` - System config ✅

---

## Proposed New Endpoints

### 1. RESTful API Prefix (Optional but Recommended)

**Rationale:** Create a dedicated `/api/` namespace for React-only endpoints, making it clear which endpoints are modern JSON-only APIs.

**Proposed Structure:**
```
/api/v1/timecard/bookings/:id
/api/v1/projects/:id
/api/v1/tags
```

**Benefits:**
- Clear separation between legacy Dojo and modern React APIs
- Easier to apply middleware (CORS, rate limiting, versioning)
- Better documentation structure

**Implementation:**
```php
// phprojekt/application/Api/config/module.config.php
return [
    'router' => [
        'routes' => [
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/v1/:resource[/:id]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Api\Controller',
                        'controller' => 'IndexController',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
];
```

**Status:** ⚠️ **Optional** - Can use existing routes with `/json` prefix

---

### 2. Authentication and Session Endpoints

**Current Gap:** No JSON endpoint to check authentication status or get current user info.

#### 2.1 Get Current User

**Endpoint:** `GET /api/v1/user/current` (or `/Default/User/jsonGetCurrent`)

**Response:**
```json
{
  "userId": 1,
  "username": "admin",
  "firstname": "John",
  "lastname": "Doe",
  "email": "admin@example.com",
  "isAuthenticated": true,
  "admin": true,
  "timezone": "Europe/Berlin",
  "language": "en"
}
```

**Use Case:** React app needs to display current user in navigation, check permissions.

**Priority:** 🔴 **HIGH**

---

#### 2.2 Login/Logout (JSON)

**Current:** Login returns HTML redirect, not suitable for SPA.

**Proposed Endpoints:**

**Login:**
```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "password123"
}
```

**Response (Success):**
```json
{
  "type": "success",
  "message": "Login successful",
  "user": {
    "userId": 1,
    "username": "admin",
    "firstname": "John",
    "lastname": "Doe",
    "admin": true
  }
}
```

**Response (Failure):**
```json
{
  "type": "error",
  "message": "Invalid username or password",
  "code": "INVALID_CREDENTIALS"
}
```

**Logout:**
```http
POST /api/v1/auth/logout
```

**Response:**
```json
{
  "type": "success",
  "message": "Logged out successfully"
}
```

**Priority:** 🔴 **HIGH**

---

### 3. Metadata and Form Schema Endpoints

**Current Gap:** React needs to know field types, validations, labels dynamically.

#### 3.1 Module Metadata (Enhanced)

**Endpoint:** `GET /api/v1/:module/metadata`

**Response:**
```json
{
  "module": "Project",
  "fields": [
    {
      "key": "title",
      "label": "Project Title",
      "type": "text",
      "required": true,
      "maxLength": 255,
      "validation": "^[\\w\\s]+$",
      "hint": "Enter a descriptive project name"
    },
    {
      "key": "startDate",
      "label": "Start Date",
      "type": "date",
      "required": false,
      "format": "YYYY-MM-DD"
    },
    {
      "key": "priority",
      "label": "Priority",
      "type": "select",
      "required": true,
      "options": [
        { "value": 1, "label": "Low" },
        { "value": 5, "label": "Medium" },
        { "value": 10, "label": "High" }
      ]
    }
  ],
  "permissions": {
    "create": true,
    "read": true,
    "update": true,
    "delete": false
  }
}
```

**Use Case:** React forms can render dynamically based on backend schema.

**Priority:** 🟡 **MEDIUM**

---

### 4. Batch Operations

**Current Gap:** No endpoints for batch operations (e.g., delete multiple items).

#### 4.1 Batch Delete

**Endpoint:** `POST /api/v1/:module/batch-delete`

**Request:**
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

**Response:**
```json
{
  "type": "success",
  "message": "Deleted 5 items",
  "deleted": [1, 2, 3, 4, 5],
  "failed": []
}
```

**Priority:** 🟢 **LOW** (can defer)

---

### 5. File Upload (JSON Response)

**Current Gap:** File upload endpoints return HTML forms, not JSON.

#### 5.1 File Upload with JSON Response

**Endpoint:** `POST /api/v1/files/upload`

**Request:**
```http
POST /api/v1/files/upload
Content-Type: multipart/form-data

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="document.pdf"
Content-Type: application/pdf

<binary data>
------WebKitFormBoundary
Content-Disposition: form-data; name="moduleName"

Project
------WebKitFormBoundary
Content-Disposition: form-data; name="itemId"

123
------WebKitFormBoundary--
```

**Response:**
```json
{
  "type": "success",
  "message": "File uploaded successfully",
  "file": {
    "id": 456,
    "filename": "document.pdf",
    "size": 102400,
    "mimeType": "application/pdf",
    "url": "/files/456/document.pdf",
    "uploadedAt": "2025-11-16T14:30:00Z"
  }
}
```

**Priority:** 🟡 **MEDIUM**

---

### 6. Pagination and Filtering

**Current Gap:** Some list endpoints don't support pagination/filtering.

#### 6.1 Enhanced Project List

**Endpoint:** `GET /api/v1/projects`

**Query Parameters:**
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 50, max: 100)
- `sort` - Sort field (e.g., `title`, `-createdAt` for desc)
- `filter` - Filter expression (e.g., `status=active`)
- `search` - Full-text search

**Example:**
```http
GET /api/v1/projects?page=2&limit=20&sort=-createdAt&filter=status=active&search=migration
```

**Response:**
```json
{
  "data": [/* ... projects ... */],
  "pagination": {
    "page": 2,
    "limit": 20,
    "total": 150,
    "totalPages": 8,
    "hasNext": true,
    "hasPrev": true
  },
  "filters": {
    "status": "active",
    "search": "migration"
  }
}
```

**Priority:** 🟡 **MEDIUM**

---

## Implementation Plan

### Phase 1: Essential APIs (Week 1)

**Priority:** 🔴 **HIGH**

1. **User Authentication:**
   - `GET /api/v1/user/current` - Get current user
   - `POST /api/v1/auth/login` - Login (JSON)
   - `POST /api/v1/auth/logout` - Logout (JSON)

2. **CSRF Token:**
   - Ensure CSRF token available in meta tag or config endpoint

**Files to Create:**
- `phprojekt/application/Api/Controller/UserController.php`
- `phprojekt/application/Api/Controller/AuthController.php`

---

### Phase 2: Metadata and Forms (Week 2)

**Priority:** 🟡 **MEDIUM**

1. **Module Metadata:**
   - `GET /api/v1/:module/metadata` - Field schema + permissions

2. **Enhanced Responses:**
   - Add pagination to existing list endpoints
   - Add filtering support

**Files to Modify:**
- `phprojekt/application/Default/Controller/IndexController.php` - Add pagination helpers

---

### Phase 3: File Upload and Batch (Week 3+)

**Priority:** 🟢 **LOW**

1. **File Operations:**
   - `POST /api/v1/files/upload` - Upload with JSON response
   - `DELETE /api/v1/files/:id` - Delete file

2. **Batch Operations:**
   - `POST /api/v1/:module/batch-delete` - Bulk delete

---

## Alternative: Keep Existing Endpoints

**If minimizing backend changes is critical:**

1. **Use existing `/json*` endpoints** - They already return JSON
2. **Add thin wrapper in React** - Transform responses to expected format
3. **Handle edge cases client-side** - e.g., authentication via cookies

**Pros:**
- ✅ Zero backend changes
- ✅ Faster React migration

**Cons:**
- ❌ Less clean API
- ❌ Harder to document
- ❌ Couples React to legacy structure

---

## Recommendation

**Start with existing endpoints first**, then add new ones as needed:

1. ✅ **Phase 1:** Use existing `/json*` endpoints (95% coverage)
2. 🟡 **Phase 2:** Add authentication endpoints (critical gap)
3. 🟢 **Phase 3:** Add metadata/batch endpoints (nice-to-have)

**This approach:**
- Minimizes backend work
- Lets React team move fast
- Allows iterative improvements

---

## TypeScript Integration Example

After implementing new endpoints, update React types:

```typescript
// frontend-react/src/api/types.ts

/**
 * Current user info
 * Endpoint: GET /api/v1/user/current
 */
export interface CurrentUser {
  userId: number;
  username: string;
  firstname: string;
  lastname: string;
  email: string;
  isAuthenticated: boolean;
  admin: boolean;
  timezone: string;
  language: string;
}

// frontend-react/src/api/client.ts

export const authApi = {
  /**
   * Get current user
   */
  async getCurrentUser(): Promise<CurrentUser> {
    return get<CurrentUser>(`/api/v1/user/current`);
  },

  /**
   * Login
   */
  async login(username: string, password: string): Promise<ApiResponse<CurrentUser>> {
    return post<ApiResponse<CurrentUser>>(`/api/v1/auth/login`, {
      username,
      password,
    });
  },

  /**
   * Logout
   */
  async logout(): Promise<ApiResponse> {
    return post<ApiResponse>(`/api/v1/auth/logout`);
  },
};
```

---

## Next Steps

1. **Review this proposal** with backend team
2. **Prioritize endpoints** based on React migration roadmap
3. **Create tickets** for each endpoint group
4. **Implement incrementally** - start with authentication
5. **Document as you go** - Update TypeScript types + JSDoc

---

**Questions or Feedback:** Contact development team

**Document Version:** 1.0
**Last Updated:** 2025-11-16
