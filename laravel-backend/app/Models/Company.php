<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Company extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'website', 'logo'];

    public function getLogoAttribute($logo)
    {
        if ($logo) {
            $image = URL::asset('storage/' . $logo);
            return $image;
        }
    }

    /**
     * Get the employees for the company.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
