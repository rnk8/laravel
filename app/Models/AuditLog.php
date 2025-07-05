<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'user_email',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'route',
        'status_code',
        'response_time',
        'session_id',
        'csrf_token',
        'request_data',
        'headers',
        'is_suspicious',
        'suspicious_reasons',
        'audit_level',
        'country',
        'city',
        'is_penetration_test',
        'test_type',
        'timestamp'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'request_data' => 'array',
        'headers' => 'array',
        'suspicious_reasons' => 'array',
        'is_suspicious' => 'boolean',
        'is_penetration_test' => 'boolean',
        'response_time' => 'decimal:2',
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el modelo User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener logs sospechosos
     */
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    /**
     * Scope para obtener logs de penetration testing
     */
    public function scopePenetrationTest($query)
    {
        return $query->where('is_penetration_test', true);
    }

    /**
     * Scope para obtener logs por nivel de auditoría
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('audit_level', $level);
    }

    /**
     * Scope para obtener logs por IP
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope para obtener logs de errores (4xx, 5xx)
     */
    public function scopeErrors($query)
    {
        return $query->where('status_code', '>=', 400);
    }

    /**
     * Scope para obtener logs de un período específico
     */
    public function scopeInTimeRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Obtener logs de los últimos N minutos
     */
    public function scopeLastMinutes($query, $minutes = 60)
    {
        return $query->where('created_at', '>=', Carbon::now()->subMinutes($minutes));
    }

    /**
     * Obtener logs de hoy
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Obtener estadísticas de IPs más activas
     */
    public static function topActiveIps($limit = 10, $hours = 24)
    {
        return static::select('ip_address')
            ->selectRaw('COUNT(*) as requests_count')
            ->selectRaw('COUNT(CASE WHEN status_code >= 400 THEN 1 END) as error_count')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->groupBy('ip_address')
            ->orderByDesc('requests_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de actividad sospechosa
     */
    public static function suspiciousActivity($hours = 24)
    {
        return static::select('ip_address', 'user_email', 'suspicious_reasons')
            ->selectRaw('COUNT(*) as incidents_count')
            ->where('is_suspicious', true)
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->groupBy('ip_address', 'user_email', 'suspicious_reasons')
            ->orderByDesc('incidents_count')
            ->get();
    }

    /**
     * Obtener estadísticas de métodos HTTP
     */
    public static function httpMethodStats($hours = 24)
    {
        return static::select('method')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('AVG(response_time) as avg_response_time')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->groupBy('method')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Obtener logs de actividad de Meterpreter
     */
    public static function meterpreterActivity($hours = 24)
    {
        return static::where('test_type', 'meterpreter')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Detectar patrones de ataque
     */
    public static function attackPatterns($hours = 24)
    {
        $patterns = [
            'sql_injection' => ['union select', 'drop table', 'insert into', '1=1'],
            'xss' => ['<script', 'javascript:', 'onerror=', 'onload='],
            'path_traversal' => ['../', '..\\', '/etc/passwd', '\\windows\\'],
            'command_injection' => ['cmd=', 'exec=', '`', ';ls', '|cat'],
        ];

        $results = [];

        foreach ($patterns as $pattern_name => $keywords) {
            $query = static::where('created_at', '>=', Carbon::now()->subHours($hours));
            
            foreach ($keywords as $keyword) {
                $query->orWhere('url', 'LIKE', "%{$keyword}%");
            }

            $results[$pattern_name] = $query->count();
        }

        return $results;
    }

    /**
     * Generar reporte de auditoría
     */
    public static function auditReport($start_date, $end_date)
    {
        return [
            'period' => [
                'start' => $start_date,
                'end' => $end_date,
            ],
            'summary' => [
                'total_requests' => static::inTimeRange($start_date, $end_date)->count(),
                'unique_ips' => static::inTimeRange($start_date, $end_date)->distinct('ip_address')->count(),
                'error_requests' => static::inTimeRange($start_date, $end_date)->errors()->count(),
                'suspicious_activities' => static::inTimeRange($start_date, $end_date)->suspicious()->count(),
                'penetration_tests' => static::inTimeRange($start_date, $end_date)->penetrationTest()->count(),
            ],
            'top_ips' => static::topActiveIps(10, Carbon::parse($end_date)->diffInHours($start_date)),
            'attack_patterns' => static::attackPatterns(Carbon::parse($end_date)->diffInHours($start_date)),
            'suspicious_activities' => static::suspiciousActivity(Carbon::parse($end_date)->diffInHours($start_date)),
        ];
    }

    /**
     * Obtener información geográfica de la IP (placeholder)
     */
    public function getLocationAttribute()
    {
        // Aquí se podría integrar con servicios como GeoIP
        return $this->country && $this->city ? "{$this->city}, {$this->country}" : 'Unknown';
    }

    /**
     * Verificar si el log es de horario no laboral
     */
    public function getIsAfterHoursAttribute()
    {
        $hour = $this->created_at->hour;
        return $hour < 7 || $hour > 19;
    }

    /**
     * Obtener el nivel de riesgo basado en criterios
     */
    public function getRiskLevelAttribute()
    {
        $risk_score = 0;

        if ($this->is_suspicious) $risk_score += 30;
        if ($this->status_code >= 400) $risk_score += 20;
        if ($this->is_after_hours) $risk_score += 15;
        if ($this->response_time > 1000) $risk_score += 10;

        if ($risk_score >= 50) return 'high';
        if ($risk_score >= 30) return 'medium';
        if ($risk_score >= 15) return 'low';
        
        return 'normal';
    }
}
