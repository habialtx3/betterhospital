<?php

namespace App\Services;

use App\Repositories\SpecialistRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SpecialistService
{
    private $specialistRepository;

    public function __construct(SpecialistRepository $specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
    }

    public function getAll(array $fields)
    {
        return $this->specialistRepository->getAll($fields);
    }

    public function getById(int $id, array $fields)
    {
        return $this->specialistRepository->getById($id, $fields);
    }

    private function uploadPhoto(UploadedFile $photo): string
    {
        return $photo->store('specialist', 'public');
    }

    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'specialist/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->specialistRepository->create($data);
    }


    public function update(int $id, array $data)
    {
        $fields = ['*'];
        $specialist = $this->specialistRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($specialist->photo)){
                $this->deletePhoto($specialist->photo);
            }

            $data['photo']->store('specialist','public');
        }
            return $this->specialistRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        $fields = ['*'];
        $specialist = $this->specialistRepository->getById($id,$fields);

        if($specialist->photo){
            $this->deletePhoto($specialist->photo);
        }
        
        return $this->specialistRepository->delete($id);
    }
}
