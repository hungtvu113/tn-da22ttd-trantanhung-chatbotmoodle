<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCourseModule extends Model
{
    protected $table = 'course_modules';
    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo(MdlCourse::class, 'course');
    }

    public function competencies()
    {
        return $this->hasMany(MdlCompetencyModuleComp::class, 'cmid');
    }
}
