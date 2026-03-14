<?php

namespace App\Http\Controllers;

use App\Services\SpecialistService;
use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    private $specialistService;

    public function __construct(SpecialistService $specialistService)
    {
        $this->specialistService = $specialistService;
    }

    public function index()
    {
        $fields = ['id','name','photo','price',];
        $specialist = $this->specialistService->getAll($fields);
        return response()->json(SpecialistResour)
    }
}
