<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    public function index()
    {
        // Listar todos los turnos con el paciente_id y el estado correspondiente.
        $turnos = Turno::with('paciente', 'nutricionista', 'estado')->get();
        return response()->json($turnos);
    }

    // Reservar turno
    public function reservar(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return response()->json(["message" => "Usuario no encontrado"], 404);
        }

        DB::beginTransaction(); // Iniciar la transacción

        try {
            $turno = Turno::find($request->turno_id);

            // Verificar que el turno esté libre antes de reservarlo.
            if ($turno && $turno->estado_id == 1) { // 1 = libre
                $turno->update([
                    'estado_id' => 3, // 3 = reservado
                    'paciente_id' => $usuario->id, // Asignar el ID del usuario actual
                ]);

                DB::commit(); // Confirmar la transacción

                return response()->json([
                    "turno" => $turno,
                    "message" => "Turno reservado con éxito"
                ], 200);
            } else {
                DB::rollBack(); // Revertir la transacción si el turno no está disponible
                return response()->json(["message" => "Turno no disponible"], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error
            return response()->json(["message" => "Ocurrió un error al reservar el turno"], 500);
        }
    }

    public function store()
    {
        $diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $horarios = [
            'matutino' => ['08:00', '09:00', '10:00', '11:00'],
            'vespertino' => ['14:00', '15:00', '16:00', '17:00']
        ];

        $fechaActual = now(); // Fecha actual para empezar la creación

        foreach ($diasSemana as $indice => $dia) {
            $fechaTurno = $fechaActual->copy()->addDays($indice); // Fecha del turno incrementada por día

            foreach ($horarios as $bloque) {
                foreach ($bloque as $hora) {
                    Turno::create([
                        'dia' => $dia,
                        'fecha' => $fechaTurno->toDateString(), // Fecha específica del turno
                        'hora' => $hora,
                        'estado_id' => 1, // 1 = libre
                        'nutricionista_id' => 11 // ID del nutricionista (ajustar si es necesario)
                    ]);
                }
            }
        }

        return response()->json(["message" => "Turnos creados con éxito"], 201);
    }


    public function show($id)
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return response()->json(["message" => "Usuario no encontrado"], 404);
        }

        $turno = Turno::with('paciente', 'nutricionista', 'estado')->find($id);

        if (!$turno) {
            return response()->json(["message" => "Turno no encontrado"], 404);
        }

        if ($turno->estado_id == 3) { // 3 = reservado
            return response()->json([
                'id' => $turno->id,
                'fecha' => $turno->fecha,
                'hora' => $turno->hora,
                'estado' => $turno->estado ? [
                    'estado_id' => $turno->estado->id,
                    'nombre_estado' => $turno->estado->nombre_estado,
                    'descripcion' => $turno->estado->descripcion
                ] : null,
                'Paciente' => $turno->paciente ? [
                    'id' => $turno->paciente->id,
                    'nombre' => $turno->paciente->nombre,
                    'email' => $turno->paciente->email,
                ] : null,
                'nutricionista' => $turno->nutricionista ? [
                    'id' => $turno->nutricionista->id,
                    'nombre' => $turno->nutricionista->nombre,
                    'email' => $turno->nutricionista->email,
                ] : null,
                'created_at' => $turno->created_at,
                'updated_at' => $turno->updated_at,
            ]);
        } else {
            return response()->json(["message" => "El turno no está reservado"], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $turno = Turno::find($id);
        if ($turno->estado_id == 3 && $turno->paciente_id == Auth::id()) {
            $nuevoTurno = Turno::find($request->nuevo_turno_id);

            if ($nuevoTurno && $nuevoTurno->estado_id == 1) { // 1 = libre
                $turno->update(['estado_id' => 1, 'paciente_id' => null]); // liberar turno anterior
                $nuevoTurno->update(['estado_id' => 3, 'paciente_id' => Auth::id()]); // reservar nuevo turno

                return response()->json(["message" => "Turno actualizado con éxito"], 200);
            } else {
                return response()->json(["message" => "El nuevo turno no está disponible"], 400);
            }
        } else {
            return response()->json(["message" => "No tienes permiso para actualizar este turno"], 403);
        }
    }

    public function destroy($id)
    {
        $turno = Turno::find($id);
        $usuario = Auth::user();
        if ($turno->estado_id == 3 && $turno->paciente_id == $usuario->id) { // 3 = reservado
            $turno->update(['estado_id' => 1, 'paciente_id' => null]); // liberar turno
            return response()->json(["message" => "Turno cancelado"], 200);
        } else {
            return response()->json(["message" => "El turno no puede ser cancelado"], 400);
        }
    }
}