<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'grade_id',
        'name',
        'status',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function getNameAttribute($value)
    {
        $name = json_decode($value, true);
        $lang = app()->getLocale();
        return $name[$lang] ?? $name['en'];
    }
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
