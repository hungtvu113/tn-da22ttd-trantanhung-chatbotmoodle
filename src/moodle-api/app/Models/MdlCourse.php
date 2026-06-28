<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCourse extends Model
{
    protected $table = 'course';
    public $timestamps = false;

    protected $fillable = [
        'fullname', 'shortname', 'idnumber', 'category',
    ];

    public function courseModules()
    {
        return $this->hasMany(MdlCourseModule::class, 'course');
    }

    public function gradeItems()
    {
        return $this->hasMany(MdlGradeItem::class, 'courseid');
    }
}
