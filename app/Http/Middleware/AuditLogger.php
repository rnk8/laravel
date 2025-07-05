<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);

        // Solo auditar si está habilitado
        if (config('app.audit_enabled', true)) {
            $this->logActivity($request, $response, $startTime, $endTime);
        }

        return $response;
    }

    /**
     * Registrar actividad del usuario
     */
    private function logActivity(Request $request, $response, $startTime, $endTime)
    {
        try {
            $user = Auth::user();
            $responseTime = round(($endTime - $startTime) * 1000, 2); // en milisegundos

            $auditData = [
                'user_id' => $user ? $user->id : null,
                'user_email' => $user ? $user->email : null,
                'ip_address' => $this->getClientIp($request),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => $request->route() ? $request->route()->getName() : null,
                'status_code' => $response->getStatusCode(),
                'response_time' => $responseTime,
                'timestamp' => Carbon::now()->toISOString(),
                'session_id' => $request->session()->getId(),
                'csrf_token' => $request->session()->token(),
            ];

            // Datos adicionales según el tipo de request
            if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
                $auditData['request_data'] = $this->sanitizeRequestData($request->all());
            }

            // Detectar actividades sospechosas
            $this->detectSuspiciousActivity($auditData, $request);

            // Guardar en base de datos
            $this->saveToDatabase($auditData);

            // Registrar en logs
            $this->logToFiles($auditData, $request);

        } catch (\Exception $e) {
            Log::channel('security')->error('Error en AuditLogger', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Obtener IP real del cliente
     */
    private function getClientIp(Request $request)
    {
        $ip = $request->ip();
        
        // Verificar headers de proxy
        if ($request->header('X-Forwarded-For')) {
            $ip = explode(',', $request->header('X-Forwarded-For'))[0];
        } elseif ($request->header('X-Real-IP')) {
            $ip = $request->header('X-Real-IP');
        }

        return trim($ip);
    }

    /**
     * Sanitizar datos sensibles del request
     */
    private function sanitizeRequestData(array $data)
    {
        $sensitive = ['password', 'password_confirmation', 'token', 'secret', 'key'];
        
        foreach ($sensitive as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Detectar actividades sospechosas
     */
    private function detectSuspiciousActivity(array $auditData, Request $request)
    {
        $suspicious = false;
        $reasons = [];

        // Verificar múltiples intentos de login fallidos
        if ($auditData['status_code'] == 401 || $auditData['status_code'] == 403) {
            $recentFailures = AuditLog::where('ip_address', $auditData['ip_address'])
                ->where('status_code', '>=', 400)
                ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                ->count();

            if ($recentFailures >= 3) {
                $suspicious = true;
                $reasons[] = 'Multiple failed authentication attempts';
            }
        }

        // Verificar acceso fuera de horario laboral
        $hour = Carbon::now()->hour;
        if ($hour < 7 || $hour > 19) {
            $reasons[] = 'Access outside business hours';
        }

        // Verificar User-Agent sospechoso
        $userAgent = $auditData['user_agent'] ?? '';
        $suspiciousAgents = ['sqlmap', 'nikto', 'nmap', 'dirb', 'gobuster', 'metasploit'];
        
        foreach ($suspiciousAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                $suspicious = true;
                $reasons[] = 'Suspicious user agent: ' . $agent;
            }
        }

        // Verificar patrones de URL sospechosos
        $url = $auditData['url'];
        $suspiciousPatterns = ['../', 'etc/passwd', 'cmd=', 'union select', '<script'];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($url, $pattern) !== false) {
                $suspicious = true;
                $reasons[] = 'Suspicious URL pattern: ' . $pattern;
            }
        }

        if ($suspicious) {
            Log::channel('security')->warning('Actividad sospechosa detectada', [
                'audit_data' => $auditData,
                'reasons' => $reasons,
                'penetration_test_mode' => config('app.penetration_test_mode', false)
            ]);

            // Si no estamos en modo test, enviar alerta
            if (!config('app.penetration_test_mode', false)) {
                $this->sendSecurityAlert($auditData, $reasons);
            }
        }
    }

    /**
     * Guardar en base de datos
     */
    private function saveToDatabase(array $auditData)
    {
        AuditLog::create($auditData);
    }

    /**
     * Registrar en archivos de log
     */
    private function logToFiles(array $auditData, Request $request)
    {
        // Log de auditoría general
        Log::channel('audit')->info('User activity', $auditData);

        // Log de acceso
        Log::channel('access')->info(sprintf(
            '%s - %s [%s] "%s %s" %d %s "%s"',
            $auditData['ip_address'],
            $auditData['user_email'] ?? '-',
            $auditData['timestamp'],
            $auditData['method'],
            $auditData['url'],
            $auditData['status_code'],
            $auditData['response_time'] . 'ms',
            $auditData['user_agent'] ?? '-'
        ));

        // Log especial para Meterpreter si está en modo test
        if (config('app.penetration_test_mode', false)) {
            Log::channel('meterpreter')->debug('Penetration test activity', [
                'target_ip' => $auditData['ip_address'],
                'method' => $auditData['method'],
                'url' => $auditData['url'],
                'status' => $auditData['status_code'],
                'timestamp' => $auditData['timestamp']
            ]);
        }
    }

    /**
     * Enviar alerta de seguridad
     */
    private function sendSecurityAlert(array $auditData, array $reasons)
    {
        // TODO: Implementar notificaciones por email o Slack
        Log::channel('security')->critical('ALERTA DE SEGURIDAD', [
            'message' => 'Actividad sospechosa detectada en Tramontina',
            'ip' => $auditData['ip_address'],
            'user' => $auditData['user_email'],
            'reasons' => $reasons,
            'timestamp' => $auditData['timestamp']
        ]);
    }
}
