<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlGradeItem extends Model
{
    protected $table = 'grade_items';
    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo(MdlCourse::class, 'courseid');
    }

    public function gradeGrades()
    {
        return $this->hasMany(MdlGradeGrade::class, 'itemid');
    }
}
