<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillController extends Controller
{
    public function index()
    {
        return Inertia::render('Bills/Index', [
            'bills' => Bill::query()->get()
        ]);
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        Bill::query()->create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $this->validateRequest($request);

        $bill->fill($validated);
        $bill->save();

        return redirect()->back();
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()->back();
    }
}
