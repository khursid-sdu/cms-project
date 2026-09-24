<?php

namespace app\modules\academics\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\db\Query;
use app\modules\academics\models\TrReport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * ReportController handles single-page TR summary reports, cascading AJAX requests,
 * and data export within the academics module.
 */
class ReportController extends Controller
{

    /**
     * Primary action: TR generation summary report page
     */
    public function actionIndex()
    {
        $years = TrReport::getBatchYears();
        $selectedYear    = Yii::$app->request->get('batch_year', null);
        $selectedCourse  = Yii::$app->request->get('course_id', null);
        $selectedBranch  = Yii::$app->request->get('branch_id', null);
        $selectedSection = Yii::$app->request->get('section_id', null);

        // Courses for the selected year (if any)
        $courses = !empty($selectedYear) ? TrReport::getCoursesByYear($selectedYear) : [];

        // Branches for the selected course (if any)
        $branches = !empty($selectedCourse) ? TrReport::getBranchesByCourse($selectedCourse) : [];

        // Sections for the selected branch (if any)
        $sections = !empty($selectedBranch) ? TrReport::getSectionsByBranch($selectedCourse, $selectedBranch) : [];

        // Only calculate report if user explicitly submitted/requested
        if (!empty($selectedYear) && !empty($selectedCourse)) {
            $reportData = TrReport::calculateReport($selectedYear, $selectedCourse, $selectedBranch, $selectedSection);
        } else {
            $reportData = [
                'rows' => [],
                'totals' => [
                    'total_strength'       => 0,
                    'backlog_students'     => 0,
                    'tr_eligible_students' => 0,
                    'tr_generated'         => 0,
                    'tr_pending'           => 0,
                ],
            ];
        }

        return $this->render('index', [
            'years'           => $years,
            'selectedYear'    => $selectedYear,
            'courses'         => $courses,
            'selectedCourse'  => $selectedCourse,
            'branches'        => $branches,
            'selectedBranch'  => $selectedBranch,
            'sections'        => $sections,
            'selectedSection' => $selectedSection,
            'reportData'      => $reportData,
        ]);
    }

    /**
     * Backward compatibility alias for tr-summary route
     */
    public function actionTrSummary()
    {
        return $this->actionIndex();
    }

    /**
     * AJAX endpoint: Get courses by batch year
     */
    public function actionGetCourses($year)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $courses = TrReport::getCoursesByYear($year);
        return [
            'success' => true,
            'data'    => $courses,
        ];
    }

    /**
     * AJAX endpoint: Get branches by course ID
     */
    public function actionGetBranches($course_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $branches = TrReport::getBranchesByCourse($course_id);
        return [
            'success' => true,
            'data'    => $branches,
        ];
    }

    /**
     * AJAX endpoint: Get sections by branch ID and course ID
     */
    public function actionGetSections($course_id = null, $branch_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sections = TrReport::getSectionsByBranch($course_id, $branch_id);
        return [
            'success' => true,
            'data'    => $sections,
        ];
    }

    /**
     * AJAX endpoint: Calculate and return report data & KPIs
     */
    public function actionGetData($year = null, $course_id = null, $branch_id = null, $section_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $report = TrReport::calculateReport($year, $course_id, $branch_id, $section_id);
        return [
            'success' => true,
            'data'    => $report,
        ];
    }

    /**
     * Export report data as Excel (.xlsx) using PhpOffice\PhpSpreadsheet
     */
    public function actionExport($year = null, $course_id = null, $branch_id = null, $section_id = null)
    {
        $report = TrReport::calculateReport($year, $course_id, $branch_id, $section_id);
        $filename = 'TR_Report_' . ($year ?: 'All') . '_' . date('Ymd_His') . '.xlsx';

        // Fallback to CSV if PhpSpreadsheet is not available
        if (!class_exists(Spreadsheet::class)) {
            $csvFile = 'TR_Report_' . ($year ?: 'All') . '_' . date('Ymd_His') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $csvFile . '"');
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Sl', 'Batch', 'Course', 'Branch', 'Section', 'Total Strength', 'Backlog Students', 'TR Eligible Students', 'TR Generated', 'TR Pending']);
            foreach ($report['rows'] as $row) {
                fputcsv($out, [$row['sl'], $row['batch'], $row['course'], $row['branch'], $row['section'], $row['total_strength'], $row['backlog_students'], $row['tr_eligible_students'], $row['tr_generated'], $row['tr_pending']]);
            }
            fputcsv($out, ['Total', '', '', '', '', $report['totals']['total_strength'], $report['totals']['backlog_students'], $report['totals']['tr_eligible_students'], $report['totals']['tr_generated'], $report['totals']['tr_pending']]);
            fclose($out);
            exit;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TR Summary');

        // Title row
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'TR Generation Status Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('0D6EFD');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle / filter metadata row
        $filterInfo = 'Batch: ' . ($year ?: 'All') . ' | Generated on: ' . date('d-M-Y H:i:s');
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('6C757D');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers (Row 4)
        $headers = [
            'A4' => 'Sl',
            'B4' => 'Batch',
            'C4' => 'Course',
            'D4' => 'Branch',
            'E4' => 'Section',
            'F4' => 'Total Strength',
            'G4' => 'Backlog Students',
            'H4' => 'TR Eligible Students',
            'I4' => 'TR Generated',
            'J4' => 'TR Pending',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'B0C4DE'],
                ],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // Data Rows
        $rowNum = 5;
        foreach ($report['rows'] as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['sl']);
            $sheet->setCellValue('B' . $rowNum, $row['batch']);
            $sheet->setCellValue('C' . $rowNum, $row['course']);
            $sheet->setCellValue('D' . $rowNum, $row['branch']);
            $sheet->setCellValue('E' . $rowNum, $row['section']);
            $sheet->setCellValue('F' . $rowNum, $row['total_strength']);
            $sheet->setCellValue('G' . $rowNum, $row['backlog_students']);
            $sheet->setCellValue('H' . $rowNum, $row['tr_eligible_students']);
            $sheet->setCellValue('I' . $rowNum, $row['tr_generated']);
            $sheet->setCellValue('J' . $rowNum, $row['tr_pending']);

            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("F{$rowNum}:J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()->setRGB('E0E0E0');

            $rowNum++;
        }

        // Totals Row
        $sheet->mergeCells("A{$rowNum}:E{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", 'TOTAL:');
        $sheet->setCellValue("F{$rowNum}", $report['totals']['total_strength']);
        $sheet->setCellValue("G{$rowNum}", $report['totals']['backlog_students']);
        $sheet->setCellValue("H{$rowNum}", $report['totals']['tr_eligible_students']);
        $sheet->setCellValue("I{$rowNum}", $report['totals']['tr_generated']);
        $sheet->setCellValue("J{$rowNum}", $report['totals']['tr_pending']);

        $sheet->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => '212529'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '212529']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '212529']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']],
            ],
        ]);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("F{$rowNum}:J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($rowNum)->setRowHeight(24);

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Clean output buffer to avoid corrupting Excel file
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
