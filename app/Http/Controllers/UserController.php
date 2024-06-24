<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // Método para mostrar todos los usuarios
    public function index()
    {
        $users = User::all();
        return view('User.index', compact('users'));
    }

    // Reglas de validación para las operaciones de creación y actualización
    protected $validationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ];

    // Método para almacenar un nuevo usuario
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada según las reglas de validación definidas
            $request->validate($this->validationRules);

            // Crear un nuevo usuario con los datos recibidos
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Redirigir a la página de índice de usuarios con un mensaje de éxito
            return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
        } catch (ValidationException $e) {
            // Capturar errores de validación y redirigir de regreso con los errores y los datos de entrada
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('modal', 'create');
        }
    }

    // Método para actualizar un usuario existente
    public function update(Request $request, User $user)
    {
        try {
            // Definir las reglas de validación básicas para nombre y correo electrónico
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ];

            // Verificar si se está actualizando la contraseña
            if ($request->filled('password')) {
                $rules['current_password'] = 'required|string|min:8';
                $rules['password'] = 'nullable|string|min:8|confirmed';
            }

            // Validar los datos de entrada según las reglas definidas
            $request->validate($rules);

            // Verificar contraseña actual si se está cambiando la contraseña
            if ($request->filled('password') && !Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->withInput()->with('modal', 'edit');
            }

            // Actualizar los datos del usuario según los campos modificados
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Redirigir a la página de índice de usuarios con un mensaje de éxito
            return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
        } catch (ValidationException $e) {
            // Capturar errores de validación y redirigir de regreso con los errores y los datos de entrada
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('modal', 'edit');
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y redirigir de regreso con un mensaje de error general
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el usuario.'])->withInput()->with('modal', 'edit');
        }
    }

    // Método para eliminar un usuario existente
    public function destroy(User $user)
    {
        try {
            // Eliminar el usuario encontrado por su instancia
            $user->delete();

            // Redirigir a la página de índice de usuarios con un mensaje de éxito
            return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            // Capturar cualquier excepción y redirigir de regreso con un mensaje de error
            return redirect()->route('users.index')->withErrors(['error' => 'Error al eliminar el usuario.']);
        }
    }
}
