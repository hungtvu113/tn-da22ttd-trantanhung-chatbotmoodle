<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlAssignGrade extends Model
{
    protected $table = 'assign_grades';
    public $timestamps = false;

    public function assignment()
    {
        return $this->belongsTo(MdlAssign::class, 'assignment');
    }

    public function user()
    {
        return $this->belongsTo(MdlUser::class, 'userid');
    }
}
