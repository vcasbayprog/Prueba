<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class HolidayController extends Controller
{
    // Reglas de validación para las operaciones de creación y actualización
    protected $validationRules = [
        'name' => 'required|string|max:255',
        'color' => 'nullable|string|max:20',
        'date' => 'required|date_format:Y-m-d',
        'recurrent' => 'boolean',
    ];

    // Método para mostrar todos los días festivos
    public function index(Request $request)
    {
        try {
            $holidays = Holiday::all();

            // Si la solicitud espera una respuesta JSON, devolver los días festivos como JSON
            if ($request->expectsJson()) {
                return response()->json($holidays);
            }

            // Si no es una solicitud JSON, devolver la vista con los días festivos
            return view('holidays.index', compact('holidays'));
        } catch (\Exception $e) {
            // Capturar cualquier excepción y redirigir de regreso con un mensaje de error
            return redirect()->back()->withErrors(['error' => 'Error al obtener los días festivos.'])->withInput();
        }
    }


    // Método para almacenar un nuevo día festivo
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada según las reglas de validación definidas
            $request->validate($this->validationRules);

            // Crear un nuevo día festivo con los datos recibidos
            $holiday = Holiday::create([
                'name' => $request->input('name'),
                'color' => $request->input('color'),
                'date' => $request->input('date'),
                'recurrent' => $request->has('recurrent'),
            ]);

            // Redirigir a la página de índice de días festivos con un mensaje de éxito
            return redirect()->route('holidays.index')->with('success', 'Día festivo creado exitosamente.');
        } catch (ValidationException $e) {
            // Capturar errores de validación y redirigir de regreso con los errores y los datos de entrada
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('modal', 'create');
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y redirigir de regreso con un mensaje de error general
            return redirect()->back()->withErrors(['error' => 'Error al crear el día festivo.'])->withInput()->with('modal', 'create');
        }
    }

  

    // Método para actualizar un día festivo existente
    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada según las reglas de validación definidas
            $request->validate($this->validationRules);

            // Buscar el día festivo por su ID; lanzará ModelNotFoundException si no se encuentra
            $holiday = Holiday::findOrFail($id);
            // Actualizar los campos del día festivo con los nuevos valores recibidos
            $holiday->fill([
                'name' => $request->input('name'),
                'color' => $request->input('color'),
                'date' => $request->input('date'),
                'recurrent' => $request->has('recurrent'),
            ])->save();

            // Redirigir a la página de índice de días festivos con un mensaje de éxito
            return redirect()->route('holidays.index')->with('success', 'Día festivo actualizado exitosamente.');
        } catch (ValidationException $e) {
            // Capturar errores de validación y redirigir de regreso con los errores y los datos de entrada
            return redirect()->back()->withErrors($e->validator->errors())->withInput()->with('modal', 'edit');
        } catch (ModelNotFoundException $e) {
            // Capturar excepción si el día festivo no se encuentra y redirigir con un mensaje de error
            return redirect()->route('holidays.index')->withErrors(['error' => 'Día festivo no encontrado.']);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y redirigir de regreso con un mensaje de error general
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el día festivo.'])->withInput()->with('modal', 'edit');
        }
    }

    // Método para eliminar un día festivo existente
    public function destroy($id)
    {
        try {
            // Buscar el día festivo por su ID; lanzará ModelNotFoundException si no se encuentra
            $holiday = Holiday::findOrFail($id);
            // Eliminar el día festivo encontrado
            $holiday->delete();

            // Redirigir a la página de índice de días festivos con un mensaje de éxito
            return redirect()->route('holidays.index')->with('success', 'Día festivo eliminado exitosamente.');
        } catch (ModelNotFoundException $e) {
            // Capturar excepción si el día festivo no se encuentra y redirigir con un mensaje de error
            return redirect()->route('holidays.index')->withErrors(['error' => 'Día festivo no encontrado.']);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción y redirigir de regreso con un mensaje de error general
            return redirect()->route('holidays.index')->withErrors(['error' => 'Error al eliminar el día festivo.']);
        }
    }

    // Método para mostrar el dashboard con todos los días festivos
    public function dashboard()
    {
        try {
            // Obtener todos los días festivos y pasarlos a la vista del dashboard
            $holidays = Holiday::all();
            return view('dashboard', compact('holidays'));
        } catch (\Exception $e) {
            // Capturar cualquier excepción y redirigir de regreso con un mensaje de error
            return redirect()->back()->withErrors(['error' => 'Error al cargar el dashboard.']);
        }
    }

    // Método para obtener los días festivos para un año específico
    public function getHolidaysForYear(Request $request)
    {
        try {
            // Obtener el año desde la solicitud o usar el año actual si no se proporciona
            $year = $request->query('year', date('Y'));
            
            // Obtener todos los días festivos y filtrar según el año
            $holidays = Holiday::all()->flatMap(function ($holiday) use ($year) {
                $results = [];
                $holidayYear = Carbon::parse($holiday->date)->year;

                // Si el día festivo es recurrente, ajustar la fecha al año solicitado
                if ($holiday->recurrent) {
                    $results[] = [
                        'name' => $holiday->name,
                        'color' => $holiday->color,
                        'date' => Carbon::parse($holiday->date)->year($year)->format('Y-m-d'),
                    ];
                } elseif ($holidayYear == $year) {
                    $results[] = $holiday; // Incluir el día festivo si pertenece al año solicitado
                }

                return $results;
            });

            // Devolver los días festivos filtrados como respuesta JSON
            return response()->json($holidays);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y devolver una respuesta JSON con un error 500
            return response()->json(['error' => 'Error al obtener los días festivos para el año.'], 500);
        }
    }
}
