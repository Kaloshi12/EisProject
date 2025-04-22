<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'documents' => Document::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'cost' => "required|numeric"
        ]);
        $inputs = $validator->validated();
        $document = Document::create($inputs);
        return response()->json([
            'message' => 'document create successfully',
            'document' => $document
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
            'cost' => "nullable|numeric"
        ]);
        $inputs = $validator->validated();
        $document = Document::find($id);
        if (isset($inputs['cost'])) {
            $document->cost = $inputs['cost'];
        }
        return response()->json([
            'message' => 'document updated successfully',
            'document' => $document
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $document = Document::find($id);
        $document->delete();
        return response()->json([
            'message' => 'document deleted successfully',
            'document' => $document
        ]);
    }
}
