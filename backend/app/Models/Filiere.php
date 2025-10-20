<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'departement_id',
        'description',
    ];

    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }
}
