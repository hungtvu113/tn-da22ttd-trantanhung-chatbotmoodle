<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdlGradeGrade extends Model
{
    protected $table = 'grade_grades';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(MdlUser::class, 'userid');
    }

    public function gradeItem()
    {
        return $this->belongsTo(MdlGradeItem::class, 'itemid');
    }
}
