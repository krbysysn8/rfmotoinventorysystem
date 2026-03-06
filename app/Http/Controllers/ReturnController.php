<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\ReturnedItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(): JsonResponse
    {
        $returns = ReturnRequest::with(['product', 'order'])
            ->orderByDesc('created_at')->get();
        return response()->json(['status' => 'success', 'returns' => $returns]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id'       => 'nullable|exists:sales_orders,order_id',
            'product_id'     => 'required|exists:products,product_id',
            'quantity'       => 'required|integer|min:1',
            'reason'         => 'required|string',
            'condition_type' => 'required|in:damaged,incomplete,defective,wrong_item,other',
        ]);

        $returnNo = 'RT-' . str_pad(ReturnRequest::max('return_id') + 1, 3, '0', STR_PAD_LEFT);

        $ret = ReturnRequest::create([
            'return_number'  => $returnNo,
            'order_id'       => $request->order_id,
            'product_id'     => $request->product_id,
            'quantity'       => $request->quantity,
            'reason'         => $request->reason,
            'condition_type' => $request->condition_type,
            'requested_by'   => $request->user()->user_id,
            'status'         => 'pending',
            'return_date'    => today(),
        ]);

        return response()->json(['status' => 'success', 'return' => $ret], 201);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $ret = ReturnRequest::findOrFail($id);
        $ret->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->user_id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Return approved.']);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $ret = ReturnRequest::findOrFail($id);
        $ret->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->user_id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Return rejected.']);
    }
}