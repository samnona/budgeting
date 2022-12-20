<?php

namespace App\Http\Controllers;

use App\Models\ObjectExpenditure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ObjectExpenditureController extends Controller
{
    public function index()
    {
        return Inertia::render('ObjectExpenditures/Index', [
            'object_expenditures' => ObjectExpenditure::query()->get()
        ]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'expenditures' => 'required|string',
            'account_code' => 'required|string|unique:object_expenditures,account_code,' . $request->id,
            'budget' => 'required|numeric',
            'balance' => 'nullable|numeric',
            'year' => 'required'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['balance'] =  $request->budget;

        ObjectExpenditure::query()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, ObjectExpenditure $objectExpenditure)
    {
        // $validated = $this->validateRequest($request);

        // $objectExpenditure->fill($validated);
        // $objectExpenditure->save();

        return redirect()->back();
    }

    public function destroy(ObjectExpenditure $objectExpenditure)
    {
        $objectExpenditure->delete();

        return redirect()->back();
    }
}
