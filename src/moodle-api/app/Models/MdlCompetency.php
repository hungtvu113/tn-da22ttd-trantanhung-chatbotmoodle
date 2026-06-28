<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCompetency extends Model
{
    protected $table = 'competency';
    public $timestamps = false;

    public function moduleComps()
    {
        return $this->hasMany(MdlCompetencyModuleComp::class, 'competencyid');
    }

    public function userComps()
    {
        return $this->hasMany(MdlCompetencyUserComp::class, 'competencyid');
    }
}
