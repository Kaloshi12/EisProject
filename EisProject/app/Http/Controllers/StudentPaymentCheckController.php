<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentPaymentCheckController extends Controller
{
    public function debit()
    {
        $student = Auth::user();
        $payments = Payment::where('student_id', $student->id)->get();
        $havePaid = 0;
        $degree = Degree::where('id', $student->degree_id);

        if (!$payments) {
            return response()->json(['message' => 'No payment found for this student.'], 404);
        }

        foreach ($payments as $payment) {
            if ($payment->document_id === null) {
                $havePaid += $payment->cost_paid;
            }
        }
        $haveToPay = $degree->cost - $havePaid;
        if ($haveToPay == 0) {
            return response()->json([
                'message' => "'Student had paid the tution for degree:{$degree->name}"
            ]);
        }
        return response()->json([
            'message' => "Student have to pay {$haveToPay}",
            'have_to_pay' => $haveToPay
        ]);
    }
    public function getPaymentsRecord()
    {
        $student = Auth::user();

        $paymentRecords = Payment::where('student_id', $student->id)
            ->with('document')
            ->get();

        $grouped = $paymentRecords->groupBy(function ($record) {
            return Carbon::parse($record->date_payment)->format('d/m/Y');
        });

        $formattedGrouped = $grouped->map(function ($group) {
            return $group->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'cost_paid' => $payment->cost_paid,
                    'iban' => $payment->iban,
                    'date_payment' => $payment->date_payment,
                    'swift_code' => $payment->swift_code,
                    'currency' => $payment->currency,
                    'document_name' => $payment->document ? $payment->document->name : null,
                    'description' => $payment->description,
                ];
            });
        });

        return response()->json([
            'data' => $formattedGrouped
        ]);
    }



}
