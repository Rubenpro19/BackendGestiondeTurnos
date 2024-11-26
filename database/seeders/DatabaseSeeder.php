<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear los roles en la base de datos
        Rol::factory()->create([
            'nombre_rol' => 'Administrador',
            'descripcion' => 'test@example.com',
        ]);
        Rol::factory()->create([
            'nombre_rol' => 'Nutricionista',
            'descripcion' => 'test@example.com',
        ]);
        Rol::factory()->create([
            'nombre_rol' => 'Paciente',
            'descripcion' => 'test@example.com',
        ]);

        // Crear estados en la base de datos
        Estado::factory()->create([
            'nombre_estado' => 'No tomado',
            'descripcion' => 'Turno no tomado aún.',
        ]);

        Estado::factory()->create([
            'nombre_estado' => 'Terminado',
            'descripcion' => 'Turno ya finalizado.',
        ]);

        Estado::factory()->create([
            'nombre_estado' => 'Reservado',
            'descripcion' => 'Turno reservado por un paciente.',
        ]);

        Estado::factory()->create([
            'nombre_estado' => 'Cancelado',
            'descripcion' => 'Turno cancelado.',
        ]);

        $this->call(UsuarioSeeder::class);

        Usuario::factory()->create([ 
            'rol_id'=> '2',
            'nombre'=> 'dayana',
            'email'=> 'daya@daya.com',
            'password'=> bcrypt('tierrita24')
        ]);

        Usuario::factory()->create([
            'rol_id'=> 3,
            'nombre'=> "Ruben Mera",
            'email' => "dariomera911@gmail.com",
            'password' => bcrypt('Rubenmera190508'),
        ]);


        // Creacion de Usuario, DatosPersonales y de Turnos de prueba
        $this->call([
            DatoPersonalSeeder::class,
            TurnoSeeder::class,
        ]);
    }
}