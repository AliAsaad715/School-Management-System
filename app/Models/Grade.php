<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Grade extends Model
{
    use HasFactory, HasTranslations;
    protected $fillable = [
        'name',
        'notes'
    ];
    protected $casts = [
        'name' => 'array',
        'notes' => 'array'
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function setNotesAttribute($value)
    {
        $this->attributes['notes'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getNameAttribute($value)
    {
        $name = json_decode($value, true);
        $lang = app()->getLocale();
        return $name[$lang] ?? $name['en'];
    }
    public function getNotesAttribute($value)
    {
        $notes = json_decode($value, true);
        $lang = app()->getLocale();
        if($notes != null)
            return $notes[$lang] ?? $notes['en'];
        return null;
    }

    public function classrooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
