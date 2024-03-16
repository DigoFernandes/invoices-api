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

        if ($validator->fails()) {
            return $this->error('Data Invalid', 422, $validator->errors());
        }

        $created = Invoices::create($validator->validated());

        //o ->load() é utilizado para fazer uma chamada de um relacionamento. Ou seja, não poderia colocar with('id") pois não acharia a relação.
        if($created){
            return $this->response('Invoice Created', 200, new InvoiceResource($created->load('user')));
        }
        
        return $this->error('Something Went Wrong',400);
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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type'=> 'required|max:1',
            'paid'=> 'required|numeric|between:0,1',
            'value' => 'required|numeric',
            'payment_date' => 'nullable|date_format:Y-m-d H-i-s'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Failed', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $updated = Invoices::find($id)->update([
            'user_id' => $validated['user_id'],
            'type'=> $validated['type'],
            'paid'=> $validated['paid'],
            'value'=> $validated['value'],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
