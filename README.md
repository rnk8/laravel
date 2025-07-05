# Sistema Corporativo Tramontina

Sistema de gestión corporativa desarrollado en Laravel 10 para la empresa Tramontina, especializado en la gestión de productos, inventario y auditoría de seguridad.

## 🏢 Características Principales

### 🎯 Dashboard Corporativo
- **Estadísticas en tiempo real**: Total de productos, categorías, stock bajo, productos destacados
- **Productos destacados**: Visualización de productos en promoción
- **Control de inventario**: Alertas de stock bajo y gestión automatizada
- **Actividad del sistema**: Monitoreo de logs de auditoría en tiempo real
- **Widgets interactivos**: Métricas y KPIs corporativos

### 📦 Gestión de Productos
- **CRUD completo**: Crear, leer, actualizar y eliminar productos
- **Categorización avanzada**: Sistema jerárquico de categorías
- **Control de stock**: Gestión automática de inventario con alertas
- **Productos destacados**: Sistema de promociones y productos especiales
- **Filtros y búsqueda**: Búsqueda semántica y filtros avanzados
- **Exportación**: Exportación de datos a CSV
- **Especificaciones técnicas**: Material, peso, dimensiones, garantía

### 🔐 Sistema de Auditoría Integrado
- **Middleware de auditoría**: Registra todas las actividades del sistema
- **Detección de amenazas**: Identificación automática de actividades sospechosas
- **Logs forenses**: Registro detallado para análisis de seguridad
- **Integración con Meterpreter**: Para pruebas de penetración
- **Análisis con Syslog**: Procesamiento avanzado de logs
- **Alertas de seguridad**: Notificaciones en tiempo real

### 🎨 Interfaz Corporativa
- **Branding Tramontina**: Diseño personalizado con colores e identidad corporativa
- **Responsive**: Adaptable a dispositivos móviles y desktop
- **UX optimizada**: Interfaz intuitiva y moderna
- **Navegación corporativa**: Menús organizados por funcionalidades
- **Componentes reutilizables**: Sistema de design system integrado

## 🚀 Tecnologías

- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Base de datos**: PostgreSQL
- **Autenticación**: Laravel Breeze
- **Auditoría**: Sistema custom de logging
- **Herramientas de seguridad**: Meterpreter, Syslog

## 📋 Instalación

### Prerrequisitos
- PHP 8.1 o superior
- Composer
- Node.js y npm
- PostgreSQL 13+
- Git

### Pasos de instalación

1. **Clonar el repositorio**
```bash
git clone [url-del-repositorio]
cd tramontina
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
npm run build
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
```

5. **Configurar base de datos PostgreSQL**
Editar `.env` con tus credenciales:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tramontina
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

6. **Generar clave de aplicación**
```bash
php artisan key:generate
```

7. **Ejecutar migraciones**
```bash
php artisan migrate
```

8. **Poblar base de datos con datos de ejemplo**
```bash
php artisan db:seed --class=TramontinaSeeder
```

9. **Iniciar servidor de desarrollo**
```bash
php artisan serve
```

El sistema estará disponible en `http://localhost:8000`

## 👤 Credenciales de Acceso

### Usuario Administrador
- **Email**: admin@tramontina.com
- **Contraseña**: password123

## 📊 Datos de Ejemplo

El seeder incluye:
- **5 categorías principales**: Utensilios, Cuchillería, Sartenes y Ollas, Electrodomésticos, Accesorios
- **13 productos realistas** con especificaciones técnicas completas
- **Precios y descuentos**: Productos con precio regular y precio de oferta
- **Control de stock**: Productos con diferentes niveles de inventario
- **Productos destacados**: Sistema de promociones activado

## 🛡️ Funcionalidades de Seguridad

### Auditoría Automática
- Registro de todas las peticiones HTTP
- Detección de patrones sospechosos
- Monitoreo de intentos de login
- Análisis de User-Agents maliciosos
- Logs de actividad forense

### Configuración de Herramientas
Ver documentación completa en:
- `DOCUMENTACION_AUDITORIA.md` - Guía de Meterpreter y Syslog
- `INSTALACION_TRAMONTINA.md` - Manual de instalación completo

## 🗂️ Estructura del Proyecto

```
tramontina/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php      # Dashboard corporativo
│   │   ├── ProductController.php        # Gestión de productos
│   │   └── AuditDashboardController.php # Dashboard de auditoría
│   ├── Models/
│   │   ├── Product.php                  # Modelo de productos
│   │   ├── Category.php                 # Modelo de categorías
│   │   └── AuditLog.php                 # Modelo de auditoría
│   └── Http/Middleware/
│       └── AuditLogger.php              # Middleware de auditoría
├── database/
│   ├── migrations/                      # Migraciones de BD
│   └── seeders/
│       └── TramontinaSeeder.php         # Datos de ejemplo
├── resources/views/
│   ├── dashboard.blade.php              # Vista del dashboard
│   ├── products/                        # Vistas de productos
│   ├── auth/login.blade.php             # Login corporativo
│   └── layouts/                         # Layouts personalizados
└── config/
    ├── audit.php                        # Configuración de auditoría
    └── logging.php                      # Configuración de logs
```

## 🔧 Configuración Avanzada

### Variables de Entorno Principales
```env
# Aplicación
APP_NAME="Tramontina"
APP_ENV=local

# Base de datos
DB_CONNECTION=pgsql
DB_DATABASE=tramontina

# Auditoría
AUDIT_ENABLED=true
AUDIT_RETENTION_DAYS=90

# Syslog
SYSLOG_HOST=127.0.0.1
SYSLOG_PORT=514

# Meterpreter
METERPRETER_ENABLED=true
METASPLOIT_HOST=127.0.0.1
```

## 📈 Dashboard y Métricas

El dashboard corporativo incluye:
- **Estadísticas generales**: Contadores de productos, categorías, stock
- **Productos destacados**: Grid visual de productos en promoción
- **Alertas de stock**: Lista de productos con inventario bajo
- **Actividad reciente**: Timeline de actividad del sistema
- **Categorías principales**: Distribución de productos por categoría
- **Enlaces rápidos**: Acceso directo a funciones principales

## 🏷️ Gestión de Productos

### Características de productos
- Información básica (nombre, SKU, descripción)
- Especificaciones técnicas (material, peso, dimensiones)
- Gestión de precios (precio regular, precio de oferta)
- Control de inventario (stock, gestión automática)
- Categorización y etiquetado
- Sistema de productos destacados
- Imágenes y galería multimedia

### Filtros disponibles
- Búsqueda por texto
- Filtro por categoría
- Filtro por estado (activo/inactivo/descontinuado)
- Filtro por nivel de stock
- Ordenamiento múltiple

## 🎯 Próximas Funcionalidades

- [ ] Módulo de ventas y facturación
- [ ] Reportes avanzados con gráficos
- [ ] API REST para integraciones
- [ ] Sistema de notificaciones push
- [ ] Módulo de proveedores
- [ ] Control de calidad
- [ ] Integración con e-commerce

## 🤝 Soporte

Para soporte técnico o consultas sobre el sistema:
- Documentación de auditoría: `DOCUMENTACION_AUDITORIA.md`
- Manual de instalación: `INSTALACION_TRAMONTINA.md`
- Logs del sistema: `storage/logs/`

## 📄 Licencia

Sistema desarrollado para uso corporativo de Tramontina.

---

**Versión**: 1.0  
**Última actualización**: {{ date('Y-m-d') }}  
**Desarrollado con**: ❤️ para Tramontina
