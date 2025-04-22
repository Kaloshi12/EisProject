<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentRequestController extends Controller
{
    public function makeDucumentRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_name' => 'required|string'
        ]);
        $inputs = $validator->validated();
        $student = Auth::user();
        $document = Document::where('name', $inputs['document_name']);


        if (!$document) {
            return response()->json(['message' => 'Document not found.'], 404);
        }


        $documentRequest = DocumentRequest::create([
            'student_id' => $student->id,
            'document_id' => $document->id,
            'status' => 'pending'
        ]);
        return response()->json([
            'message' => "Document request for '{$document->name}' has been submitted and is pending.",
            'request_id' => $documentRequest->id,
            'status' => $documentRequest->status
        ], 201);
    }

    public function studentPendingDocumentRequest()
    {
        $student = Auth::user();
        $requests = DocumentRequest::with(['student', 'document'])->where('student_id', $student->id)->where('status', 'pending')->get();
        return response()->json([
            'number_of_request' => "{$requests->count()} Pending Document Request",
            'requests' => $requests
        ]);
    }
    public function studenHistoricDocumentRequest()
    {
        $student = Auth::user();
        $requests = DocumentRequest::with(['student', 'document'])->where('student_id', $student->id)->where('status', 'paid')->get();

        return response()->json([
            'number_of_request' => "{$requests->count()} Pending Document Request",
            'requests' => $requests
        ]);
    }

    public function showPendingDocumentRequest()
    {
        $requests = Document::with(['student', 'document'])->where('status', 'pending')->get();
        return response()->json([
            'number of pending requests' => $requests->count(),
            'pendingRequests' => $requests
        ]);
    }
    public function approveDocumentRequest(string $id)
    {
        $documentRequest = DocumentRequest::find($id);

        if (!$documentRequest) {
            return response()->json(['message' => 'Document request not found.'], 404);
        }

        if ($documentRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be approved.'], 400);
        }
        $documentRequest->update([
            'status' => 'approved'
        ]);
        return response()->json([
            'message' => 'Document request approved successfully.',
            'request' => $documentRequest
        ]);
    }

    public function payDocumentRequest($requestId)
    {
        $docRequest = DocumentRequest::with(['student', 'document'])->find($requestId);

        if (!$docRequest || $docRequest->status !== 'pending') {
            return response()->json(['message' => 'Invalid or already processed request.'], 404);
        }

        $payment = Payment::create([
            'student_id' => $docRequest->student_id,
            'cost_paid' => $docRequest->document->cost,
            'iban' => 'DEFAULT_IBAN',
            'swift_code' => 'DEFAULT_SWIFT',
            'currency' => 'EUR',
            'date_payment' => Carbon::now(),
            'document_id' => $docRequest->document_id,
            'description' => "Payment by " . $docRequest->student->name . " for document: " . $docRequest->document->name

        ]);

        $docRequest->update(['status' => 'paid']);

        return response()->json([
            'message' => 'Payment completed and document request marked as paid.',
            'payment' => $payment
        ]);
    }
    public function getDocumentRequestsByStatus(string $status)
    {
        $documentRequests = DocumentRequest::with(['student', 'document'])->with('status', $status)->get();
        return response()->json([
            'status' => $status,
            'document requested' => $documentRequests
        ]);
    }

}
