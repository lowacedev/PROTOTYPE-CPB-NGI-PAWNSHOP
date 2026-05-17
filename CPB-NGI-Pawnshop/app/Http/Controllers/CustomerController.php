<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Region;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = Customer::with('barangay', 'city', 'province', 'region')
            ->latest()
            ->paginate(15);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('customers.create', compact('regions'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        // Handle ID image upload
        if ($request->hasFile('id_image')) {
            $data['id_image_path'] = $request->file('id_image')->store('customer-ids', 'public');
        }

        // Remove id_image from data (it's not a column)
        unset($data['id_image']);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer registered successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load('transactions.items', 'region', 'province', 'city', 'barangay');
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $regions = Region::orderBy('name')->get();
        $customer->load('region', 'province', 'city', 'barangay');
        return view('customers.edit', compact('customer', 'regions'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        // Handle ID image upload
        if ($request->hasFile('id_image')) {
            // Delete old image if exists
            if ($customer->id_image_path) {
                Storage::disk('public')->delete($customer->id_image_path);
            }
            $data['id_image_path'] = $request->file('id_image')->store('customer-ids', 'public');
        }

        // Remove id_image from data
        unset($data['id_image']);

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->transactions()->exists()) {
            return redirect()->route('customers.index')->with('error', 'Cannot delete customer with existing transactions.');
        }

        // Delete ID image if exists
        if ($customer->id_image_path) {
            Storage::disk('public')->delete($customer->id_image_path);
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
