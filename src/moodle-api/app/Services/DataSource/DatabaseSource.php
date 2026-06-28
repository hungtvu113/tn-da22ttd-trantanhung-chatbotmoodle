<?php

namespace App\Services\DataSource;

use Illuminate\Support\Facades\DB;

/**
 * Nguồn dữ liệu Moodle bằng truy vấn CSDL trực tiếp.
 *
 * Ưu điểm: nhanh, không bị deadlock với Moodle dev server (php -S đơn luồng).
 * Dùng làm nguồn mặc định trên môi trường dev.
 */
class DatabaseSource implements MoodleDataSourceInterface
{
    public function name(): string
    {
        return 'db';
    }

    public function getCourses(?string $search = null): array
    {
        $query = DB::table('course as c')
            ->leftJoin('course_categories as cat', 'cat.id', '=', 'c.category')
            ->where('c.id', '>', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(c.fullname) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(c.shortname) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(c.idnumber) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        return $query->orderBy('c.fullname')
            ->get([
                'c.id', 'c.fullname', 'c.shortname', 'c.idnumber',
                'c.category as categoryid',
                'cat.name as category_name',
            ])
            ->map(fn($c) => [
                'id'            => (int) $c->id,
                'fullname'      => $c->fullname,
                'shortname'     => $c->shortname,
                'idnumber'      => $c->idnumber ?? '',
                'category'      => $c->categoryid ? ['id' => (int) $c->categoryid, 'name' => $c->category_name ?? ''] : null,
                'category_path' => [],
            ])
            ->values()
            ->all();
    }

    public function getCourseStudents(int $courseId): array
    {
        return DB::table('user_enrolments as ue')
            ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
            ->join('user as u', 'u.id', '=', 'ue.userid')
            ->join('role_assignments as ra', function ($j) use ($courseId) {
                $j->on('ra.userid', '=', 'ue.userid')
                  ->whereExists(function ($q) use ($courseId) {
                      $q->select(DB::raw(1))->from('context')
                        ->whereColumn('context.id', 'ra.contextid')
                        ->where('context.contextlevel', 50)
                        ->where('context.instanceid', $courseId);
                  });
            })
            ->join('role as r', 'r.id', '=', 'ra.roleid')
            ->where('e.courseid', $courseId)
            ->where('ue.status', 0)->where('e.status', 0)->where('u.deleted', 0)
            ->where('r.shortname', 'student')
            ->distinct()
            ->orderBy('u.lastname')
            ->get(['u.id', 'u.username', 'u.firstname', 'u.lastname', 'u.email', 'u.idnumber'])
            ->map(fn($u) => [
                'id'        => (int) $u->id,
                'username'  => $u->username,
                'firstname' => $u->firstname,
                'lastname'  => $u->lastname,
                'fullname'  => trim($u->firstname . ' ' . $u->lastname),
                'email'     => $u->email ?? '',
                'idnumber'  => $u->idnumber ?? '',
            ])
            ->values()
            ->all();
    }

    public function getCourseGrades(int $courseId): array
    {
        $students = DB::table('user_enrolments as ue')
            ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
            ->join('user as u', 'u.id', '=', 'ue.userid')
            ->join('role_assignments as ra', function ($j) use ($courseId) {
                $j->on('ra.userid', '=', 'ue.userid')
                  ->whereExists(function ($q) use ($courseId) {
                      $q->select(DB::raw(1))->from('context')
                        ->whereColumn('context.id', 'ra.contextid')
                        ->where('context.contextlevel', 50)
                        ->where('context.instanceid', $courseId);
                  });
            })
            ->join('role as r', 'r.id', '=', 'ra.roleid')
            ->where('e.courseid', $courseId)
            ->where('ue.status', 0)->where('e.status', 0)->where('u.deleted', 0)
            ->where('r.shortname', 'student')
            ->distinct()
            ->get(['u.id', 'u.firstname', 'u.lastname'])
            ->each(fn($u) => $u->fullname = trim($u->firstname . ' ' . $u->lastname))
            ->keyBy('id');

        $gradeRows = DB::table('grade_grades as gg')
            ->join('grade_items as gi', 'gi.id', '=', 'gg.itemid')
            ->where('gi.courseid', $courseId)
            ->whereIn('gi.itemtype', ['mod', 'manual'])
            ->whereIn('gg.userid', $students->keys()->all())
            ->get(['gg.userid', 'gi.itemname', 'gi.itemmodule', 'gg.finalgrade', 'gi.grademax']);

        $actsByUser = [];
        foreach ($gradeRows as $g) {
            $actsByUser[$g->userid][] = [
                'name'       => $g->itemname ?? '',
                'module'     => $g->itemmodule ?? '',
                'grade'      => $g->finalgrade !== null ? (float) $g->finalgrade : null,
                'max_grade'  => $g->grademax !== null ? (float) $g->grademax : null,
                'percentage' => ($g->grademax > 0 && $g->finalgrade !== null)
                    ? round($g->finalgrade / $g->grademax * 100, 1) . '%' : '',
            ];
        }

        return $students->map(fn($s) => [
            'user_id'    => (int) $s->id,
            'fullname'   => $s->fullname,
            'activities' => $actsByUser[$s->id] ?? [],
        ])->values()->all();
    }
}
