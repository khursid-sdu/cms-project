<?php

namespace app\modules\academics\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

/**
 * TrReport handles the business logic, DB queries, and KPI calculations
 * for TR Mark Generation Report within the academics module.
 */
class TrReport extends Model
{
    public $batch_year;
    public $course_id;
    public $branch_id;
    public $section_id;

    public function rules()
    {
        return [
            [['batch_year', 'course_id'], 'required'],
            [['batch_year'], 'safe'],
            [['course_id', 'branch_id', 'section_id'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'batch_year' => 'Batch (Year)',
            'course_id'  => 'Course',
            'branch_id'  => 'Branch',
            'section_id' => 'Section',
        ];
    }

    /**
     * Get distinct batch years extracted from batches.batch_name
     * e.g. "BTECH 2026-P" -> "2026"
     * @return array [year => year]
     */
    public static function getBatchYears()
    {
        $batches = (new Query())
            ->select(['batch_id', 'batch_name'])
            ->from('batches')
            ->where(['is_status' => [0, 1]])
            ->all();

        $years = [];
        foreach ($batches as $batch) {
            if (preg_match('/\b(20\d\d)\b/', $batch['batch_name'], $matches)) {
                $years[$matches[1]] = $matches[1];
            }
        }

        // Sort descending
        krsort($years);

        return $years;
    }

    /**
     * Get batches matching a specific year
     * @param string $year
     * @return array
     */
    public static function getBatchesByYear($year = null)
    {
        $query = (new Query())
            ->select(['batch_id', 'batch_name'])
            ->from('batches')
            ->where(['is_status' => 0])
            ->orderBy(['batch_name' => SORT_ASC]);

        if (!empty($year)) {
            $query->andWhere(['like', 'batch_name', $year]);
        }

        return $query->all();
    }

    /**
     * Get courses/degrees relevant to the selected batch year.
     * Groups by program (e.g. B.Tech, M.Tech, MBA, MCA).
     * @param string|null $year
     * @return array [program_key => program_label]
     */
    public static function getCoursesByYear($year = null)
    {
        $matchingBatches = self::getBatchesByYear($year);
        $programs = [];

        if (!empty($matchingBatches)) {
            foreach ($matchingBatches as $b) {
                $upper = strtoupper($b['batch_name']);
                if (strpos($upper, 'BTECH') !== false || strpos($upper, 'B.TECH') !== false) {
                    $programs['btech'] = 'B.Tech';
                } elseif (strpos($upper, 'MTECH') !== false || strpos($upper, 'M.TECH') !== false) {
                    $programs['mtech'] = 'M.Tech';
                } elseif (strpos($upper, 'MBA') !== false) {
                    $programs['mba'] = 'MBA';
                } elseif (strpos($upper, 'MCA') !== false) {
                    $programs['mca'] = 'MCA';
                }
            }
        }

        // If no batches matched or no standard programs detected, scan courses table
        if (empty($programs)) {
            $allCourses = (new Query())
                ->select(['course_id', 'course_name'])
                ->from('courses')
                ->orderBy(['course_name' => SORT_ASC])
                ->all();

            foreach ($allCourses as $c) {
                $upper = strtoupper($c['course_name']);
                if (strpos($upper, 'BTECH') !== false || strpos($upper, 'B.TECH') !== false) {
                    $programs['btech'] = 'B.Tech';
                } elseif (strpos($upper, 'MTECH') !== false || strpos($upper, 'M.TECH') !== false) {
                    $programs['mtech'] = 'M.Tech';
                } elseif (strpos($upper, 'MBA') !== false) {
                    $programs['mba'] = 'MBA';
                } elseif (strpos($upper, 'MCA') !== false) {
                    $programs['mca'] = 'MCA';
                } else {
                    $programs[$c['course_id']] = $c['course_name'];
                }
            }
        }

        return $programs;
    }

    /**
     * Get branches (from section_master) filtered by course
     * @param string|int|null $courseId (e.g. 'btech', 'mca', or numeric course_id)
     * @return array [section_master_id => section_master_name]
     */
    public static function getBranchesByCourse($courseId = null)
    {
        $normalized = strtolower(str_replace(['.', ' ', '-'], '', (string)$courseId));
        if (is_numeric($courseId)) {
            $cRow = (new Query())
                ->select(['course_name'])
                ->from('courses')
                ->where(['course_id' => $courseId])
                ->scalar();
            if ($cRow) {
                $normalized = strtolower(str_replace(['.', ' ', '-'], '', $cRow));
            }
        }

        $allBranches = (new Query())
            ->select(['section_master_id', 'section_master_name'])
            ->from('section_master')
            ->where(['is_status' => 0])
            ->orderBy(['section_master_name' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($allBranches as $b) {
            $id = $b['section_master_id'];
            $name = $b['section_master_name'];
            $upper = strtoupper($name);

            if (empty($normalized) || $normalized === 'all') {
                $result[$id] = $name;
            } elseif (strpos($normalized, 'btech') !== false) {
                if ($upper !== 'MBA' && $upper !== 'MCA') {
                    $result[$id] = $name;
                }
            } elseif (strpos($normalized, 'mtech') !== false) {
                if (strpos($upper, 'COMPUTER') !== false || strpos($upper, 'MECHANICAL') !== false) {
                    $result[$id] = $name;
                }
            } elseif (strpos($normalized, 'mba') !== false) {
                if ($upper === 'MBA') {
                    $result[$id] = $name;
                }
            } elseif (strpos($normalized, 'mca') !== false) {
                if ($upper === 'MCA') {
                    $result[$id] = $name;
                }
            } elseif (strpos($upper, $normalized) !== false) {
                $result[$id] = $name;
            }
        }

        if (empty($result)) {
            foreach ($allBranches as $b) {
                $result[$b['section_master_id']] = $b['section_master_name'];
            }
        }

        return $result;
    }

    /**
     * Get sections from section_defined for selected branch
     * @param string|int|null $courseId
     * @param int|string|null $branchId
     * @return array [sid => section_name]
     */
    public static function getSectionsByBranch($courseId = null, $branchId = null)
    {
        $query = (new Query())
            ->select(['sid', 'section_name'])
            ->from('section_defined')
            ->orderBy(['section_name' => SORT_ASC]);

        if (!empty($branchId) && $branchId !== 'all') {
            $query->andWhere(['section_master' => $branchId]);
        } elseif (!empty($courseId) && $courseId !== 'all') {
            $validBranches = array_keys(self::getBranchesByCourse($courseId));
            if (!empty($validBranches)) {
                $query->andWhere(['section_master' => $validBranches]);
            }
        }

        $rows = $query->all();
        $result = [];
        foreach ($rows as $r) {
            $result[$r['sid']] = $r['section_name'];
        }

        if (empty($result) && !empty($branchId) && $branchId !== 'all') {
            $bmName = (new Query())
                ->select(['section_master_name'])
                ->from('section_master')
                ->where(['section_master_id' => $branchId])
                ->scalar();
            $code = $bmName ? self::formatBranchShortName($bmName) : 'SEC';
            $result['bm_' . $branchId] = $code;
        }

        return $result;
    }

    /**
     * Calculate TR Report metrics for given filters
     * @param string|null $batchYear
     * @param string|int|null $courseId (e.g. 'btech', 'mca', 'mba')
     * @param int|string|null $branchId
     * @param int|string|null $sectionId
     * @return array ['rows' => [...], 'totals' => [...]]
     */
    public static function calculateReport($batchYear = null, $courseId = null, $branchId = null, $sectionId = null)
    {
        $normalizedCourse = strtolower(str_replace(['.', ' ', '-'], '', (string)$courseId));

        // 1. Resolve Batches
        $batchIds = [];
        $batchYearDisplay = $batchYear ?: 'All';
        if (!empty($batchYear)) {
            $batchIds = (new Query())
                ->select(['batch_id'])
                ->from('batches')
                ->where(['like', 'batch_name', $batchYear])
                ->column();
        }

        // 2. Resolve Courses
        $matchingCourseIds = [];
        if (!empty($courseId) && $courseId !== 'all') {
            if (is_numeric($courseId)) {
                $matchingCourseIds = [(int)$courseId];
            } else {
                $courseQuery = (new Query())->select(['course_id'])->from('courses');
                if (strpos($normalizedCourse, 'btech') !== false) {
                    $courseQuery->where(['like', 'course_name', 'B.Tech']);
                } elseif (strpos($normalizedCourse, 'mtech') !== false) {
                    $courseQuery->where(['like', 'course_name', 'M.Tech']);
                } elseif (strpos($normalizedCourse, 'mba') !== false) {
                    $courseQuery->where(['like', 'course_name', 'MBA']);
                } elseif (strpos($normalizedCourse, 'mca') !== false) {
                    $courseQuery->where(['like', 'course_name', 'MCA']);
                }
                $matchingCourseIds = $courseQuery->column();
            }
        }

        // Branches filter
        $targetBranches = [];
        if (!empty($branchId) && $branchId !== 'all') {
            $targetBranches = [(int)$branchId];
        } elseif (!empty($courseId)) {
            $validBranches = array_keys(self::getBranchesByCourse($courseId));
            $targetBranches = $validBranches;
        }

        // 3. Query Target Sections
        $sectionQuery = (new Query())
            ->select([
                'sd.sid',
                'sd.section_name',
                'sd.strength AS default_strength',
                'sd.section_master AS branch_id',
                'sm.section_master_name AS branch_name',
            ])
            ->from(['sd' => 'section_defined'])
            ->leftJoin(['sm' => 'section_master'], 'sm.section_master_id = sd.section_master');

        if (!empty($targetBranches)) {
            $sectionQuery->andWhere(['sd.section_master' => $targetBranches]);
        }

        if (!empty($sectionId) && $sectionId !== 'all') {
            $sectionQuery->andWhere(['sd.sid' => $sectionId]);
        }

        $sections = $sectionQuery->all();

        // Synthesize entries for branches not defined in section_defined
        $coveredBranchIds = array_column($sections, 'branch_id');
        foreach ($targetBranches as $tbId) {
            if (!in_array($tbId, $coveredBranchIds)) {
                $bm = (new Query())
                    ->select(['section_master_id', 'section_master_name'])
                    ->from('section_master')
                    ->where(['section_master_id' => $tbId])
                    ->one();
                if ($bm) {
                    $synthSid = 'bm_' . $tbId;
                    if (empty($sectionId) || $sectionId === 'all' || $sectionId === $synthSid) {
                        $sections[] = [
                            'sid'              => $synthSid,
                            'section_name'     => self::formatBranchShortName($bm['section_master_name']),
                            'default_strength' => 0,
                            'branch_id'        => $tbId,
                            'branch_name'      => $bm['section_master_name'],
                        ];
                    }
                }
            }
        }

        // Sort sections by branch name then section name
        usort($sections, function($a, $b) {
            $cmp = strcmp($a['branch_name'], $b['branch_name']);
            return ($cmp === 0) ? strcmp($a['section_name'], $b['section_name']) : $cmp;
        });

        // Determine display course program label
        if (is_numeric($courseId)) {
            $cName = (new Query())
                ->select(['course_name'])
                ->from('courses')
                ->where(['course_id' => $courseId])
                ->scalar();
            if ($cName) {
                $normalizedCourse = strtolower(str_replace(['.', ' ', '-'], '', $cName));
                $courseFull = $cName;
                $displayCourse = self::formatCourseShortName($cName);
            }
        }

        if (empty($displayCourse)) {
            if (strpos($normalizedCourse, 'btech') !== false) {
                $displayCourse = 'BTech';
                $courseFull = 'Bachelor of Technology (B.Tech)';
            } elseif (strpos($normalizedCourse, 'mtech') !== false) {
                $displayCourse = 'MTech';
                $courseFull = 'Master of Technology (M.Tech)';
            } elseif (strpos($normalizedCourse, 'mba') !== false) {
                $displayCourse = 'MBA';
                $courseFull = 'Master of Business Administration (MBA)';
            } elseif (strpos($normalizedCourse, 'mca') !== false) {
                $displayCourse = 'MCA';
                $courseFull = 'Master of Computer Applications (MCA)';
            } else {
                $displayCourse = self::formatCourseShortName((string)$courseId);
                $courseFull = (string)$courseId;
            }
        }

        $rows = [];
        $sl = 1;

        $totStrength  = 0;
        $totBacklog   = 0;
        $totEligible  = 0;
        $totGenerated = 0;
        $totPending   = 0;

        foreach ($sections as $sec) {
            $sId = $sec['sid'];
            $bId = $sec['branch_id'];
            $bName = $sec['branch_name'] ?: 'N/A';
            $sName = $sec['section_name'];
            $displayBranch = self::formatBranchShortName($bName);
            $isSynthetic = (strpos((string)$sId, 'bm_') === 0);

            // A. TOTAL STRENGTH
            $stuQuery = (new Query())->from('stu_master');
            if (!$isSynthetic) {
                $stuQuery->where(['stu_master_section_id' => $sId]);
            } else {
                $stuQuery->where(['stu_master_branch_id' => $bId]);
            }
            if (!empty($batchIds)) {
                $stuQuery->andWhere(['stu_master_batch_id' => $batchIds]);
            }
            if (!empty($matchingCourseIds)) {
                $stuQuery->andWhere(['stu_master_course_id' => $matchingCourseIds]);
            }

            $activeStudentsCount = (int)$stuQuery->count('DISTINCT stu_master_id');
            $totalStrength = $activeStudentsCount;

            // B. BACKLOG STUDENTS
            $backlogCount = 0;
            try {
                if (Yii::$app->db->getTableSchema('university_marks') !== null) {
                    $smTable = Yii::$app->db->getTableSchema('stu_master');
                    $regCol = null;
                    if ($smTable) {
                        foreach (['stu_master_reg_no', 'stu_master_regd_no', 'stu_reg_no', 'stu_regno'] as $col) {
                            if (isset($smTable->columns[$col])) {
                                $regCol = $col;
                                break;
                            }
                        }
                    }

                    if ($regCol) {
                        $backlogQuery = (new Query())
                            ->from(['um' => 'university_marks'])
                            ->innerJoin(['sm' => 'stu_master'], "sm.{$regCol} = um.stu_regno")
                            ->where(['like', 'um.grade', 'F']);

                        if (!$isSynthetic) {
                            $backlogQuery->andWhere(['sm.stu_master_section_id' => $sId]);
                        } else {
                            $backlogQuery->andWhere(['sm.stu_master_branch_id' => $bId]);
                        }
                        if (!empty($batchIds)) {
                            $backlogQuery->andWhere(['sm.stu_master_batch_id' => $batchIds]);
                        }
                        if (!empty($matchingCourseIds)) {
                            $backlogQuery->andWhere(['sm.stu_master_course_id' => $matchingCourseIds]);
                        }

                        $backlogCount = (int)$backlogQuery->count('DISTINCT um.stu_regno');
                    } else if (Yii::$app->db->getTableSchema('tabulation_registrar') !== null) {
                        $trBacklogQuery = (new Query())
                            ->from('tabulation_registrar')
                            ->where(['like', 'tr_grade', 'F']);
                        if (!empty($batchIds)) {
                            $trBacklogQuery->andWhere(['tr_batch_id' => $batchIds]);
                        }
                        if (!empty($matchingCourseIds)) {
                            $trBacklogQuery->andWhere(['tr_course_id' => $matchingCourseIds]);
                        }
                        $secStuIds = (new Query())
                            ->select(['stu_master_id'])
                            ->from('stu_master');
                        if (!$isSynthetic) {
                            $secStuIds->where(['stu_master_section_id' => $sId]);
                        } else {
                            $secStuIds->where(['stu_master_branch_id' => $bId]);
                        }
                        if (!empty($batchIds)) {
                            $secStuIds->andWhere(['stu_master_batch_id' => $batchIds]);
                        }
                        if (!empty($matchingCourseIds)) {
                            $secStuIds->andWhere(['stu_master_course_id' => $matchingCourseIds]);
                        }
                        $trBacklogQuery->andWhere(['tr_stu_master_id' => $secStuIds]);
                        $backlogCount = (int)$trBacklogQuery->count('DISTINCT tr_stu_master_id');
                    }
                }
            } catch (\Throwable $ex) {
                $backlogCount = 0;
            }

            // C. TR ELIGIBLE
            $trEligible = max(0, $totalStrength - $backlogCount);

            // D. TR GENERATED
            $trGeneratedCount = 0;
            try {
                if (Yii::$app->db->getTableSchema('tabulation_registrar') !== null) {
                    $trQuery = (new Query())->from('tabulation_registrar');
                    if (!empty($batchIds)) {
                        $trQuery->andWhere(['tr_batch_id' => $batchIds]);
                    }
                    if (!empty($matchingCourseIds)) {
                        $trQuery->andWhere(['tr_course_id' => $matchingCourseIds]);
                    }
                    
                    $secStuIds = (new Query())
                        ->select(['stu_master_id'])
                        ->from('stu_master');
                    if (!$isSynthetic) {
                        $secStuIds->where(['stu_master_section_id' => $sId]);
                    } else {
                        $secStuIds->where(['stu_master_branch_id' => $bId]);
                    }
                    if (!empty($batchIds)) {
                        $secStuIds->andWhere(['stu_master_batch_id' => $batchIds]);
                    }
                    if (!empty($matchingCourseIds)) {
                        $secStuIds->andWhere(['stu_master_course_id' => $matchingCourseIds]);
                    }
                    $trQuery->andWhere(['tr_stu_master_id' => $secStuIds]);

                    $trGeneratedCount = (int)$trQuery->count('DISTINCT tr_stu_master_id');
                }
            } catch (\Throwable $ex) {
                $trGeneratedCount = 0;
            }

            // E. TR PENDING
            $trPending = max(0, $trEligible - $trGeneratedCount);

            $rows[] = [
                'sl'                   => $sl++,
                'batch'                => $batchYearDisplay,
                'course'               => $displayCourse,
                'course_full'          => $courseFull,
                'branch'               => $displayBranch,
                'branch_full'          => $bName,
                'section'              => $sName,
                'total_strength'       => $totalStrength,
                'backlog_students'     => $backlogCount,
                'tr_eligible_students' => $trEligible,
                'tr_generated'         => $trGeneratedCount,
                'tr_pending'           => $trPending,
            ];

            $totStrength  += $totalStrength;
            $totBacklog   += $backlogCount;
            $totEligible  += $trEligible;
            $totGenerated += $trGeneratedCount;
            $totPending   += $trPending;
        }

        return [
            'rows' => $rows,
            'totals' => [
                'total_strength'       => $totStrength,
                'backlog_students'     => $totBacklog,
                'tr_eligible_students' => $totEligible,
                'tr_generated'         => $totGenerated,
                'tr_pending'           => $totPending,
            ],
        ];
    }

    /**
     * Format full course name to short code (e.g. "B.Tech ..." -> "BTech")
     */
    public static function formatCourseShortName($name)
    {
        $upper = strtoupper($name);
        if (strpos($upper, 'B.TECH') !== false || strpos($upper, 'BTECH') !== false) {
            return 'BTech';
        }
        if (strpos($upper, 'M.TECH') !== false || strpos($upper, 'MTECH') !== false) {
            return 'MTech';
        }
        if (strpos($upper, 'MBA') !== false) {
            return 'MBA';
        }
        if (strpos($upper, 'MCA') !== false) {
            return 'MCA';
        }
        return $name;
    }

    /**
     * Format branch name to short label (e.g. "Civil Engineering" -> "Civil")
     */
    public static function formatBranchShortName($name)
    {
        $upper = strtoupper($name);
        if (strpos($upper, 'COMPUTER') !== false || strpos($upper, 'CSE') !== false) {
            return 'CSE';
        }
        if (strpos($upper, 'CIVIL') !== false) {
            return 'Civil';
        }
        if (strpos($upper, 'ELECTRONICS AND COMMUNICATION') !== false || strpos($upper, 'ECE') !== false) {
            return 'ECE';
        }
        if (strpos($upper, 'ELECTRICAL AND ELECTRONICS') !== false || strpos($upper, 'EEE') !== false) {
            return 'EEE';
        }
        if (strpos($upper, 'ELECTRICAL') !== false || $upper === 'EE') {
            return 'EE';
        }
        if (strpos($upper, 'MECHANICAL') !== false || strpos($upper, 'MECH') !== false || strpos($upper, 'ME') !== false) {
            return 'ME';
        }
        if (strpos($upper, 'ENVIRONMENT') !== false || strpos($upper, 'ENV') !== false) {
            return 'ENV';
        }
        if (strpos($upper, 'INFORMATION') !== false || strpos($upper, 'IT') !== false) {
            return 'IT';
        }
        if (strpos($upper, 'AGRICULTURE') !== false) {
            return 'AGRI';
        }
        if (strpos($upper, 'MBA') !== false) {
            return 'MBA';
        }
        if (strpos($upper, 'MCA') !== false) {
            return 'MCA';
        }
        return $name;
    }
}
