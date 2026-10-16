<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of patients with search + pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                $query->where('patient_number', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalPatients = Patient::count();

        return view('patients.index', compact('patients', 'search', 'totalPatients'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_number' => ['required', 'string', 'max:50', 'unique:patients,patient_number'],
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'birth_date'     => ['required', 'date', 'before_or_equal:today'],
            'sex'            => ['required', Rule::in(['Male', 'Female'])],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address'        => ['nullable', 'string', 'max:2000'],
        ]);

        Patient::create($validated);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'patient_number' => [
                'required', 'string', 'max:50',
                Rule::unique('patients', 'patient_number')->ignore($patient->id),
            ],
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'birth_date'     => ['required', 'date', 'before_or_equal:today'],
            'sex'            => ['required', Rule::in(['Male', 'Female'])],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address'        => ['nullable', 'string', 'max:2000'],
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient record updated successfully.');
    }

    /**
     * Remove the specified patient.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient record deleted successfully.');
    }
}