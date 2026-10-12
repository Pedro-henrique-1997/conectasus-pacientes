<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class PatientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'digits:11',
                Rule::unique('patients', 'cpf'),
            ],
            'birth_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'health_unit_id' => [
                'required',
                'integer',
                'exists:health_units,id',
            ],
        ]);

        $patient = Patient::create($validated);

        return (new PatientResource($patient))
            ->response()
            ->setStatusCode(201);
    }


    

public function index(Request $request)
{
    $search = $request->query('search');

    $patients = Patient::with('healthUnit')
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        })
        ->orderBy('name')
        ->paginate(5)
        ->withQueryString();

    return PatientResource::collection($patients);
}


public function show(int $id)
{
    $patient = Patient::with('healthUnit')->findOrFail($id);

    return new PatientResource($patient);
}



public function update(Request $request, int $id)
{
    $patient = Patient::findOrFail($id);

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'cpf' => [
            'required',
            'string',
            'digits:11',
            Rule::unique('patients', 'cpf')->ignore($patient->id),
        ],
        'birth_date' => [
            'required',
            'date',
            'before_or_equal:today',
        ],
        'phone' => ['nullable', 'string', 'max:20'],
        'health_unit_id' => [
            'required',
            'integer',
            'exists:health_units,id',
        ],
    ]);

    $patient->update($validated);

    return new PatientResource($patient->fresh());
}



public function destroy(int $id)
{
    $patient = Patient::findOrFail($id);

    $patient->delete();

    return response()->json([
        'message' => 'Paciente excluído com sucesso.',
    ]);
}



}

