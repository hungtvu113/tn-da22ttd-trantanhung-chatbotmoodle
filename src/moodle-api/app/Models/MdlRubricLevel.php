<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlRubricLevel extends Model
{
    protected $table = 'gradingform_rubric_levels';
    public $timestamps = false;

    public function criteria()
    {
        return $this->belongsTo(MdlRubricCriteria::class, 'criterionid');
    }
}
