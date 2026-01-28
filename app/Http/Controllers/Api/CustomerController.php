<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET: List Customer
    public function index()
    {
        return response()->json(Customer::all());
    }

    // POST: Add Customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'CustID' => 'required|unique:dbo.Customer,CustID|max:11',
            'Name' => 'required|max:17',
            'Address' => 'required|max:37',
            'BranchCode' => 'required|max:3',
            'PhoneNo' => 'required|max:14',
        ]);

        $customer = Customer::create($validated);
        return response()->json(['message' => 'Customer created', 'data' => $customer], 201);
    }

    // DELETE: Delete Customer
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['message' => 'Not found'], 404);
        
        $customer->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}