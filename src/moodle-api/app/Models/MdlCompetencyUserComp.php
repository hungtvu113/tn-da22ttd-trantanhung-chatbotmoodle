<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCompetencyUserComp extends Model
{
    protected $table = 'competency_usercomp';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(MdlUser::class, 'userid');
    }

    public function competency()
    {
        return $this->belongsTo(MdlCompetency::class, 'competencyid');
    }
}
