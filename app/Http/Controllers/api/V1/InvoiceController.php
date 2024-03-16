<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoices;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Http\Resources\V1\InvoiceResourceCollection;
use Illuminate\Support\Facades\Validator;
class InvoiceController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new InvoiceResourceCollection(Invoices::with('user')->get());
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), rules:[
            'user_id' => 'required',
            'type' => 'required|max:1',
            'paid' => 'required|numeric|between:0,1',
            'payment_date' => 'nullable',
            'value' => 'required|numeric|between:1,9999.99'
        ]);

        if($validator->fails()){
            return $this-> errors('Data Invalid', 422, $validator->errors());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new InvoiceResource(resource: Invoices::where('id', $id)->first());
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
