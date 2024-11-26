<?php

use App\Http\Controllers\AtencionController;
use App\Http\Controllers\DatoPersonalController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurnoController;

// Rutas para autenticación
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout'])->middleware('auth:sanctum');

// Rutas para UsuarioController
Route::get('/user', [UsuarioController::class, 'index']); //Ver todos los usuario
Route::post('/user', [UsuarioController::class, 'store']); //Ver usuario específico
Route::get('/user/{id}', [UsuarioController::class, 'registro_unico']); //Crear un usuario
Route::put('/user/{id}', [UsuarioController::class, 'update']); //Actualiza un usuario
Route::delete('/user/{id}', [UsuarioController::class, 'destroy']); //Borra un usuario

// Rutas para DatoPersonalController
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/datos-personales', [DatoPersonalController::class, 'index']);
    Route::post('/datos-personales', [DatoPersonalController::class, 'store']);
    Route::get('/datos-personales/{id}', [DatoPersonalController::class, 'show']);
    Route::put('/datos-personales', [DatoPersonalController::class, 'update']);
    Route::delete('/datos-personales', [DatoPersonalController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/turnos', [TurnoController::class, 'index']);// Obtener todos los turnos
    Route::post('/turnos/reservar', [TurnoController::class, 'reservar']);// Reservar un turno
    Route::post('/turnos/crear', [TurnoController::class, 'store']);// Crear turnos automáticamente
    Route::get('/turnos/{id}', [TurnoController::class, 'show']);// Ver un turno específico
    Route::put('/turnos/{id}', [TurnoController::class, 'update']);// Actualizar un turno reservado
    Route::delete('/turnos/{id}', [TurnoController::class, 'destroy']);// Cancelar un turno reservado
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/atenciones', [AtencionController::class, 'index']); // Para listar atenciones de los pacientes
    Route::get('/atenciones/{id}', [AtencionController::class, 'show']); // Para ver una atención específica
    Route::put('/atenciones/{id}', [AtencionController::class, 'actualizar']);//actualiza una atecion ya hecha por el nutricionista
    Route::delete('/atenciones/{id}', [AtencionController::class, 'eliminar']);//elimina una atencion hecha por el paciente
    Route::post('/atenciones', [AtencionController::class, 'crearStore']);//crear un atencion del nutricionista  a  el paciente ligado al turno
});

