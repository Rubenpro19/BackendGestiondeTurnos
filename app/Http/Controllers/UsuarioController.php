<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    // Obtener todos los usuarios
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios);
    }

    // Registro único de un usuario
    public function registro_unico($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    // Registrar un usuario
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'email' => 'required|email|unique:usuario',
            'password' => 'required',
            'rol_id' => 'sometimes|exists:rol,id'// sometimes porque por defecto el rol es 3 (Paciente)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al crear usuario',
                'errors' => $validator->errors(),
            ], 400);
        }

        $rol_id = $request->input('rol_id', 3);//Por defecto el usuario se crea con el rol de Paciente(3)

        $usuario = Usuario::create([
            'rol_id' => $rol_id,
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encriptar contraseña
        ]);

        return response()->json([
            'usuario' => $usuario,
            'message' => 'Usuario creado exitosamente',
        ], 201);
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rol_id' => 'sometimes|exists:rol,id',
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:usuario,email,' . $usuario->id,
            'password' => 'sometimes|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $usuario->update([
            'rol_id' => $request->rol_id ?? $usuario->rol_id,
            'nombre' => $request->nombre ?? $usuario->nombre,
            'email' => $request->email ?? $usuario->email,
            'password' => $request->password ? bcrypt($request->password) : $usuario->password,
        ]);

        return response()->json($usuario, 200);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    // Login de usuario
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $usuario = Auth::user(); // Usa Auth::user() para obtener el usuario autenticado
            $token = $usuario->createToken('API TOKEN')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => $usuario,
                'token' => $token,
            ], 200);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Logout del usuario
    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete(); // Eliminar todos los tokens del usuario autenticado

        return response()->json(['message' => 'Cierre de sesión exitoso'], 200);
    }
}