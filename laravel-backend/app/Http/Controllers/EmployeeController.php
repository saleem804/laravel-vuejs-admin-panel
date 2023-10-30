<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return EmployeeResource::collection(Employee::with('company')->orderBy('created_at', 'desc')->paginate(10));
    }

    public function store(StoreEmployeeRequest $request)
    {
         $employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'company_id' => $request->company_id,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return new EmployeeResource($employee);
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $data_to_update = $request->only(['first_name', 'last_name', 'company_id', 'email', 'phone']);
        $employee->update($data_to_update);

        return new EmployeeResource($employee);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(null, 204);
    }
}
