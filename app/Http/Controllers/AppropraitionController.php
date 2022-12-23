<?php

namespace App\Http\Controllers;

use App\Models\Appropriation;
use App\Models\Bill;
use App\Models\ObjectExpenditure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AppropraitionController extends Controller
{
    public function index()
    {
        return Inertia::render('Appropriations/Index', [
            'appropriation' => Appropriation::query()
                ->with(['user', 'bill', 'objectExpenditure'])
                ->get(),
            'users' => User::query()->whereNot('id', auth()->user()->id)->get(),
            'bills' => Bill::query()->get(),
            'object_expenditures' => ObjectExpenditure::query()->get()
        ]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'type' => 'required|string|in:employee,utitility',
            'user_id' => ['nullable', 'exists:users,id', Rule::requiredIf($request->type === 'employee')],
            'bill_id' => ['nullable', 'exists:bills,id', Rule::requiredIf($request->type === 'utitility')],
            'object_expenditure_id' => ['required', 'exists:object_expenditures,id'],
            'expense' => 'required|numeric'
        ]);
    }

    public function store(Request $request)
    {

        $validated = $this->validateRequest($request);

        $objectExpenditure = ObjectExpenditure::query()->firstWhere('id', $request->object_expenditure_id);

        if ($request->expense >  $objectExpenditure->balance) {
            throw ValidationException::withMessages([
                'expense' => 'Insufficient balance',
            ]);
        }

        Appropriation::query()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Appropriation $appropriation)
    {
        // $validated = $this->validateRequest($request);

        // $appropriation->fill($validated);
        // $appropriation->save();

        return redirect()->back();
    }

    public function destroy(Appropriation $appropriation)
    {
        $appropriation->delete();

        return redirect()->back();
    }
}
