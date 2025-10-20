<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'filiere_id',
        'description',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
