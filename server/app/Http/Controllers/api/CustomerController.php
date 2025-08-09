<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index(Request $request)
        {
            $limit = $request->input('limit', 10);

            $sort = $request->input('sort', 'asc');
            if (!in_array($sort, ['asc', 'desc'])) {
                $sort = 'asc';
            }

            $customers = Customer::select(
                'id',
                'name',
                'date_of_birth',
                'address',
                'email',
                'contact_number'
            )
            ->orderBy('name',$sort)
            ->paginate($limit);

            $customers->getCollection()->transform(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'date_of_birth' => $customer->date_of_birth,
                    'address' => $customer->address,
                    'email' => $customer->email,
                    'contact_number' => $customer->contact_number,
                ];
            });

            return response()->json([

                'status' => 200,
                'message' => 'Customers retrieved successfully',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                ],
            ]);
        }

        /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return response()->json([
            'status' => 201,
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Customer retrieved successfully',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'date_of_birth' => $customer->date_of_birth,
                'address' => $customer->address,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Customer retrieved successfully',
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'date_of_birth' => $customer->date_of_birth,
                'address' => $customer->address,
                'email' => $customer->email,
                'contact_number' => $customer->contact_number,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Customer deleted successfully',
        ]);
    }
}
