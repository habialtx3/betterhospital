<?php

namespace App\Http\Controllers;

use App\Models\BookingTransaction;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    //

    public function getAll(array $fields)
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist', 'user'])
            ->latest()
            ->paginate(10);
    }

    public function getByIdForManager(int $userId)
    {
        return BookingTransaction::with(['doctor', 'doctor.hospital', 'doctor.specialist', 'user'])
            ->findOrFail($userId);
    }

    public function getAllForUser(int $userId)
    {
        return BookingTransaction::where('user_id', $userId)
            ->with(['doctor', 'doctor.hospital', 'doctor.specialist'])
            ->latest()
            ->paginate(10);
    }

    public function getById(int $id, int $userId)
    {
        return BookingTransaction::where('id', $id)
            ->where('user_id', $userId)
            ->with(['doctor', 'doctor.hospital', 'doctor.specialist'])
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return BookingTransaction::create($data);
    }

    public function updateStatus(int $id, string $status)
    {
        $transaction = $this->getByIdForManager($id);
        $transaction->update(['status' => $status]);
        return $transaction;
    }

    public function isTimeSlotTakenForDoctor(int $doctorId, string $date, string $time)
    {
        return BookingTransaction::where('doctor_id',$doctorId)
        ->where('started_at',$date)
        ->where('time_at',$time)
        ->exists();
    }
}
