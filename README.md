<div align="center">

# 🛡️ Reportería — API

### Backend API para sistema de reportería de ventas de seguros

[![PHP](https://img.shields.io/badge/PHP-8.2+-8892BF?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-Multi--DB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Passport](https://img.shields.io/badge/OAuth2-Laravel_Passport-red?style=for-the-badge&logo=auth0&logoColor=white)](https://laravel.com/docs/passport)
[![Status](https://img.shields.io/badge/Estado-En_Desarrollo-orange?style=for-the-badge)]()

</div>

---

## 📋 Descripción

**Reportería BNT** es una API RESTful desarrollada con **Laravel 12** que actúa como backend para un sistema de reportería y dashboard orientado a la gestión de ventas de **seguros de automóvil**. El sistema consolida información de **múltiples bases de datos MySQL** (campañas comerciales independientes) en una sola capa de acceso unificada, ofreciendo análisis en tiempo real por fecha, campaña, estatus y agente.

> 🏗️ **Proyecto en desarrollo activo** — nuevas funcionalidades de dashboard y reportería avanzada en camino.

---

## ⚙️ Stack Tecnológico

| Capa | Tecnología | Versión | Rol |
|---|---|---|---|
| **Runtime** | PHP | `^8.2` | Lenguaje base |
| **Framework** | Laravel | `^12.0` | Core del backend |
| **Auth** | Laravel Passport | `^13.4` | OAuth2 / Tokens de acceso |
| **RBAC** | Spatie Permission | `^6.23` | Roles y permisos granulares |
| **Base de datos** | MySQL (multi-conexión) | — | Persistencia por campaña |
| **Testing** | PHPUnit | `^11.5` | Pruebas unitarias e integración |
| **Dev Tools** | Laravel Pint, Pail, Sail | Latest | Linting, logs en vivo, Docker |
| **Build** | Vite + Node.js | — | Asset bundling |

---

## 🏛️ Arquitectura

### Patrón general

```
Cliente (Vue 3 SPA)
        │
        │ HTTP + Cookie HttpOnly (access_token)
        ▼
┌──────────────────────────────────────┐
│          Laravel 12 API              │
│  ┌──────────┐  ┌──────────────────┐  │
│  │ Middleware│  │   Form Requests  │  │
│  │ auth:api  │  │   (Validación)   │  │
│  └──────────┘  └──────────────────┘  │
│         │                            │
│  ┌──────▼──────────────────────┐     │
│  │        Controllers (API)    │     │
│  │  Auth · Sales · Users        │     │
│  │  Roles · Campaigns · Status  │     │
│  │  Catalogs · Email            │     │
│  └──────────────┬──────────────┘     │
│                 │                    │
│  ┌──────────────▼──────────────┐     │
│  │       Service Layer         │     │
│  │    DataBasesServices        │     │
│  └──────────────┬──────────────┘     │
│                 │                    │
│  ┌──────────────▼──────────────┐     │
│  │  Multi-DB Connection Layer  │     │
│  │  mysql_main · mysql_multi   │     │
│  └─────────────────────────────┘     │
└──────────────────────────────────────┘
        │                    │
        ▼                    ▼
  DB Principal         DBs por Campaña
  (users, roles,       (ventas, clientes,
   campaigns,          usuarios, catálogos)
   permissions)
```

### Característica clave: Multi-Database Engine

El sistema gestiona **campañas comerciales independientes**, cada una con su propia base de datos MySQL. El servicio `DataBasesServices` realiza conexiones dinámicas en runtime, sin necesidad de definir cada base de datos en la configuración estática:

```php
// Conexión dinámica por nombre de campaña
$db = app(DataBasesServices::class)->connectionTo($campaign->db_name);
$sales = $db->table('ventas')->whereBetween('fecha_venta', [...])->get();
```

Esto permite **escalar horizontalmente** agregando campañas sin modificar código.

---

## 🔐 Seguridad y Autenticación

- **OAuth2 con Laravel Passport** — emisión y revocación de `access_token`
- **Tokens almacenados en Cookie `HttpOnly` + `SameSite: Lax`** — protección contra XSS
- **RBAC jerárquico por niveles** — `nivel-1` a `nivel-4`; cada nivel restringe qué roles y usuarios puede gestionar
- **Autenticación dual** — login por `email` o `RFC` (campo `user`)
- **Todas las rutas protegidas** bajo middleware `auth:api`
- **Visibilidad de datos filtrada por nivel** — usuarios y roles expuestos según jerarquía del solicitante

---

## 📡 Endpoints de la API

### Autenticación
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| `GET` | `/api/version` | Versión del sistema | ❌ |
| `POST` | `/api/login` | Login (email o RFC) | ❌ |
| `GET` | `/api/me` | Usuario autenticado | ✅ |
| `POST` | `/api/logout` | Cerrar sesión | ✅ |
| `GET` | `/api/password/reset/{user_id}` | Reset contraseña | ✅ |
| `POST` | `/api/password/change` | Cambiar contraseña | ✅ |

### Ventas & Dashboard
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| `POST` | `/api/sales/get-general-counts` | KPIs generales por campaña y estatus | ✅ |
| `POST` | `/api/sales/search` | Búsqueda detallada de ventas con filtros | ✅ |
| `POST` | `/api/sales/delete` | Eliminación auditada de ventas | ✅ |

### Catálogos
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| `GET` | `/api/campaigns` | Listado de campañas activas | ✅ |
| `GET` | `/api/statuses` | Catálogo de estatus de ventas | ✅ |
| `GET` | `/api/catalogs/{db_id}` | 🆕 Tablas de catálogo de una campaña | ✅ |
| `GET` | `/api/catalogs/{db_id}/search/{table_name}` | 🆕 Contenido de una tabla catálogo | ✅ |

### Emails
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| `POST` | `/api/email-sent-stats` | 🆕 Estadísticas de envíos de email (24h / 48h) | ✅ |

### Usuarios & Control de Acceso
| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| `GET/POST/PUT/DELETE` | `/api/users` | CRUD de usuarios (filtrado por nivel) | ✅ |
| `GET` | `/api/roles` | Roles con permisos (filtrado por nivel) | ✅ |
| `GET` | `/api/permissions` | Todos los permisos | ✅ |
| `GET` | `/api/permissions-by-group` | Permisos agrupados por módulo | ✅ |
| `GET` | `/api/role-access/{role_id}` | Acceso de un rol (permisos + campañas) | ✅ |
| `POST` | `/api/access-control` | Asignación de permisos y campañas a un rol | ✅ |

---

## 🗂️ Estructura del Proyecto

```
rep_bnt_api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # Controladores REST
│   │   │   ├── AuthController.php
│   │   │   ├── SaleController.php
│   │   │   ├── UserController.php
│   │   │   ├── RolesAndPermissionsController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── StatusController.php
│   │   │   ├── CatalogController.php   # 🆕 Explorador de catálogos multi-DB
│   │   │   └── EmailController.php     # 🆕 Estadísticas de envío de emails
│   │   ├── Requests/            # Validación por Form Requests
│   │   │   ├── Sales/
│   │   │   ├── User/
│   │   │   ├── Email/           # 🆕
│   │   │   └── RolesAndPermissions/
│   │   ├── Resources/           # API Resources (transformación de respuestas)
│   │   └── Middleware/
│   ├── Models/                  # Eloquent ORM
│   │   ├── User.php             # HasApiTokens + HasRoles
│   │   ├── Role.php             # RBAC + Campañas
│   │   ├── Campaign.php
│   │   ├── Status.php
│   │   └── LogDeletedSale.php   # Auditoría de eliminaciones
│   ├── Services/
│   │   └── DataBasesServices.php  # Motor multi-DB dinámico
│   ├── Helpers/
│   │   └── CustomerData.php       # Campos dinámicos por campaña
│   └── Providers/
├── database/
│   ├── migrations/              # 10 migraciones versionadas
│   └── seeders/
├── routes/
│   └── api.php                  # Definición de rutas protegidas
└── tests/                       # PHPUnit
```

---

## 🧩 Módulos del Sistema

### 📊 Dashboard de Ventas
Consolida en tiempo real los **KPIs de ventas** de todas las campañas activas:
- Conteos por estatus: `AP`, `NAP`, `PEN`, `HOLD`, `AOP`, `REC`
- Cálculo de **% de no-aplica (NAP)** por campaña
- Filtros por rango de fechas, campaña y estatus

### 🔍 Buscador de Ventas
- Consultas cruzadas a múltiples bases de datos de forma transparente
- Selects dinámicos adaptados a la estructura de cada campaña
- Joins con tablas de clientes, usuarios y catálogos de calificación

### 🗑️ Eliminación Auditada
- Backup automático en tabla `ventas_eliminadas` antes de eliminar
- Registro en `log_deleted_sales` con usuario, base de datos y certificado
- Manejo de errores granular por certificado

### 👥 Gestión de Usuarios
- CRUD completo con validación por Form Requests
- Un usuario = un rol (modelo simplificado)
- Cambio y reset de contraseña integrados
- **Filtrado por nivel jerárquico**: cada usuario solo ve los usuarios que puede gestionar

### 🛡️ Control de Acceso (RBAC Jerárquico) — Actualizado
- Roles con **permisos granulares** agrupados por módulo
- Roles vinculados a **campañas específicas** (visibilidad de datos)
- Sincronización atómica de permisos y campañas por rol
- **4 niveles jerárquicos** (`nivel-1` a `nivel-4`): cada nivel restringe qué roles puede ver y asignar
- `Super-Admin` tiene visibilidad total sin restricciones

### 📋 Catálogos Dinámicos — 🆕 Nuevo
- Exploración de tablas catálogo de cualquier base de datos de campaña
- Filtro automático de tablas con prefijo `catalogo_` (excluye backups)
- Permite al frontend construir dropdowns dinámicos desde las fuentes de datos reales

### 📧 Estadísticas de Emails — 🆕 Nuevo
- Reporte de envíos de correos por campaña y periodo
- Distingue entre emails de **24h** (`log_email_validacion`) y **48h** (`log_email_automatico`)
- Modos: **totales** por campaña o **listado detallado** de registros enviados
- Soporte para selects dinámicos según el esquema de cada campaña (campos `nombre`, `correo`)

---

## 🚀 Instalación y Configuración

### Prerrequisitos
- PHP `>= 8.2`
- Composer
- MySQL (base de datos principal + bases de campañas)
- Node.js + NPM

### Setup rápido

```bash
# 1. Clonar el repositorio
git clone https://github.com/cr-gcc/rep_bnt_api.git
cd rep_bnt_api

# 2. Setup automatizado (install, .env, key, migrate, npm build)
composer run setup

# 3. Configurar variables de entorno
cp .env.example .env
# Editar .env con credenciales de BD, configuración multi-DB, etc.

# 4. Instalar Passport (OAuth2 keys)
php artisan passport:install

# 5. Ejecutar en desarrollo
composer run dev
```
---

## 🧪 Testing

```bash
# Ejecutar suite completa
composer run test

# O directamente con PHPUnit
php artisan test --coverage
```

---

## 🗺️ Roadmap

- [x] Autenticación OAuth2 con Laravel Passport
- [x] RBAC con Spatie Permissions + campañas
- [x] Dashboard de KPIs multi-campaña
- [x] Buscador de ventas con filtros dinámicos
- [x] Eliminación auditada con backup y log
- [x] CRUD de usuarios con control de contraseña
- [x] **RBAC jerárquico por niveles** (`nivel-1` a `nivel-4`)
- [x] **Explorador de catálogos** multi-DB
- [x] **Estadísticas de envío de emails** (24h / 48h)
- [ ] **Exportación a Excel/CSV** de reportes
- [ ] **Gráficas y series de tiempo** para el dashboard
- [ ] **Notificaciones** por umbrales de NAP
- [ ] **Endpoint de métricas** por agente/supervisor
- [ ] **Rate limiting** y throttling por usuario
- [ ] **Caché de consultas** con Redis para KPIs frecuentes
- [ ] **Documentación Swagger/OpenAPI** autogenerada
- [ ] **CI/CD pipeline** con GitHub Actions

---

## 👨‍💻 Decisiones de Diseño

| Decisión | Justificación |
|---|---|
| **Multi-DB dinámico** | Cada campaña tiene su propio esquema legacy; la conexión dinámica evita N configuraciones estáticas |
| **Cookie HttpOnly en lugar de Bearer** | Protección nativa contra XSS sin lógica de almacenamiento en el frontend |
| **Form Requests por endpoint** | Validación centralizada, desacoplada del controlador, fácil de testear |
| **Un rol por usuario** | Regla de negocio del cliente; simplifica la lógica de control de acceso |
| **Spatie + tabla pivote rol-campaña** | Permite restringir qué datos ve cada rol sin modificar las queries de negocio |
| **Service Layer para multi-DB** | Encapsula la complejidad de reconexión; los controladores no conocen detalles de conexión |

---

<div align="center">

**Reportería API** — Desarrollado con ❤️ y Laravel 12

</div>
