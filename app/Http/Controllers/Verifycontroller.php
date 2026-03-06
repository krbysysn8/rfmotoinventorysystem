<?php

namespace App\Http\Controllers;

use App\Models\VerifyAction;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifyController extends Controller
{
    // GET /api/verify  (Admin only)
    public function index(): JsonResponse
    {
        $actions = VerifyAction::with(['product', 'requestedBy'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();
        return response()->json(['status' => 'success', 'actions' => $actions]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $action = VerifyAction::findOrFail($id);

        DB::transaction(function () use ($request, $action) {
            $action->update([
                'status'      => 'approved',
                'reviewed_by' => $request->user()->user_id,
                'reviewed_at' => now(),
                'review_notes'=> $request->notes,
            ]);

            if ($action->action_type === 'Large Stock Out' && $action->product_id) {
                preg_match('/(\d+)\s*units/', $action->details, $m);
                $qty = (int)($m[1] ?? 0);

                if ($qty > 0) {
                    $product = Product::lockForUpdate()->find($action->product_id);
                    $before  = $product->stock_qty;
                    $product->decrement('stock_qty', $qty);

                    StockMovement::create([
                        'product_id'      => $action->product_id,
                        'movement_type'   => 'out',
                        'quantity'        => $qty,
                        'qty_before'      => $before,
                        'qty_after'       => $before - $qty,
                        'reference_no'    => 'VA-APPROVED-' . $action->verify_id,
                        'notes'           => $action->details,
                        'performed_by'    => $request->user()->user_id,
                        'requires_verify' => true,
                        'verified_by'     => $request->user()->user_id,
                        'verified_at'     => now(),
                        'movement_date'   => today(),
                    ]);
                }
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Action approved and executed.']);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $action = VerifyAction::findOrFail($id);
        $action->update([
            'status'       => 'rejected',
            'reviewed_by'  => $request->user()->user_id,
            'reviewed_at'  => now(),
            'review_notes' => $request->notes,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Action rejected.']);
    }
}