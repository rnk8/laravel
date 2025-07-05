<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Información del usuario
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_email')->nullable();
            
            // Información de la request
            $table->string('ip_address', 45); // Soporta IPv6
            $table->text('user_agent')->nullable();
            $table->string('method', 10);
            $table->text('url');
            $table->string('route')->nullable();
            
            // Información de la response
            $table->integer('status_code');
            $table->decimal('response_time', 8, 2)->nullable(); // en milisegundos
            
            // Información de sesión
            $table->string('session_id')->nullable();
            $table->string('csrf_token')->nullable();
            
            // Datos adicionales
            $table->json('request_data')->nullable();
            $table->json('headers')->nullable();
            
            // Información de auditoría
            $table->boolean('is_suspicious')->default(false);
            $table->json('suspicious_reasons')->nullable();
            $table->string('audit_level', 20)->default('info'); // info, warning, error, critical
            
            // Información geográfica (opcional)
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            
            // Información de penetration testing
            $table->boolean('is_penetration_test')->default(false);
            $table->string('test_type')->nullable(); // meterpreter, manual, automated
            
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index(['is_suspicious', 'created_at']);
            $table->index(['method', 'url']);
            $table->index('audit_level');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
