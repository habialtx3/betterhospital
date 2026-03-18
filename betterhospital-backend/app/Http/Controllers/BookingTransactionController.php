<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\BookingTransaction;
use App\Services\BookingTransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    //
    private $bookingTransactionService;

    public function __construct(BookingTransactionService $bookingTransactionService)
    {
        $this->bookingTransactionService = $bookingTransactionService;
    }

    public function index()
    {
        $fields = [
            'user_id',
            'doctor_id',
            'status',
            'started_at',
            'time_at',
            'sub_total',
            'tax_total',
            'grand_total'
        ];
        $transactions = $this->bookingTransactionService->getAll($fields);
        return response()->json(TransactionResource::collection($transactions));
    }

    public function show($id)
    {
        try {
            $transaction = $this->bookingTransactionService->getByIdForManager($id);
            return response()->json(new TransactionResource($transaction));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Transactions not found'
            ], 404);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);
        try {
            $transaction = $this->bookingTransactionService->updateStatus($id,$validated['status']);
            return response()->json([
                'message' => 'Transaction status updated successfully',
                'data' => new TransactionResource($transaction)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Transactions not found'
            ], 404);
        }
    }
}
