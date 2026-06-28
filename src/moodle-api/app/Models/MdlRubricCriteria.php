<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlRubricCriteria extends Model
{
    protected $table = 'gradingform_rubric_criteria';
    public $timestamps = false;

    public function definition()
    {
        return $this->belongsTo(MdlGradingDefinition::class, 'definitionid');
    }

    public function levels()
    {
        return $this->hasMany(MdlRubricLevel::class, 'criterionid');
    }

    public function fillings()
    {
        return $this->hasMany(MdlRubricFilling::class, 'criterionid');
    }
}
