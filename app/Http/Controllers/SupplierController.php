<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers');
    }

    // ── GET /api/suppliers ────────────────────────────────────
    public function list(Request $request): JsonResponse
    {
        $query = Supplier::query()->orderBy('supplier_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(supplier_name) LIKE ?',    [strtolower("%{$search}%")])
                  ->orWhereRaw('LOWER(contact_person) LIKE ?', [strtolower("%{$search}%")])
                  ->orWhereRaw('LOWER(email) LIKE ?',          [strtolower("%{$search}%")])
                  ->orWhereRaw('LOWER(address) LIKE ?',        [strtolower("%{$search}%")]);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->get()->map(fn($s) => [
            'supplier_id'    => $s->supplier_id,
            'supplier_name'  => $s->supplier_name,
            'contact_person' => $s->contact_person,
            'phone'          => $s->phone,
            'email'          => $s->email,
            'address'        => $s->address,
            'status'         => $s->status,
        ]);

        return response()->json(['status' => 'success', 'suppliers' => $suppliers]);
    }

    // ── POST /api/suppliers ───────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_name'  => 'required|string|max:150|unique:suppliers,supplier_name',
            'contact_person' => 'nullable|string|max:120',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:120',
            'address'        => 'nullable|string|max:500',
            'status'         => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::create([
            'supplier_name'  => $request->supplier_name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'status'         => $request->status ?? 'active',
        ]);

        ActivityLog::record(
            action:      'supplier',
            subject:     $supplier->supplier_name,
            description: "New supplier registered." . ($supplier->contact_person ? " Contact: {$supplier->contact_person}." : ''),
            user:        $request->user(),
        );

        return response()->json([
            'status'   => 'success',
            'message'  => 'Supplier added.',
            'supplier' => [
                'supplier_id'    => $supplier->supplier_id,
                'supplier_name'  => $supplier->supplier_name,
                'contact_person' => $supplier->contact_person,
                'phone'          => $supplier->phone,
                'email'          => $supplier->email,
                'address'        => $supplier->address,
                'status'         => $supplier->status,
            ],
        ], 201);
    }

    // ── PUT /api/suppliers/{id} ───────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['status' => 'error', 'message' => 'Supplier not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'supplier_name'  => "required|string|max:150|unique:suppliers,supplier_name,{$id},supplier_id",
            'contact_person' => 'nullable|string|max:120',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:120',
            'address'        => 'nullable|string|max:500',
            'status'         => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $supplier->update([
            'supplier_name'  => $request->supplier_name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'status'         => $request->status ?? $supplier->status,
        ]);

        ActivityLog::record(
            action:      'item_updated',
            subject:     $request->supplier_name,
            description: "Supplier details updated.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Supplier updated.']);
    }

    // ── DELETE /api/suppliers/{id} ────────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json(['status' => 'error', 'message' => 'Supplier not found.'], 404);
        }

        $name = $supplier->supplier_name;
        $supplier->delete();

        ActivityLog::record(
            action:      'deleted',
            subject:     $name,
            description: "Supplier deleted.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Supplier deleted.']);
    }
}