<?php

namespace App\Models;

use App\Livewire\Penilaian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;


    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}
