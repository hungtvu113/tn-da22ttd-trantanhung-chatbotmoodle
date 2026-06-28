<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DataSource\MoodleDataSourceInterface;
use App\Services\MoodleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoodleController extends Controller
{
    protected MoodleClient $moodle;
    protected MoodleDataSourceInterface $dataSource;

    public function __construct(MoodleClient $moodle, MoodleDataSourceInterface $dataSource)
    {
        $this->moodle = $moodle;
        // Nguồn dữ liệu (db | ws) — quyết định bởi MOODLE_DATA_SOURCE.
        $this->dataSource = $dataSource;
    }

    /**
     * Danh sach courses + category
     */
    public function courses(Request $request): JsonResponse
    {
        try {
            // Nguồn dữ liệu (db | ws) tùy MOODLE_DATA_SOURCE.
            $data = $this->dataSource->getCourses($request->input('search'));

            return response()->json([
                'success' => true,
                'source'  => $this->dataSource->name(),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * SV trong course
     */
    public function courseStudents(int $courseId): JsonResponse
    {
        try {
            $data = $this->dataSource->getCourseStudents($courseId);

            return response()->json([
                'success' => true,
                'source'  => $this->dataSource->name(),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Diem SV trong course
     */
    public function courseGrades(int $courseId): JsonResponse
    {
        try {
            $data = $this->dataSource->getCourseGrades($courseId);

            return response()->json([
                'success' => true,
                'source'  => $this->dataSource->name(),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * CLO/PLO gan vao course
     */
    public function courseCompetencies(int $courseId): JsonResponse
    {
        try {
            $comps = $this->moodle->getCourseCompetencies($courseId);

            $result = array_map(fn($c) => [
                'id' => $c['competency']['id'] ?? $c['id'] ?? 0,
                'shortname' => $c['competency']['shortname'] ?? $c['shortname'] ?? '',
                'description' => strip_tags($c['competency']['description'] ?? $c['description'] ?? ''),
                'idnumber' => $c['competency']['idnumber'] ?? $c['idnumber'] ?? '',
            ], $comps);

            return response()->json([
                'success' => true,
                'data' => array_values($result),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ket qua CLO cua SV trong course
     */
    public function courseCompetencyResults(int $courseId): JsonResponse
    {
        try {
            // Lay danh sach SV
            $users = $this->moodle->getEnrolledUsers($courseId);
            $students = array_filter($users, function ($u) {
                foreach ($u['roles'] ?? [] as $role) {
                    if ($role['roleid'] == 5) return true;
                }
                return false;
            });

            // Lay danh sach competencies
            $comps = $this->moodle->getCourseCompetencies($courseId);
            $compIds = array_map(fn($c) => $c['competency']['id'] ?? $c['id'] ?? 0, $comps);

            // Lay ket qua tung SV
            $result = [];
            foreach ($students as $student) {
                $studentComps = [];
                foreach ($compIds as $compId) {
                    try {
                        $ucResult = $this->moodle->getUserCompetencyInCourse($courseId, $student['id'], $compId);
                        $usercomp = $ucResult['usercompetencycourse'] ?? $ucResult;
                        $studentComps[] = [
                            'competency_id' => $compId,
                            'grade' => $usercomp['grade'] ?? null,
                            'proficiency' => $usercomp['proficiency'] ?? null,
                            'status' => ($usercomp['proficiency'] ?? 0) ? 'Dat' : 'Chua dat',
                        ];
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $result[] = [
                    'user_id' => $student['id'],
                    'fullname' => $student['fullname'],
                    'competencies' => $studentComps,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Truy xuat nguoc: search -> course -> category hierarchy
     */
    public function courseTraceback(Request $request): JsonResponse
    {
        try {
            $search = $request->input('search');
            if (!$search) {
                return response()->json(['success' => false, 'message' => 'Vui long cung cap tham so search'], 400);
            }

            $courses = $this->moodle->getCourses();
            $categories = collect($this->moodle->getCategories())->keyBy('id');

            // Filter
            $courses = array_filter($courses, function ($c) use ($search) {
                return $c['id'] > 1 && (
                    str_contains(strtolower($c['shortname']), strtolower($search))
                    || str_contains(strtolower($c['fullname']), strtolower($search))
                    || str_contains(strtolower($c['idnumber'] ?? ''), strtolower($search))
                );
            });

            if (empty($courses)) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay: ' . $search], 404);
            }

            $result = array_map(function ($course) use ($categories) {
                $categoryPath = $this->buildCategoryPath($course['categoryid'] ?? 0, $categories);
                $breadcrumb = collect($categoryPath)->pluck('name')->push($course['shortname'])->implode(' > ');

                return [
                    'course' => [
                        'id' => $course['id'],
                        'fullname' => $course['fullname'],
                        'shortname' => $course['shortname'],
                        'idnumber' => $course['idnumber'] ?? '',
                    ],
                    'category_hierarchy' => $categoryPath,
                    'breadcrumb' => $breadcrumb,
                ];
            }, $courses);

            return response()->json(['success' => true, 'data' => array_values($result)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tong hop course: SV + Diem + CLO + Assignments
     */
    public function courseFullResults(int $courseId): JsonResponse
    {
        try {
            // Course info
            $allCourses = $this->moodle->getCourses();
            $course = collect($allCourses)->firstWhere('id', $courseId);
            if (!$course) {
                return response()->json(['success' => false, 'message' => 'Course not found'], 404);
            }

            // Students
            $users = $this->moodle->getEnrolledUsers($courseId);
            $students = array_values(array_filter($users, function ($u) {
                foreach ($u['roles'] ?? [] as $role) {
                    if ($role['roleid'] == 5) return true;
                }
                return false;
            }));

            // Grades
            $gradeData = $this->moodle->getGrades($courseId);

            // Assignments
            $assignData = $this->moodle->getAssignments([$courseId]);
            $assignments = $assignData[0]['assignments'] ?? [];

            // CLO
            $competencies = [];
            try {
                $comps = $this->moodle->getCourseCompetencies($courseId);
                $competencies = array_map(fn($c) => [
                    'id' => $c['competency']['id'] ?? 0,
                    'shortname' => $c['competency']['shortname'] ?? '',
                    'description' => strip_tags($c['competency']['description'] ?? ''),
                ], $comps);
            } catch (\Exception $e) {}

            // Map grades per student
            $userGradesMap = [];
            foreach ($gradeData['usergrades'] ?? [] as $ug) {
                $userGradesMap[$ug['userid']] = array_values(array_filter(
                    array_map(function ($gi) {
                        if ($gi['itemtype'] === 'course') return null;
                        return [
                            'activity' => $gi['itemname'] ?? '',
                            'module' => $gi['itemmodule'] ?? '',
                            'grade' => $gi['graderaw'] ?? null,
                            'max_grade' => $gi['grademax'] ?? null,
                        ];
                    }, $ug['gradeitems'] ?? [])
                ));
            }

            $result = [
                'course' => [
                    'id' => $course['id'],
                    'fullname' => $course['fullname'],
                    'shortname' => $course['shortname'],
                ],
                'competencies_clo' => $competencies,
                'assignments' => array_map(fn($a) => [
                    'id' => $a['id'],
                    'name' => $a['name'],
                    'grade' => $a['grade'] ?? 0,
                ], $assignments),
                'students' => array_map(fn($s) => [
                    'id' => $s['id'],
                    'fullname' => $s['fullname'],
                    'email' => $s['email'] ?? '',
                    'grades' => $userGradesMap[$s['id']] ?? [],
                ], $students),
            ];

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ket qua hoc tap cua 1 SV
     */
    public function studentResults(string $username): JsonResponse
    {
        try {
            $student = $this->findUserByUsername($username);
            if (empty($student)) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay SV: ' . $username], 404);
            }

            // Lay khoa hoc da ghi danh qua DB.
            $studentCourses = DB::table('user_enrolments as ue')
                ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
                ->join('course as c', 'c.id', '=', 'e.courseid')
                ->where('ue.userid', $student->id)
                ->where('ue.status', 0)
                ->where('e.status', 0)
                ->where('c.id', '>', 1)
                ->distinct()
                ->orderBy('c.fullname')
                ->get(['c.id', 'c.fullname', 'c.shortname']);

            $courseIds = $studentCourses->pluck('id')->all();

            // Diem: lay tu grade_grades JOIN grade_items (khong dung webservice).
            $gradeRows = [];
            if (!empty($courseIds)) {
                $gradeRows = DB::table('grade_grades as gg')
                    ->join('grade_items as gi', 'gi.id', '=', 'gg.itemid')
                    ->whereIn('gi.courseid', $courseIds)
                    ->where('gg.userid', $student->id)
                    ->whereIn('gi.itemtype', ['mod', 'manual'])
                    ->get(['gi.courseid', 'gi.itemname', 'gi.itemmodule', 'gg.finalgrade', 'gi.grademax']);
            }
            $gradesByCourse = [];
            foreach ($gradeRows as $g) {
                $gradesByCourse[$g->courseid][] = [
                    'activity'  => $g->itemname ?? '',
                    'module'    => $g->itemmodule ?? '',
                    'grade'     => $g->finalgrade !== null ? (float) $g->finalgrade : null,
                    'max_grade' => $g->grademax !== null ? (float) $g->grademax : null,
                ];
            }

            // CLO: lay tu competency + user_competency_course (khong dung webservice).
            $competencyRows = [];
            if (!empty($courseIds)) {
                $competencyRows = DB::table('competency_coursecomp as cc')
                    ->join('competency as comp', 'comp.id', '=', 'cc.competencyid')
                    ->leftJoin('competency_usercompcourse as ucc', function ($j) use ($student) {
                        $j->on('ucc.competencyid', '=', 'cc.competencyid')
                          ->on('ucc.courseid', '=', 'cc.courseid')
                          ->where('ucc.userid', $student->id);
                    })
                    ->whereIn('cc.courseid', $courseIds)
                    ->orderBy('cc.courseid')
                    ->get(['cc.courseid', 'comp.id as comp_id', 'comp.shortname', 'ucc.grade', 'ucc.proficiency']);
            }
            $compsByCourse = [];
            foreach ($competencyRows as $r) {
                $compsByCourse[$r->courseid][] = [
                    'competency_id' => (int) $r->comp_id,
                    'shortname'     => $r->shortname ?? '',
                    'grade'         => $r->grade,
                    'proficiency'   => $r->proficiency,
                    'status'        => ($r->proficiency ?? 0) ? 'Dat' : 'Chua dat',
                ];
            }

            $courseResults = [];
            foreach ($studentCourses as $course) {
                $courseResults[] = [
                    'course_id'    => (int) $course->id,
                    'fullname'     => $course->fullname,
                    'shortname'    => $course->shortname,
                    'category_path' => [],
                    'grades'       => $gradesByCourse[$course->id] ?? [],
                    'competencies' => $compsByCourse[$course->id] ?? [],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id'       => (int) $student->id,
                        'username' => $student->username,
                        'fullname' => $student->firstname . ' ' . $student->lastname,
                        'email'    => $student->email ?? '',
                        'idnumber' => $student->idnumber ?? '',
                    ],
                    'total_courses' => count($courseResults),
                    'courses'       => $courseResults,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Danh sach khoa hoc ma 1 user da ghi danh (nhe, chi id + ten).
     * Dung cho chatbot phan quyen noi dung RAG theo enrolment -> can nhanh.
     */
    public function studentCourses(string $username): JsonResponse
    {
        try {
            $student = $this->findUserByUsername($username);
            if (empty($student)) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay user: ' . $username], 404);
            }

            // Truy van enrolment truc tiep tu DB (1 query) -> nhanh, tranh timeout webservice.
            $courses = DB::table('user_enrolments as ue')
                ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
                ->join('course as c', 'c.id', '=', 'e.courseid')
                ->where('ue.userid', $student->id)
                ->where('ue.status', 0)
                ->where('e.status', 0)
                ->where('c.id', '>', 1)
                ->distinct()
                ->orderBy('c.fullname')
                ->get(['c.id as course_id', 'c.fullname', 'c.shortname'])
                ->map(fn($c) => [
                    'course_id' => (int) $c->course_id,
                    'fullname' => $c->fullname,
                    'shortname' => $c->shortname,
                ])
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => (int) $student->id,
                    'username' => $student->username,
                    'courses' => $courses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cac deadline (assignment + quiz) sap toi cua 1 SV, truy van truc tiep DB.
     * Mac dinh chi tra ve deadline con han (duedate >= now), sap xep tang dan.
     */
    public function studentDeadlines(string $username, Request $request): JsonResponse
    {
        try {
            $student = $this->findUserByUsername($username);
            if (empty($student)) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay user: ' . $username], 404);
            }

            $courseIds = DB::table('user_enrolments as ue')
                ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
                ->where('ue.userid', $student->id)
                ->where('ue.status', 0)
                ->where('e.status', 0)
                ->where('e.courseid', '>', 1)
                ->distinct()
                ->pluck('e.courseid')
                ->all();

            if (empty($courseIds)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'username' => $student->username,
                        'now' => time(),
                        'total' => 0,
                        'deadlines' => [],
                    ],
                ]);
            }

            // includepast=1 -> tra ve ca deadline da qua (mac dinh chi lay con han).
            $includePast = (bool) $request->query('includepast', false);
            $now = time();

            // Assignment (mdl_assign.duedate)
            $assignQuery = DB::table('assign as a')
                ->join('course as c', 'c.id', '=', 'a.course')
                ->whereIn('a.course', $courseIds)
                ->where('a.duedate', '>', 0);
            if (!$includePast) {
                $assignQuery->where('a.duedate', '>=', $now);
            }
            $assignments = $assignQuery
                ->get(['a.name', 'a.duedate', 'c.fullname as course_fullname', 'c.shortname as course_shortname'])
                ->map(fn($a) => [
                    'type' => 'assignment',
                    'name' => $a->name,
                    'course' => $a->course_fullname,
                    'course_shortname' => $a->course_shortname,
                    'duedate' => (int) $a->duedate,
                    'duedate_text' => date('Y-m-d H:i', (int) $a->duedate),
                    'days_left' => (int) floor(((int) $a->duedate - $now) / 86400),
                ]);

            // Quiz (mdl_quiz.timeclose)
            $quizQuery = DB::table('quiz as q')
                ->join('course as c', 'c.id', '=', 'q.course')
                ->whereIn('q.course', $courseIds)
                ->where('q.timeclose', '>', 0);
            if (!$includePast) {
                $quizQuery->where('q.timeclose', '>=', $now);
            }
            $quizzes = $quizQuery
                ->get(['q.name', 'q.timeclose', 'c.fullname as course_fullname', 'c.shortname as course_shortname'])
                ->map(fn($q) => [
                    'type' => 'quiz',
                    'name' => $q->name,
                    'course' => $q->course_fullname,
                    'course_shortname' => $q->course_shortname,
                    'duedate' => (int) $q->timeclose,
                    'duedate_text' => date('Y-m-d H:i', (int) $q->timeclose),
                    'days_left' => (int) floor(((int) $q->timeclose - $now) / 86400),
                ]);

            $deadlines = $assignments->concat($quizzes)
                ->sortBy('duedate')
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => [
                    'username' => $student->username,
                    'now' => $now,
                    'total' => count($deadlines),
                    'deadlines' => $deadlines,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Liet ke cac hoat dong (module) trong tung khoa hoc ma SV dang ghi danh.
     * Course enrolment lay tu DB (nhanh); ten hoat dong lay qua core_course_get_contents.
     */
    public function studentActivities(string $username): JsonResponse
    {
        try {
            $student = $this->findUserByUsername($username);
            if (empty($student)) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay user: ' . $username], 404);
            }

            $courses = DB::table('user_enrolments as ue')
                ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
                ->join('course as c', 'c.id', '=', 'e.courseid')
                ->where('ue.userid', $student->id)
                ->where('ue.status', 0)
                ->where('e.status', 0)
                ->where('c.id', '>', 1)
                ->distinct()
                ->orderBy('c.fullname')
                ->get(['c.id', 'c.fullname', 'c.shortname']);

            $courseIds = $courses->pluck('id')->all();

            // Lay course_modules (dang hien) + ten loai module, truy van DB truc tiep.
            // KHONG dung webservice core_course_get_contents vi Moodle chay php -S don luong:
            // goi webservice long trong request cua plugin se deadlock (treo/timeout).
            $modulesByCourse = [];
            if (!empty($courseIds)) {
                $cms = DB::table('course_modules as cm')
                    ->join('modules as m', 'm.id', '=', 'cm.module')
                    ->whereIn('cm.course', $courseIds)
                    ->where('cm.visible', 1)
                    ->where(function ($q) {
                        $q->whereNull('cm.deletioninprogress')->orWhere('cm.deletioninprogress', 0);
                    })
                    ->orderBy('cm.section')
                    ->orderBy('cm.id')
                    ->get(['cm.course', 'cm.instance', 'm.name as modname']);

                // Ten hoat dong nam o bang rieng theo tung loai module (mdl_<modname>.name).
                $namesByType = [];
                foreach ($cms->groupBy('modname') as $modname => $items) {
                    $instanceIds = $items->pluck('instance')->unique()->all();
                    try {
                        $namesByType[$modname] = DB::table($modname)
                            ->whereIn('id', $instanceIds)
                            ->pluck('name', 'id');
                    } catch (\Exception $e) {
                        // Bang khong co cot 'name' hoac khong ton tai -> bo qua loai nay.
                    }
                }

                foreach ($cms as $cm) {
                    $name = trim((string) ($namesByType[$cm->modname][$cm->instance] ?? ''));
                    if ($name === '') {
                        continue;
                    }
                    $modulesByCourse[$cm->course][] = [
                        'name' => $name,
                        'type' => $cm->modname,
                    ];
                }
            }

            $result = [];
            foreach ($courses as $course) {
                $result[] = [
                    'course_id' => (int) $course->id,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'activities' => $modulesByCourse[$course->id] ?? [],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'username' => $student->username,
                    'total_courses' => count($result),
                    'courses' => $result,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tien do hoan thanh khoa hoc cua 1 SV (module completion).
     * completionstate: 0=chua, 1=hoan thanh, 2=hoan thanh dat.
     */
    public function studentProgress(string $username): JsonResponse
    {
        try {
            $student = $this->findUserByUsername($username);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Khong tim thay user: ' . $username], 404);
            }

            $courses = DB::table('user_enrolments as ue')
                ->join('enrol as e', 'e.id', '=', 'ue.enrolid')
                ->join('course as c', 'c.id', '=', 'e.courseid')
                ->where('ue.userid', $student->id)
                ->where('ue.status', 0)->where('e.status', 0)->where('c.id', '>', 1)
                ->distinct()->orderBy('c.fullname')
                ->get(['c.id', 'c.fullname', 'c.shortname']);

            $result = [];
            foreach ($courses as $course) {
                // Dem tong so module co bat completion tracking.
                $total = DB::table('course_modules')
                    ->where('course', $course->id)
                    ->where('visible', 1)
                    ->where('completion', '>', 0)
                    ->count();

                // Dem so module SV da hoan thanh.
                $done = DB::table('course_modules_completion as cmc')
                    ->join('course_modules as cm', 'cm.id', '=', 'cmc.coursemoduleid')
                    ->where('cm.course', $course->id)
                    ->where('cmc.userid', $student->id)
                    ->where('cmc.completionstate', '>=', 1)
                    ->count();

                $result[] = [
                    'course_id'   => (int) $course->id,
                    'fullname'    => $course->fullname,
                    'shortname'   => $course->shortname,
                    'total_modules' => (int) $total,
                    'done_modules'  => (int) $done,
                    'percent'     => $total > 0 ? round($done / $total * 100) : null,
                ];
            }

            return response()->json(['success' => true, 'data' => ['username' => $student->username, 'courses' => $result]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Thong ke diem cua 1 khoa hoc (TB, cao nhat, thap nhat, so SV co diem).
     */
    public function courseGradeStats(int $courseId): JsonResponse
    {
        try {
            // Grade item tong ket cua course (itemtype=course).
            $courseItem = DB::table('grade_items')
                ->where('courseid', $courseId)
                ->where('itemtype', 'course')
                ->first(['id']);

            if (!$courseItem) {
                return response()->json(['success' => true, 'data' => ['course_id' => $courseId, 'stats' => null, 'activities' => []]]);
            }

            $stats = DB::table('grade_grades')
                ->where('itemid', $courseItem->id)
                ->whereNotNull('finalgrade')
                ->selectRaw('COUNT(*) as total, ROUND(AVG(finalgrade),2) as avg, MAX(finalgrade) as max, MIN(finalgrade) as min')
                ->first();

            // Thong ke tung hoat dong co diem.
            $activities = DB::table('grade_items as gi')
                ->whereIn('gi.itemtype', ['mod', 'manual'])
                ->where('gi.courseid', $courseId)
                ->leftJoinSub(
                    DB::table('grade_grades as gg2')->whereNotNull('gg2.finalgrade')
                        ->join('grade_items as gi2', 'gi2.id', '=', 'gg2.itemid')
                        ->selectRaw('`mdl_gg2`.`itemid`, COUNT(*) as cnt, ROUND(AVG(`mdl_gg2`.`finalgrade`),2) as avg_grade, MAX(`mdl_gg2`.`finalgrade`) as max_grade, MIN(`mdl_gg2`.`finalgrade`) as min_grade, MAX(`mdl_gi2`.`grademax`) as grademax')
                        ->groupBy('gg2.itemid'),
                    'g', 'g.itemid', '=', 'gi.id'
                )
                ->get(['gi.itemname', 'gi.itemmodule', 'g.cnt', 'g.avg_grade', 'g.max_grade', 'g.min_grade', 'g.grademax'])
                ->map(fn($r) => [
                    'activity'  => $r->itemname ?? '',
                    'module'    => $r->itemmodule ?? '',
                    'count'     => (int) ($r->cnt ?? 0),
                    'avg'       => $r->avg_grade !== null ? (float) $r->avg_grade : null,
                    'max'       => $r->max_grade !== null ? (float) $r->max_grade : null,
                    'min'       => $r->min_grade !== null ? (float) $r->min_grade : null,
                    'max_grade' => $r->grademax !== null ? (float) $r->grademax : null,
                ]);

            return response()->json(['success' => true, 'data' => [
                'course_id' => $courseId,
                'stats' => [
                    'total_graded' => (int) ($stats->total ?? 0),
                    'avg'  => $stats->avg !== null ? (float) $stats->avg : null,
                    'max'  => $stats->max !== null ? (float) $stats->max : null,
                    'min'  => $stats->min !== null ? (float) $stats->min : null,
                ],
                'activities' => $activities,
            ]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Danh sach SV chua nop bai cho 1 assignment trong 1 course.
     * Neu khong truyen ten bai cu the, tra tat ca assignment cua course.
     */
    public function courseUnsubmitted(int $courseId, Request $request): JsonResponse
    {
        try {
            $assignName = $request->query('assign');

            $assignQuery = DB::table('assign')->where('course', $courseId);
            if ($assignName) {
                $assignQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($assignName) . '%']);
            }
            $assigns = $assignQuery->get(['id', 'name', 'duedate']);

            if ($assigns->isEmpty()) {
                return response()->json(['success' => true, 'data' => ['course_id' => $courseId, 'assignments' => []]]);
            }

            // Lay danh sach SV trong course.
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
                ->get(['u.id', 'u.username', DB::raw("CONCAT(mdl_u.firstname, ' ', mdl_u.lastname) as fullname")]);

            $result = [];
            foreach ($assigns as $assign) {
                $submitted = DB::table('assign_submission')
                    ->where('assignment', $assign->id)
                    ->whereIn('status', ['submitted', 'reopened'])
                    ->where('latest', 1)
                    ->pluck('userid')
                    ->all();

                $unsubmitted = $students->filter(fn($s) => !in_array($s->id, $submitted))
                    ->map(fn($s) => ['username' => $s->username, 'fullname' => $s->fullname])
                    ->values();

                $result[] = [
                    'assign_id'   => (int) $assign->id,
                    'name'        => $assign->name,
                    'duedate'     => (int) $assign->duedate,
                    'duedate_text'=> $assign->duedate > 0 ? date('Y-m-d H:i', (int)$assign->duedate) : null,
                    'total_students' => $students->count(),
                    'submitted'   => count($submitted),
                    'unsubmitted_count' => $unsubmitted->count(),
                    'unsubmitted' => $unsubmitted,
                ];
            }

            return response()->json(['success' => true, 'data' => ['course_id' => $courseId, 'assignments' => $result]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tien do hoan thanh cua tung SV trong 1 khoa hoc (giao vien xem).
     */
    public function courseProgress(int $courseId): JsonResponse
    {
        try {
            $total = DB::table('course_modules')
                ->where('course', $courseId)->where('visible', 1)->where('completion', '>', 0)
                ->count();

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
                ->get(['u.id', 'u.username', DB::raw("CONCAT(mdl_u.firstname, ' ', mdl_u.lastname) as fullname")]);

            $doneMap = DB::table('course_modules_completion as cmc')
                ->join('course_modules as cm', 'cm.id', '=', 'cmc.coursemoduleid')
                ->where('cm.course', $courseId)
                ->where('cmc.completionstate', '>=', 1)
                ->whereIn('cmc.userid', $students->pluck('id'))
                ->groupBy('cmc.userid')
                ->selectRaw('`mdl_cmc`.`userid`, COUNT(*) as done')
                ->pluck('done', 'userid');

            $rows = $students->map(function ($s) use ($total, $doneMap) {
                $done = (int) ($doneMap[$s->id] ?? 0);
                return [
                    'username' => $s->username,
                    'fullname' => $s->fullname,
                    'done'     => $done,
                    'total'    => (int) $total,
                    'percent'  => $total > 0 ? round($done / $total * 100) : null,
                ];
            })->sortByDesc('percent')->values();

            return response()->json(['success' => true, 'data' => [
                'course_id' => $courseId,
                'total_modules_tracked' => (int) $total,
                'students' => $rows,
            ]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tim user theo username, fallback sang idnumber. Truy van DB truc tiep.
     */
    private function findUserByUsername(string $username): ?object
    {
        return DB::table('user')
            ->where('deleted', 0)
            ->where(function ($q) use ($username) {
                $q->where('username', $username)->orWhere('idnumber', $username);
            })
            ->first(['id', 'username', 'firstname', 'lastname', 'email', 'idnumber']);
    }

    /**
     * Cau truc course: sections + modules + files (cho RAG ingestion)
     */
    public function courseContents(int $courseId): JsonResponse
    {
        try {
            $sections = $this->moodle->getCourseContents($courseId);

            return response()->json([
                'success' => true,
                'data' => array_values($sections),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Flat text content cua course (toi uu cho RAG indexing)
     * Tra ve list cac chunk text tu page, book chapters, label, section summary
     */
    public function courseTextContent(int $courseId): JsonResponse
    {
        try {
            $sections = $this->moodle->getCourseContents($courseId);
            $pages = collect($this->moodle->getPagesByCourses([$courseId]))->keyBy('coursemodule');
            $books = collect($this->moodle->getBooksByCourses([$courseId]))->keyBy('coursemodule');
            $labels = collect($this->moodle->getLabelsByCourses([$courseId]))->keyBy('coursemodule');

            $chunks = [];

            foreach ($sections as $section) {
                $sectionName = $section['name'] ?? '';
                $sectionSummary = $this->stripHtml($section['summary'] ?? '');

                if ($sectionSummary !== '') {
                    $chunks[] = [
                        'section' => $sectionName,
                        'module_id' => 0,
                        'module_name' => $sectionName,
                        'type' => 'section_summary',
                        'text' => $sectionSummary,
                        'files' => [],
                    ];
                }

                foreach ($section['modules'] ?? [] as $module) {
                    $modtype = $module['modname'] ?? '';
                    $cmid = $module['id'] ?? 0;
                    $text = '';
                    $files = [];

                    if ($modtype === 'page' && isset($pages[$cmid])) {
                        $text = $this->stripHtml($pages[$cmid]['content'] ?? '');
                    } elseif ($modtype === 'label' && isset($labels[$cmid])) {
                        $text = $this->stripHtml($labels[$cmid]['intro'] ?? '');
                    } elseif ($modtype === 'book' && isset($books[$cmid])) {
                        $text = $this->stripHtml($books[$cmid]['intro'] ?? '');
                    } elseif ($modtype === 'resource') {
                        foreach ($module['contents'] ?? [] as $file) {
                            if (($file['type'] ?? '') === 'file') {
                                $files[] = [
                                    'filename' => $file['filename'] ?? '',
                                    'mimetype' => $file['mimetype'] ?? '',
                                    'fileurl' => $file['fileurl'] ?? '',
                                    'filesize' => $file['filesize'] ?? 0,
                                ];
                            }
                        }
                    }

                    $intro = $this->stripHtml($module['description'] ?? '');
                    if ($text === '' && $intro !== '') {
                        $text = $intro;
                    }

                    if ($text === '' && empty($files)) {
                        continue;
                    }

                    $chunks[] = [
                        'section' => $sectionName,
                        'module_id' => $cmid,
                        'module_name' => $module['name'] ?? '',
                        'type' => $modtype,
                        'text' => $text,
                        'files' => $files,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'course_id' => $courseId,
                    'total_chunks' => count($chunks),
                    'chunks' => $chunks,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Proxy download file tu Moodle (PDF, DOCX, ...) co kem wstoken
     */
    public function downloadFile(Request $request)
    {
        $fileurl = $request->query('fileurl');
        if (empty($fileurl)) {
            return response()->json(['success' => false, 'message' => 'fileurl required'], 400);
        }

        try {
            [$content, $mime, $filename] = $this->moodle->downloadFile($fileurl);
            return response($content, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Strip HTML, decode entities, normalize whitespace
     */
    private function stripHtml(string $html): string
    {
        $text = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>'], "\n", $html));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }


    /**
     * Build category path (truy nguoc len category cha)
     */
    private function buildCategoryPath(int $categoryId, $categories): array
    {
        $path = [];
        $currentId = $categoryId;

        while ($currentId > 0 && isset($categories[$currentId])) {
            $cat = $categories[$currentId];
            array_unshift($path, [
                'id' => $cat['id'],
                'name' => $cat['name'],
            ]);
            $currentId = $cat['parent'] ?? 0;
        }

        return $path;
    }
}
