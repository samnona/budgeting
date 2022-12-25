<?php

namespace App\Http\Controllers;

use App\Models\ObjectExpenditure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ObjectExpenditureController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('ObjectExpenditures/Index', [
            'object_expenditures' => ObjectExpenditure::query()
                ->search($request->search)
                ->paginate()
        ]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'expenditures' => ['required', 'string', Rule::unique('object_expenditures')
                ->where('year', $request->year)
                ->ignore($request->id)],
            'account_code' => 'required|string|unique:object_expenditures,account_code,' . $request->id,
            'budget' => 'required|regex:/[\d]{2},[\d]{2}/',
            'balance' => 'nullable|regex:/[\d]{2},[\d]{2}/',
            'year' => 'nullable'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['year'] =  $request->input('year', now()->format('Y'));
        $validated['balance'] =  $request->budget;

        ObjectExpenditure::query()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, ObjectExpenditure $expenditures)
    {
        // $validated = $this->validateRequest($request);

        // $expenditures->fill($validated);
        // $expenditures->save();

        return redirect()->back();
    }

    public function destroy(ObjectExpenditure $expenditure)
    {
        $expenditures->delete();

        return redirect()->back();
    }
}
