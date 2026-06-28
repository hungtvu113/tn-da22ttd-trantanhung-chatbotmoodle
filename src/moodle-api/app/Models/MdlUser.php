<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlUser extends Model
{
    protected $table = 'user';
    public $timestamps = false;

    protected $fillable = [
        'username', 'firstname', 'lastname', 'email', 'idnumber',
    ];

    public function gradeGrades()
    {
        return $this->hasMany(MdlGradeGrade::class, 'userid');
    }

    public function competencyUserComps()
    {
        return $this->hasMany(MdlCompetencyUserComp::class, 'userid');
    }
}
