<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlGradingInstance extends Model
{
    protected $table = 'grading_instances';
    public $timestamps = false;

    public function definition()
    {
        return $this->belongsTo(MdlGradingDefinition::class, 'definitionid');
    }

    public function rubricFillings()
    {
        return $this->hasMany(MdlRubricFilling::class, 'instanceid');
    }
}
