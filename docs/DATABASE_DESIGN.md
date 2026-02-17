# Diseño de Base de Datos - Sistema de Gestión de Pedidos (Mini ERP)

## Diagrama Entidad-Relación (Conceptual)

```
┌─────────────┐       ┌─────────────────┐       ┌─────────────┐
│   roles     │       │  role_user      │       │   users     │
├─────────────┤       ├─────────────────┤       ├─────────────┤
│ id (PK)     │◄──────│ role_id (FK)    │       │ id (PK)     │
│ name        │       │ user_id (FK) ───│──────►│ name        │
│ created_at  │       └─────────────────┘       │ email       │
│ updated_at  │                                 │ password    │
└─────────────┘                                 │ created_at  │
                                                │ updated_at  │
                                                │ deleted_at  │
                                                └──────┬──────┘
                                                       │
┌─────────────┐       ┌─────────────────┐              │
│  clients    │       │    orders       │              │
├─────────────┤       ├─────────────────┤              │
│ id (PK)     │◄──────│ id (PK)         │              │
│ name        │       │ client_id (FK)──│              │
│ email       │       │ user_id (FK) ───│──────────────┘
│ phone       │       │ order_date      │
│ address     │       │ status          │
│ created_at  │       │ total           │
│ updated_at  │       │ notes           │
│ deleted_at  │       │ created_at      │
└─────────────┘       │ updated_at      │       ┌─────────────┐
                      │ deleted_at      │       │  products   │
                      └────────┬────────┘       ├─────────────┤
                               │                │ id (PK)     │
                               │                │ name        │
                               │                │ sku         │
                               │                │ description │
                               │                │ price       │
                               │                │ stock       │
                               │                │ created_at  │
                               │                │ updated_at  │
                               │                │ deleted_at  │
                               │                └──────┬──────┘
                               │                       │
                               ▼                       │
                      ┌─────────────────┐              │
                      │ order_product   │              │
                      ├─────────────────┤              │
                      │ id (PK)         │              │
                      │ order_id (FK) ──│──────────────┤
                      │ product_id (FK)─│──────────────┘
                      │ quantity        │
                      │ price           │
                      │ created_at      │
                      │ updated_at      │
                      └─────────────────┘
```

---

## 1. Tabla: `users` (existente - modificaciones)

| Columna        | Tipo         | Constraints         | Descripción                    |
|----------------|--------------|---------------------|--------------------------------|
| id             | BIGINT UNSIGNED | PK, AI            | Identificador único            |
| name           | VARCHAR(255) | NOT NULL            | Nombre completo                |
| email          | VARCHAR(255) | UNIQUE, NOT NULL    | Email (login)                  |
| email_verified_at | TIMESTAMP | NULLABLE            | Verificación de email          |
| password       | VARCHAR(255) | NOT NULL            | Contraseña hasheada             |
| remember_token | VARCHAR(100) | NULLABLE            | Token de "recordarme"           |
| created_at     | TIMESTAMP    | NULLABLE            | Fecha de creación              |
| updated_at     | TIMESTAMP    | NULLABLE            | Fecha de actualización         |
| deleted_at     | TIMESTAMP    | NULLABLE            | Soft delete                    |

**Relaciones:**
- `belongsToMany(Role)` → roles
- `hasMany(Order)` → pedidos creados por el usuario/vendedor

---

## 2. Tabla: `roles`

| Columna    | Tipo         | Constraints | Descripción           |
|------------|--------------|-------------|-----------------------|
| id         | BIGINT UNSIGNED | PK, AI   | Identificador único   |
| name       | VARCHAR(50)  | UNIQUE, NOT NULL | admin, vendedor |
| description| VARCHAR(255) | NULLABLE   | Descripción del rol   |
| created_at | TIMESTAMP    | NULLABLE   |                       |
| updated_at | TIMESTAMP    | NULLABLE   |                       |

**Relaciones:**
- `belongsToMany(User)` → usuarios

**Índices:**
- `idx_roles_name` → Búsqueda por nombre

---

## 3. Tabla: `role_user` (pivote many-to-many)

| Columna    | Tipo         | Constraints | Descripción          |
|------------|--------------|-------------|----------------------|
| id         | BIGINT UNSIGNED | PK, AI   | Identificador único  |
| role_id    | BIGINT UNSIGNED | FK → roles.id, NOT NULL | |
| user_id    | BIGINT UNSIGNED | FK → users.id, NOT NULL | |
| created_at | TIMESTAMP    | NULLABLE   |                      |
| updated_at | TIMESTAMP    | NULLABLE   |                      |

**Índices:**
- `idx_role_user_role_id` → role_id
- `idx_role_user_user_id` → user_id
- `unique_role_user` → UNIQUE(role_id, user_id) → Un usuario no puede tener el mismo rol dos veces

---

## 4. Tabla: `clients`

| Columna    | Tipo         | Constraints | Descripción                |
|------------|--------------|-------------|----------------------------|
| id         | BIGINT UNSIGNED | PK, AI   | Identificador único        |
| name       | VARCHAR(255) | NOT NULL    | Nombre o razón social      |
| email      | VARCHAR(255) | NOT NULL    | Email de contacto          |
| phone      | VARCHAR(50)  | NULLABLE    | Teléfono                   |
| address    | TEXT         | NULLABLE    | Dirección fiscal/envío     |
| tax_id     | VARCHAR(50)  | NULLABLE    | NIF/CIF/RFC (opcional)     |
| created_at | TIMESTAMP    | NULLABLE    |                            |
| updated_at | TIMESTAMP    | NULLABLE    |                            |
| deleted_at | TIMESTAMP    | NULLABLE    | Soft delete                |

**Índices:**
- `idx_clients_email` → Búsqueda por email
- `idx_clients_name` → Búsqueda por nombre
- `idx_clients_deleted_at` → Para filtrar soft deletes

---

## 5. Tabla: `products`

| Columna     | Tipo            | Constraints | Descripción              |
|-------------|-----------------|-------------|--------------------------|
| id          | BIGINT UNSIGNED | PK, AI      | Identificador único      |
| name        | VARCHAR(255)   | NOT NULL    | Nombre del producto      |
| sku         | VARCHAR(100)   | UNIQUE      | Código único (opcional)  |
| description | TEXT            | NULLABLE    | Descripción detallada    |
| price       | DECIMAL(10, 2)  | NOT NULL, DEFAULT 0 | Precio unitario |
| stock       | INT UNSIGNED    | NOT NULL, DEFAULT 0 | Stock disponible |
| created_at  | TIMESTAMP       | NULLABLE    |                          |
| updated_at  | TIMESTAMP       | NULLABLE    |                          |
| deleted_at  | TIMESTAMP       | NULLABLE    | Soft delete              |

**Índices:**
- `idx_products_sku` → UNIQUE
- `idx_products_name` → Búsqueda por nombre
- `idx_products_deleted_at` → Para filtrar soft deletes

---

## 6. Tabla: `orders`

| Columna     | Tipo            | Constraints | Descripción                    |
|-------------|-----------------|-------------|--------------------------------|
| id          | BIGINT UNSIGNED | PK, AI      | Identificador único            |
| client_id   | BIGINT UNSIGNED | FK → clients.id, NOT NULL | Cliente |
| user_id     | BIGINT UNSIGNED | FK → users.id, NULLABLE | Vendedor que creó el pedido |
| order_number| VARCHAR(50)     | UNIQUE      | Número de pedido legible       |
| order_date  | DATE            | NOT NULL    | Fecha del pedido               |
| status      | ENUM/VARCHAR(20)| NOT NULL, DEFAULT 'pending' | pending, confirmed, processing, shipped, delivered, cancelled |
| total       | DECIMAL(12, 2)  | NOT NULL, DEFAULT 0 | Total calculado            |
| notes       | TEXT            | NULLABLE    | Notas internas                 |
| created_at  | TIMESTAMP       | NULLABLE    |                                |
| updated_at  | TIMESTAMP       | NULLABLE    |                                |
| deleted_at  | TIMESTAMP       | NULLABLE    | Soft delete                    |

**Índices:**
- `idx_orders_client_id` → Filtrar por cliente
- `idx_orders_user_id` → Filtrar por vendedor
- `idx_orders_order_date` → Filtrar por fecha
- `idx_orders_status` → Filtrar por estado
- `idx_orders_order_number` → UNIQUE
- `idx_orders_deleted_at` → Para filtrar soft deletes

**Relaciones:**
- `belongsTo(Client)`
- `belongsTo(User)` → vendedor
- `belongsToMany(Product)` con pivot `quantity`, `price`

---

## 7. Tabla: `order_product` (pivote con datos extras)

| Columna     | Tipo            | Constraints | Descripción                    |
|-------------|-----------------|-------------|--------------------------------|
| id          | BIGINT UNSIGNED | PK, AI      | Identificador único            |
| order_id    | BIGINT UNSIGNED | FK → orders.id, NOT NULL | Pedido |
| product_id  | BIGINT UNSIGNED | FK → products.id, NOT NULL | Producto |
| quantity    | INT UNSIGNED    | NOT NULL, DEFAULT 1 | Cantidad pedida    |
| price       | DECIMAL(10, 2)  | NOT NULL    | Precio unitario en momento del pedido |

**Índices:**
- `idx_order_product_order_id` → order_id
- `idx_order_product_product_id` → product_id
- `unique_order_product` → UNIQUE(order_id, product_id) → Evita duplicar mismo producto en mismo pedido

**Nota:** El campo `price` en la pivote guarda el precio en el momento del pedido (precio histórico), para que cambios futuros en `products.price` no afecten pedidos pasados.

---

## 8. Tabla: `personal_access_tokens` (Laravel Sanctum)

| Columna    | Tipo         | Descripción                    |
|------------|--------------|--------------------------------|
| id         | BIGINT UNSIGNED | PK, AI                     |
| tokenable_type | VARCHAR(255) | Modelo (App\Models\User)     |
| tokenable_id   | BIGINT UNSIGNED | ID del usuario             |
| name          | VARCHAR(255) | Nombre del token             |
| token         | VARCHAR(64)  | Token hasheado               |
| abilities     | TEXT         | Permisos (nullable)          |
| last_used_at  | TIMESTAMP    | Último uso                   |
| expires_at    | TIMESTAMP    | Expiración                   |
| created_at    | TIMESTAMP    |                              |
| updated_at    | TIMESTAMP    |                              |

---

## Resumen de Soft Deletes

| Tabla     | Soft Delete | Motivo                                    |
|-----------|-------------|-------------------------------------------|
| users     | ✅ Sí       | Mantener histórico de pedidos asociados   |
| roles     | ❌ No       | Tabla de catálogo, pocos registros        |
| clients   | ✅ Sí       | No borrar clientes con pedidos históricos |
| products  | ✅ Sí       | Mantener histórico en order_product       |
| orders    | ✅ Sí       | Los pedidos nunca se borran físicamente    |

---

## Orden de Migraciones (dependencias)

1. `users` (existente - agregar deleted_at si no existe)
2. `roles`
3. `role_user`
4. `clients`
5. `products`
6. `orders`
7. `order_product`
8. `personal_access_tokens` (Sanctum)

---

## Estados de Pedido (order.status)

| Valor       | Descripción                    |
|-------------|--------------------------------|
| pending     | Pendiente de confirmación      |
| confirmed   | Confirmado                     |
| processing  | En preparación                 |
| shipped     | Enviado                        |
| delivered   | Entregado                      |
| cancelled   | Cancelado                      |

---

## Validaciones a considerar en aplicaciones

- **Cliente:** email único (excepto soft deleted), name requerido
- **Producto:** sku único (nullable), price ≥ 0, stock ≥ 0
- **Pedido:** client_id existe, order_date válida, status válido
- **Order_product:** quantity > 0, price ≥ 0, product_id existe, stock disponible
