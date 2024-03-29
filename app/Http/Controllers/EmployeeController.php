<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Employees/Index', [
            'employees' => User::query()
                ->whereNot('id', auth()->user()->id)
                ->search($request->search)
                ->paginate()
        ]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|unique:users,name,' . $request->id,
            'status' => 'required|boolean'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        User::query()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, User $employee)
    {
        $validated = $this->validateRequest($request);

        $employee->fill($validated);
        $employee->save();

        return redirect()->back();
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->back();
    }
}
