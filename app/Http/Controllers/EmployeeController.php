<?php

// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\Company;
use Inertia\Inertia;

class EmployeeController extends Controller
{
   public function index()
   {
       return Inertia::render('Employees/Index', [
           'employees' => Employee::with('company')
               ->paginate(10)
               ->through(fn ($employee) => [
                   'id' => $employee->id,
                   'first_name' => $employee->first_name,
                   'last_name' => $employee->last_name,
                   'full_name' => $employee->full_name,
                   'email' => $employee->email,
                   'phone' => $employee->phone,
                   'company' => [
                       'id' => $employee->company->id,
                       'name' => $employee->company->name
                   ]
               ]),
           'companies' => Company::select('id', 'name')->get()
       ]);
   }

   public function store(EmployeeRequest $request)
   {
       Employee::create($request->validated());
       return redirect()->back()->with('success', 'Employee added successfully');
   }

   public function update(EmployeeRequest $request, Employee $employee)
   {
       $employee->update($request->validated());
       return redirect()->back()->with('success', 'Employee updated successfully');
   }

   public function destroy(Employee $employee)
   {
       $employee->delete();
       return redirect()->back()->with('success', 'Employee deleted successfully');
   }
}

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->name,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}