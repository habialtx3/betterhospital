<?php

namespace App\Repositories;

use App\Models\HospitalSpecialist;

class HospitalSpecialistRepository
{
    public function existForHospitalAndSpecialist(int $hospitalId, int $specialistId)
    {
        return HospitalSpecialist::where('hospital_id', $hospitalId)
            ->where('specialist_id', $specialistId)
            ->exists();
    }
}
