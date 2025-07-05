# Guía de Instalación - Sistema de Auditoría Tramontina

## 📋 Prerequisitos

### 1. Software Requerido
```bash
# Windows 10/11
- PHP 8.1 o superior
- Composer
- PostgreSQL 13+
- Node.js 18+
- Git
```

### 2. Herramientas de Auditoría (Opcionales)
```bash
# Para pentesting autorizado
- Metasploit Framework
- Wireshark
- Nmap
- Burp Suite
```

---

## 🚀 Instalación Paso a Paso

### Paso 1: Clonar/Configurar el Proyecto
```bash
# Si ya tienes el proyecto
cd "C:\Users\Ren\Desktop\moises auditoria\tramontina"

# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install
```

### Paso 2: Configurar Base de Datos PostgreSQL

#### 2.1 Crear Base de Datos
```sql
-- Conectar a PostgreSQL como superusuario
psql -U postgres

-- Crear base de datos
CREATE DATABASE tramontina;

-- Crear usuario (si no existe)
CREATE USER postgres WITH PASSWORD '0808';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON DATABASE tramontina TO postgres;
GRANT ALL ON SCHEMA public TO postgres;
```

#### 2.2 Configurar Conexión
```bash
# Tu archivo .env ya está configurado correctamente con:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tramontina
DB_USERNAME=postgres
DB_PASSWORD=0808
```

### Paso 3: Ejecutar Migraciones
```bash
# Crear las tablas
php artisan migrate

# Verificar que se creó la tabla audit_logs
php artisan tinker
>>> \Schema::hasTable('audit_logs')
>>> exit
```

### Paso 4: Generar Claves de Aplicación
```bash
# Si necesitas nueva clave
php artisan key:generate
```

### Paso 5: Configurar Permisos de Logs
```bash
# En Windows (PowerShell como Administrador)
New-Item -ItemType Directory -Force -Path "storage\logs"
icacls "storage\logs" /grant "Everyone:(OI)(CI)F"

# Crear directorios específicos
New-Item -ItemType Directory -Force -Path "storage\logs\audit"
New-Item -ItemType Directory -Force -Path "storage\logs\security"
```

---

## 🔧 Configuración de Syslog (Windows)

### Opción 1: Usar Syslog Local (Recomendado)
```bash
# El proyecto ya está configurado para usar archivos locales
# Los logs se guardarán en:
# - storage/logs/audit.log (Auditoría general)
# - storage/logs/security.log (Eventos de seguridad)
# - storage/logs/access.log (Logs de acceso)
# - storage/logs/meterpreter.log (Tests de penetración)
```

### Opción 2: Configurar Syslog Server
```bash
# Instalar Kiwi Syslog Server (Gratuito para uso personal)
# Descargar: https://www.kiwisyslog.com/

# Configurar en puerto 514 UDP
# Direccionar logs a: C:\Logs\Tramontina\
```

---

## 🔴 Configuración de Meterpreter

### 1. Instalar Metasploit Framework
```bash
# Descargar desde: https://www.metasploit.com/download
# Instalar en: C:\metasploit-framework\

# Agregar a PATH del sistema
$env:PATH += ";C:\metasploit-framework\bin"
```

### 2. Configurar Listeners
```bash
# Script de configuración básica
msfconsole -q -x "
use exploit/multi/handler;
set payload windows/meterpreter/reverse_tcp;
set LHOST 127.0.0.1;
set LPORT 4444;
set ExitOnSession false;
exploit -j
"
```

### 3. Variables de Entorno ya Configuradas
```bash
PENETRATION_TEST_MODE=false    # Cambiar a true solo durante tests
METASPLOIT_HOST=127.0.0.1     # IP local
METASPLOIT_PORT=4444          # Puerto para Meterpreter
```

---

## 🛠️ Comandos de Mantenimiento

### Limpiar Logs
```bash
# Limpiar logs antiguos (más de 30 días)
php artisan log:clear

# Backup de logs
php artisan schedule:run

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Monitoreo en Tiempo Real
```bash
# Ver logs de auditoría en tiempo real
Get-Content -Path "storage\logs\audit.log" -Wait -Tail 50

# Ver logs de seguridad
Get-Content -Path "storage\logs\security.log" -Wait -Tail 50

# Ver actividad de Meterpreter
Get-Content -Path "storage\logs\meterpreter.log" -Wait -Tail 50
```

---

## 🧪 Verificación de Instalación

### Paso 1: Iniciar Servidor Laravel
```bash
php artisan serve
```

### Paso 2: Probar Sistema de Auditoría
```bash
# En un navegador, visitar:
http://localhost:8000

# Verificar logs generados:
Get-Content -Path "storage\logs\audit.log" -Tail 10
```

### Paso 3: Verificar Base de Datos
```bash
php artisan tinker
>>> App\Models\AuditLog::count()
>>> App\Models\AuditLog::latest()->first()
>>> exit
```

---

## 🔍 Uso del Sistema

### Comandos de Auditoría

#### Consultar Logs
```bash
# Logs sospechosos de las últimas 24 horas
php artisan tinker
>>> App\Models\AuditLog::suspicious()->lastMinutes(1440)->get()

# IPs más activas
>>> App\Models\AuditLog::topActiveIps(10, 24)

# Patrones de ataque
>>> App\Models\AuditLog::attackPatterns(24)
>>> exit
```

#### Activar Modo Penetration Test
```bash
# Editar .env
PENETRATION_TEST_MODE=true

# Reiniciar servidor
php artisan serve
```

#### Generar Reporte de Auditoría
```bash
php artisan tinker
>>> $start = \Carbon\Carbon::today()
>>> $end = \Carbon\Carbon::now()
>>> $report = App\Models\AuditLog::auditReport($start, $end)
>>> print_r($report)
>>> exit
```

---

## 📊 Dashboard Web (Próximamente)

El sistema incluirá un dashboard web para:
- Monitoreo en tiempo real
- Alertas de seguridad
- Reportes automáticos
- Gestión de tests de penetración

---

## 🚨 Procedimientos de Emergencia

### Detectar Actividad Sospechosa
```bash
# Revisar alertas de seguridad
Get-Content -Path "storage\logs\security.log" | Select-String "ALERTA"

# Verificar IPs bloqueadas
php artisan tinker
>>> App\Models\AuditLog::suspicious()->byLevel('critical')->today()->get()
>>> exit
```

### Backup de Evidencia
```bash
# Crear backup completo
$date = Get-Date -Format "yyyyMMdd_HHmm"
Compress-Archive -Path "storage\logs\*" -DestinationPath "backup_tramontina_$date.zip"
```

---

## 📞 Soporte y Contacto

- **Responsable Técnico:** auditoria@tramontina.com
- **Emergencias de Seguridad:** +XX XXXX-XXXX
- **Documentación:** Ver DOCUMENTACION_AUDITORIA.md

---

## ⚠️ Advertencias Importantes

1. **NUNCA ejecutar herramientas de penetración sin autorización**
2. **Mantener PENETRATION_TEST_MODE=false en producción**
3. **Hacer backup regular de logs de auditoría**
4. **Revisar diariamente logs de seguridad**
5. **Seguir normativas legales locales**

---

*Sistema configurado para la materia de Auditoría - Empresa Tramontina*
*Versión: 1.0 - Fecha: $(Get-Date -Format "yyyy-MM-dd")* 