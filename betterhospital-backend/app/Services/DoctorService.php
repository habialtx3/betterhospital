<?php

namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Repositories\HospitalSpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorService
{
    private $doctorRepository;
    private $hospitalSpecialistRepository;

    public function __construct(DoctorRepository $doctorRepository, HospitalSpecialistRepository $hospitalSpecialistRepository)
    {
        $this->doctorRepository = $doctorRepository;
        $this->hospitalSpecialistRepository = $hospitalSpecialistRepository;
    }


    public function getAll(array $fields)
    {
        $this->doctorRepository->getAll($fields);
    }

    public function getById(int $id, array $fields)
    {
        $this->doctorRepository->getById($id, $fields);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('doctor', 'public');
    }

    private function deletePhoto(UploadedFile $photo)
    {
        $relativePath = 'doctor/' . basename($photo);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function create(array $data)
    {
        if (!$this->hospitalSpecialistRepository->existForHospitalAndSpecialist(
            $data['hospital_id'],
            $data['specialist_id']
        )) {
            throw ValidationException::withMessages([
                'specialist_id' => ['Selected specialist not avaible in the selected hospital.']
            ]);
        }

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (!$this->hospitalSpecialistRepository->existForHospitalAndSpecialist(
            $data['hospital_id'],
            $data['specialist_id']
        )) {
            throw ValidationException::withMessages([
                'specialist_id' => ['Selected specialist not avaible in the selected hospital.']
            ]);
        }

        $doctor = $this->doctorRepository->getById($id, ['*']);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($doctor->photo)) {
                $this->deletePhoto($doctor->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->doctorRepository->update($id, $data);
    }


    public function delete(int $id)
    {
        $doctor = $this->doctorRepository->getById($id, ['*']);

        if ($doctor->photo) {
            $this->deletePhoto($doctor->photo);
        }

        $this->doctorRepository->delete($id);
    }

    public function filterBySpecialistAndHospital(int $hospitalId, int $specialistId)
    {
        return $this->doctorRepository->filterBySpecialistAndHospital($hospitalId, $specialistId);
    }

    public function getAvailableSlot(int $doctorId)
    {
        $doctor = $this->doctorRepository->getById($doctorId, ['id']);

        $timeSlots = ['10:30', '11:30', '13:30', '14:30', '15:30', '16:30'];
        $dates = collect([
            now()->addDays(1)->startOfDay(),
            now()->addDays(2)->startOfDay(),
            now()->addDays(3)->startOfDay(),
        ]);

        $availablity = [];

        foreach ($dates as $date) {
            $dateStr = $date->toDateString();
            $availablity[$dateStr] = [];

            foreach ($timeSlots as $time) {
                $isTaken = $doctor->bookingTransactions()
                    ->whereDate('started_at', $dateStr)
                    ->whereTime('time_at', $time)
                    ->exists();

                if (!$isTaken) {
                $availablity[$dateStr][] = $time;
                }
            }
        }

        return $availablity;
    }
}
