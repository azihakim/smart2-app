<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'bobot',
        'keterangan'
    ];

    public function subKriterias()
    {
        return $this->hasMany(SubKriteria::class, 'kriteria_id', 'id');
    }
}
