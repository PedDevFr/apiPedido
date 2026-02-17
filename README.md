# Sistema de Gestión de Pedidos (Mini ERP)

API REST profesional para un sistema de gestión de pedidos desarrollada con Laravel 10+ y MySQL.

## Características

- **Autenticación** con Laravel Sanctum (registro, login, logout)
- **Roles** admin y vendedor con middleware de restricción
- **CRUD completo** de Clientes, Productos y Pedidos
- **Relaciones** Order ↔ Client, Order ↔ Products (many-to-many con cantidad y precio)
- **Filtros** por fecha, cliente, búsqueda de productos
- **Paginación** en todos los listados
- **Respuestas JSON estandarizadas**
- **Manejo centralizado de errores**
- **Soft deletes** en usuarios, clientes, productos y pedidos

## Stack Tecnológico

- PHP 8.2+
- Laravel 12
- MySQL
- Laravel Sanctum
- Eloquent ORM
- FormRequest para validación

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/Api/     # AuthController, ClientController, OrderController, ProductController
│   ├── Middleware/          # CheckRole
│   ├── Requests/            # FormRequests de validación
│   └── Traits/              # ApiResponse
├── Models/                  # User, Role, Client, Product, Order
database/
├── migrations/              # Esquema completo con FKs e índices
├── seeders/                 # RoleSeeder, UserSeeder, ClientSeeder, etc.
└── factories/               # Factories con Faker
docs/
└── DATABASE_DESIGN.md       # Documentación del diseño de BD
```

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8+
- Extensiones PHP: mbstring, pdo, openssl, json, zip (para Composer)

## Instalación

```bash
# Clonar e instalar dependencias
composer install

# Copiar variables de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar .env con credenciales MySQL
# DB_DATABASE=market
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Cargar datos iniciales (opcional)
php artisan db:seed

# Iniciar servidor
php artisan serve
```

La API estará disponible en `http://localhost:8000/api`

## Datos de Prueba (Seeder)

| Usuario | Email | Password | Rol |
|---------|-------|----------|-----|
| Admin | admin@example.com | password | admin |
| Vendedor | vendedor@example.com | password | vendedor |

## Endpoints

### Autenticación

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/register` | Registrar usuario | No |
| POST | `/api/auth/login` | Iniciar sesión | No |
| POST | `/api/auth/logout` | Cerrar sesión | Sí |
| GET | `/api/auth/me` | Usuario actual | Sí |

### Clientes

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/clients` | Listar (paginado) |
| POST | `/api/clients` | Crear |
| GET | `/api/clients/{id}` | Ver detalle |
| PUT/PATCH | `/api/clients/{id}` | Actualizar |
| DELETE | `/api/clients/{id}` | Eliminar (soft) |

### Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/products` | Listar (paginado, ?search=) |
| POST | `/api/products` | Crear |
| GET | `/api/products/{id}` | Ver detalle |
| PUT/PATCH | `/api/products/{id}` | Actualizar |
| DELETE | `/api/products/{id}` | Eliminar (soft) |

### Pedidos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/orders` | Listar (?client_id=, ?date_from=, ?date_to=, ?status=) |
| POST | `/api/orders` | Crear |
| GET | `/api/orders/{id}` | Ver detalle |
| PUT/PATCH | `/api/orders/{id}` | Actualizar |
| DELETE | `/api/orders/{id}` | Eliminar (soft) |

## Ejemplos de Request/Response

### Registro

**Request:**
```json
POST /api/auth/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Usuario registrado correctamente",
  "data": {
    "user": {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "roles": [{"id": 2, "name": "vendedor"}]
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Login

**Request:**
```json
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com"
    },
    "token": "2|xyz789...",
    "token_type": "Bearer"
  }
}
```

### Crear Pedido

**Request:**
```json
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
  "client_id": 1,
  "order_date": "2026-02-16",
  "status": "pending",
  "products": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 10.50
    },
    {
      "product_id": 2,
      "quantity": 1,
      "price": 25.00
    }
  ]
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Pedido creado correctamente",
  "data": {
    "id": 1,
    "order_number": "ORD-20260216-0001",
    "client_id": 1,
    "user_id": 2,
    "order_date": "2026-02-16",
    "status": "pending",
    "total": 46.00,
    "products": [
      {"id": 1, "name": "Producto A", "pivot": {"quantity": 2, "price": 10.50}},
      {"id": 2, "name": "Producto B", "pivot": {"quantity": 1, "price": 25.00}}
    ]
  }
}
```

### Respuesta de Error (Validación)

**Response (422):**
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["El nombre es obligatorio."]
  }
}
```

### Respuesta de Error (No autorizado)

**Response (401):**
```json
{
  "success": false,
  "message": "No autenticado."
}
```

## Filtros y Paginación

**Productos - Búsqueda:**
```
GET /api/products?search=nombre&per_page=15
```

**Pedidos - Filtros:**
```
GET /api/orders?client_id=1&date_from=2026-01-01&date_to=2026-02-16&status=pending&per_page=15
```

## Estados de Pedido

- `pending` - Pendiente
- `confirmed` - Confirmado
- `processing` - En preparación
- `shipped` - Enviado
- `delivered` - Entregado
- `cancelled` - Cancelado

## Testing

```bash
php artisan test
# o para tests específicos:
php artisan test tests/Feature/Api
```

Tests incluidos:
- Registro de usuario
- Login
- Creación de pedido
- Restricción por rol

## Documentación Adicional

- [Diseño de Base de Datos](docs/DATABASE_DESIGN.md)

## License

MIT
