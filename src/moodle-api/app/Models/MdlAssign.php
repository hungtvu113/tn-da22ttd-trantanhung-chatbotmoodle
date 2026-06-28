<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlAssign extends Model
{
    protected $table = 'assign';
    public $timestamps = false;

    protected $fillable = ['course', 'name', 'intro', 'grade'];

    public function assignGrades()
    {
        return $this->hasMany(MdlAssignGrade::class, 'assignment');
    }
}
