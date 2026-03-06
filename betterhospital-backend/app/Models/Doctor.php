<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Doctor extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'about',
        'yoe',
        'specialist_id',
        'hospital_id',
        'gender',
    ];

    public function hospital(){
        return $this->belongsTo(Hospital::class,'hospital_id');
    }

    public function specialist(){
        return $this->belongsTo(Specialist::class,'specialist_id');
    }

    public function bookingTransactions(){
        return $this->hasMany(BookingTransaction::class);
    }

    public function getPhotoAttribute($value){
        if(!$value){
            return null;
        }

        return url(Storage::url($value));
    }

}
