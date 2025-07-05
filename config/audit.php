<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de Auditoría - Tramontina
    |--------------------------------------------------------------------------
    |
    | Esta configuración controla el sistema de auditoría y logging
    | para el proyecto de auditoría de la empresa Tramontina.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Auditoría Habilitada
    |--------------------------------------------------------------------------
    |
    | Controla si el sistema de auditoría está activo.
    | En producción siempre debe estar en true.
    |
    */
    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Configuración de Logs
    |--------------------------------------------------------------------------
    |
    | Configuración de los diferentes canales de logging.
    |
    */
    'logging' => [
        'driver' => env('AUDIT_LOG_DRIVER', 'database'),
        'channel' => env('AUDIT_LOG_CHANNEL', 'audit'),
        'security_enabled' => env('SECURITY_LOG_ENABLED', true),
        'access_enabled' => env('ACCESS_LOG_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Seguridad
    |--------------------------------------------------------------------------
    |
    | Parámetros para la detección de actividades sospechosas.
    |
    */
    'security' => [
        'failed_login_attempts' => env('FAILED_LOGIN_ATTEMPTS', 3),
        'lockout_time' => env('LOGIN_LOCKOUT_TIME', 300), // 5 minutos
        'password_history_count' => env('PASSWORD_HISTORY_COUNT', 5),
        
        // Horario laboral (24h format)
        'business_hours' => [
            'start' => 7,
            'end' => 19,
        ],

        // User agents sospechosos
        'suspicious_user_agents' => [
            'sqlmap', 'nikto', 'nmap', 'dirb', 'gobuster', 
            'metasploit', 'burp', 'owasp', 'w3af'
        ],

        // Patrones de URL sospechosos
        'suspicious_patterns' => [
            '../', '..\\', '/etc/passwd', '\\windows\\',
            'union select', 'drop table', 'insert into',
            '<script', 'javascript:', 'cmd=', 'exec=',
            '1=1', 'OR 1=1', 'admin\'--'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Syslog
    |--------------------------------------------------------------------------
    |
    | Configuración para envío de logs via Syslog.
    |
    */
    'syslog' => [
        'host' => env('SYSLOG_HOST', '127.0.0.1'),
        'port' => env('SYSLOG_PORT', 514),
        'protocol' => env('SYSLOG_PROTOCOL', 'udp'),
        'facility' => env('SYSLOG_FACILITY', 'local0'),
        'level' => env('SYSLOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Meterpreter
    |--------------------------------------------------------------------------
    |
    | Configuración para tests de penetración con Meterpreter.
    | IMPORTANTE: Solo usar con autorización explícita.
    |
    */
    'meterpreter' => [
        'test_mode' => env('PENETRATION_TEST_MODE', false),
        'host' => env('METASPLOIT_HOST', '127.0.0.1'),
        'port' => env('METASPLOIT_PORT', 4444),
        
        // IPs autorizadas para testing
        'authorized_ips' => [
            '127.0.0.1',
            '::1',
            // Agregar IPs del equipo de auditoría
        ],

        // Tipos de payload soportados
        'payloads' => [
            'windows/meterpreter/reverse_tcp',
            'linux/x86/meterpreter/reverse_tcp',
            'php/meterpreter/reverse_tcp',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reportes
    |--------------------------------------------------------------------------
    |
    | Configuración para generación automática de reportes.
    |
    */
    'reports' => [
        'auto_generate' => env('AUTO_GENERATE_REPORTS', true),
        'daily_report_time' => '23:59',
        'weekly_report_day' => 'sunday',
        'monthly_report_day' => 1,
        
        // Emails para envío de reportes
        'recipients' => [
            'auditoria@tramontina.com',
            'seguridad@tramontina.com',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Retención
    |--------------------------------------------------------------------------
    |
    | Tiempo de retención de logs y datos de auditoría.
    |
    */
    'retention' => [
        'audit_logs_days' => 365, // 1 año
        'security_logs_days' => 730, // 2 años
        'access_logs_days' => 90, // 3 meses
        'penetration_test_logs_days' => 180, // 6 meses
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Alertas
    |--------------------------------------------------------------------------
    |
    | Configuración para alertas automáticas de seguridad.
    |
    */
    'alerts' => [
        'enabled' => true,
        'channels' => ['email', 'log'], // email, slack, log
        'thresholds' => [
            'failed_logins_per_minute' => 5,
            'suspicious_requests_per_minute' => 10,
            'error_rate_percentage' => 15,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de GeoIP
    |--------------------------------------------------------------------------
    |
    | Configuración para localización geográfica de IPs.
    |
    */
    'geoip' => [
        'enabled' => false,
        'service' => 'maxmind', // maxmind, ipapi, freegeoip
        'api_key' => env('GEOIP_API_KEY'),
        'database_path' => storage_path('app/geoip'),
    ],

]; 