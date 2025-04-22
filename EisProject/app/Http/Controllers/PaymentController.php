<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Document;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'Payments' => Payment::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_payment' => 'required|date',
            'cost_paid' => 'required|numeric',
            'iban' => 'required|string|size:8',
            'swift_code' => 'required|string',
            'currency' => 'required|string|size:3',
            'document_name' => 'required|string',
            'id_cart_number' => 'required|string|regex:/^[A-Z][0-9]{8}[A-Z]$/',
            'description' => 'required|string'

        ]);
        $inputs = $validator->validated();
        $student = User::where('id_cart_number', $inputs['id_card_number'])->where('role_id', config('constants.STUDENT_ROLE_ID'))->first();
        if (!$student) {
            return response()->json([
                'message' => 'There are no student with this card id'
            ], 404);
        }
        $document = Document::where('name', $inputs['document_name']);
        $payment = Payment::create([
            'date_payment' => $inputs['date_payment'],
            'cost_paid' => $inputs['cost_paid'],
            'iban' => $inputs['iban'],
            'swift_code' => $inputs['swift_code'],
            'currency' => $inputs['currency'],
            'document_id' => $document->id,
            'student_id' => $student->id,
            'description' => $inputs['description'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment created successfully',
            'payment' => $payment,
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [

            'cost_paid' => 'nullable|numeric',
            'iban' => 'nullable|string|size:8',
            'swift_code' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'degree_name' => 'nullable|string',
            'description' => 'nullable|string'

        ]);
        $inputs = $validator->validated();
        $checkCostPaid = 0;
        $payment = Payment::find($id);
        if (isset($inputs['degree_name'])) {
            $degree = Degree::wehere('name', $inputs['degree_name'])->first();
            if (!$degree) {
                return response()->json([
                    'message' => 'There are no degree with this name'
                ], 404);
                if (isset($inputs['cost_paid']) && ($inputs['cost_paid'] >= 0 || $inputs['cost_paid'] <= $degree->cost_paid)) {
                    $payment->cost_paid = $inputs['cost_paid'];
                } else {
                    $halfCost = $degree->cost_paid / 2;
                    return response()->json([
                        'message' => "The cost_paid must be between 0 and {$degree->cost_paid}."
                    ], 422);
                }
            }
        }


        if (!$payment) {
            return response()->json([
                'message' => 'There are no redord with this id'
            ]);
        }

        $payment->update($inputs);

        return response()->json([
            'message' => 'Record updated successfully.',
            'record' => $payment
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json([
                'message' => 'There are no redord with this id'
            ]);
        }
        $payment->delete();
        return response()->json([
            'message' => 'Record deleted successfully.',
            'record' => $payment
        ]);
    }
}
