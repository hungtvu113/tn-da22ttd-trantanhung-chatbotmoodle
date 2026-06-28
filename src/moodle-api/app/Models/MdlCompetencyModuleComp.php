<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCompetencyModuleComp extends Model
{
    protected $table = 'competency_modulecomp';
    public $timestamps = false;

    public function competency()
    {
        return $this->belongsTo(MdlCompetency::class, 'competencyid');
    }

    public function courseModule()
    {
        return $this->belongsTo(MdlCourseModule::class, 'cmid');
    }
}
