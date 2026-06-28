<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlGradingDefinition extends Model
{
    protected $table = 'grading_definitions';
    public $timestamps = false;

    public function instances()
    {
        return $this->hasMany(MdlGradingInstance::class, 'definitionid');
    }

    public function rubricCriteria()
    {
        return $this->hasMany(MdlRubricCriteria::class, 'definitionid');
    }
}
