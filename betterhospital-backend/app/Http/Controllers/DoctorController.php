<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Http\Requests\SpecialistAndHospitalRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //
    private $doctorService;
    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $fields = ['name', 'photo', 'about', 'yoe', 'specialist_id', 'hospital_id', 'gender'];
        $doctors = $this->doctorService->getAll($fields);
        return response()->json(DoctorResource::collection($doctors));
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $doctor = $this->doctorService->getById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctors not found'
            ], 404);
        }
    }

    public function store(DoctorRequest $request)
    {
        $doctor = $this->doctorService->create($request->validated());
        return response()->json(new DoctorResource($doctor));
    }

    public function update(int $id)
    {
        try {
            $fields = ['*'];
            $doctor = $this->doctorService->getById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctors not found'
            ], 404);
        }
    }

    public function delete(int $id)
    {
        try {
            $fields = ['*'];
            $doctor = $this->doctorService->getById($id, $fields);
            return response()->json(new DoctorResource($doctor));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctors not found'
            ], 404);
        }
    }

    public function filterBySpecialistAndHospital(SpecialistAndHospitalRequest $request)
    {
        $validated = $request->validated();
        $doctors = $this->doctorService->filterBySpecialistAndHospital(
            $validated['hospital_id'],
            $validated['specialist_id'],
        );

        return DoctorResource::collection($doctors);
    }

    public function availableSlots(int $id)
    {
        try {
            $availability = $this->doctorService->getAvailableSlot($id);
            return response()->json(['data' => $availability]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doctors not found'
            ], 404);
        }
    }
}
