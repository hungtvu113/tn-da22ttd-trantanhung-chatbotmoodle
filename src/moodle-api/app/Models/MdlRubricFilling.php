<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlRubricFilling extends Model
{
    protected $table = 'gradingform_rubric_fillings';
    public $timestamps = false;

    public function instance()
    {
        return $this->belongsTo(MdlGradingInstance::class, 'instanceid');
    }

    public function criteria()
    {
        return $this->belongsTo(MdlRubricCriteria::class, 'criterionid');
    }

    public function level()
    {
        return $this->belongsTo(MdlRubricLevel::class, 'levelid');
    }
}
