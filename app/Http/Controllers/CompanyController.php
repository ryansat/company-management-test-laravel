<?php
// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Requests\CompanyRequest;

class CompanyController extends Controller
{
    public function index()
    {
        return Inertia::render('Companies/Index', [
            'companies' => Company::query()
                ->when(request('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->through(fn ($company) => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,
                    'logo' => $company->logo ? Storage::url($company->logo) : null,
                    'website' => $company->website,
                ]),
        ]);
    }

    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Company::create($data);

        return redirect()->back()->with('success', 'Company created successfully');
    }

    public function update(CompanyRequest $request, Company $company)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($data);

        return redirect()->back()->with('success', 'Company updated successfully');
    }

    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->delete();

        return redirect()->back()->with('success', 'Company deleted successfully');
    }
}

// app/Http/Requests/CompanyRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|dimensions:min_width=100,min_height=100',
            'website' => 'nullable|url',
        ];
    }
}