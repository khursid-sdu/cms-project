<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $years */
/** @var string|null $selectedYear */
/** @var array $courses */
/** @var int|null $selectedCourse */
/** @var array $branches */
/** @var int|null $selectedBranch */
/** @var array $sections */
/** @var int|null $selectedSection */
/** @var array $reportData */

$this->title = 'TR Generation Report';
$this->params['breadcrumbs'][] = ['label' => 'TR Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tr-summary-report py-2">
    <!-- Header Banner -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>TR Generation Status Report
            </h2>
            <p class="text-muted small mb-0">Tabulation Register Progression Summary (Batch &rarr; Course &rarr; Branch &rarr; Section)</p>
        </div>
        <div class="btn-group shadow-sm">
            <a id="btn-export-csv" href="<?= Url::to(['export', 'year' => $selectedYear, 'course_id' => $selectedCourse, 'branch_id' => $selectedBranch, 'section_id' => $selectedSection]) ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel
            </a>
            <!-- <button type="button" onclick="window.print();" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Print
            </button> -->
        </div>
    </div>

    <!-- Filter Card: Cascading Dropdowns -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-semibold text-secondary">
                <i class="bi bi-funnel-fill text-primary me-1"></i> Cascading Filter Options
            </h5>
        </div>
        <div class="card-body">
            <form id="tr-filter-form" class="row g-3 align-items-end">
                <!-- 1. Select Batch (Year) -->
                <div class="col-md-3">
                    <label for="select-batch" class="form-label fw-bold small text-uppercase text-secondary">
                        <span class="badge bg-primary me-1">1</span> Select Batch
                    </label>
                    <select id="select-batch" name="batch_year" class="form-select form-select-lg shadow-sm">
                        <option value="">-- Select Batch --</option>
                        <?php foreach ($years as $yr => $lbl): ?>
                            <option value="<?= Html::encode($yr) ?>" <?= ($selectedYear == $yr) ? 'selected' : '' ?>>
                                <?= Html::encode($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Select Course (Filtered by Batch Year) -->
                <div class="col-md-3">
                    <label for="select-course" class="form-label fw-bold small text-uppercase text-secondary">
                        <span class="badge bg-primary me-1">2</span> Select Course
                    </label>
                    <select id="select-course" name="course_id" class="form-select form-select-lg shadow-sm">
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $cId => $cName): ?>
                            <option value="<?= Html::encode($cId) ?>" <?= ($selectedCourse == $cId) ? 'selected' : '' ?>>
                                <?= Html::encode($cName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 3. Select Branch (Filtered by Course) -->
                <div class="col-md-3">
                    <label for="select-branch" class="form-label fw-bold small text-uppercase text-secondary">
                        <span class="badge bg-primary me-1">3</span> Select Branch
                    </label>
                    <select id="select-branch" name="branch_id" class="form-select form-select-lg shadow-sm">
                        <option value="">-- Select Branch --</option>
                        <?php if (!empty($branches)): ?>
                            <option value="all" <?= ($selectedBranch === 'all') ? 'selected' : '' ?>>-- All Branches --</option>
                            <?php foreach ($branches as $bId => $bName): ?>
                                <option value="<?= Html::encode($bId) ?>" <?= ($selectedBranch == $bId) ? 'selected' : '' ?>>
                                    <?= Html::encode($bName) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 4. Select Section (Filtered by Branch) -->
                <div class="col-md-3">
                    <label for="select-section" class="form-label fw-bold small text-uppercase text-secondary">
                        <span class="badge bg-primary me-1">4</span> Select Section
                    </label>
                    <select id="select-section" name="section_id" class="form-select form-select-lg shadow-sm">
                        <option value="">-- Select Section --</option>
                        <?php if (!empty($sections)): ?>
                            <option value="all" <?= ($selectedSection === 'all') ? 'selected' : '' ?>>-- All Sections --</option>
                            <?php foreach ($sections as $sId => $sName): ?>
                                <option value="<?= Html::encode($sId) ?>" <?= ($selectedSection == $sId) ? 'selected' : '' ?>>
                                    <?= Html::encode($sName) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-12 text-end pt-2">
                    <span id="filter-spinner" class="spinner-border spinner-border-sm text-primary me-2 d-none" role="status"></span>
                    <button type="button" id="btn-generate" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-search me-1"></i> Generate Report
                    </button>
                    <button type="button" id="btn-reset" class="btn btn-outline-secondary px-3 ms-2">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0" id="tr-report-table-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-table text-primary me-2"></i>TR Report Details
            </h5>
            <span class="badge bg-light text-dark border" id="table-row-count">
                Showing <?= count($reportData['rows']) ?> record(s)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle mb-0 text-center" id="tr-report-table">
                    <thead class="table-light fw-bold">
                        <tr>
                            <th style="width: 50px;">Sl</th>
                            <th>Batch</th>
                            <th>Course</th>
                            <th>Branch</th>
                            <th>Section</th>
                            <th class="table-primary text-dark">Total Strength</th>
                            <th class="table-danger text-danger">Backlog Students</th>
                            <th class="table-info text-info">TR Eligible Students</th>
                            <th class="table-success text-success">TR Generated</th>
                            <th class="table-warning text-warning">TR Pending</th>
                        </tr>
                    </thead>
                    <tbody id="tr-table-body">
                        <?php if (empty($reportData['rows'])): ?>
                            <tr>
                                <td colspan="10" class="text-muted py-5 text-center">
                                    <i class="bi bi-info-circle fs-4 text-primary d-block mb-2"></i>
                                    Please select filter options and click <strong>Generate Report</strong> to view data.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData['rows'] as $row): ?>
                                <tr>
                                    <td><?= Html::encode($row['sl']) ?></td>
                                    <td class="fw-semibold"><?= Html::encode($row['batch']) ?></td>
                                    <td>
                                        <span title="<?= Html::encode($row['course_full']) ?>">
                                            <?= Html::encode($row['course']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span title="<?= Html::encode($row['branch_full']) ?>">
                                            <?= Html::encode($row['branch']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-start ps-3"><?= Html::encode($row['section']) ?></td>
                                    <td class="fw-bold"><?= number_format($row['total_strength']) ?></td>
                                    <td class="fw-bold text-danger"><?= number_format($row['backlog_students']) ?></td>
                                    <td class="fw-bold text-primary"><?= number_format($row['tr_eligible_students']) ?></td>
                                    <td class="fw-bold text-success"><?= number_format($row['tr_generated']) ?></td>
                                    <td class="fw-bold text-warning"><?= number_format($row['tr_pending']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark fw-bold" id="tr-table-footer">
                        <tr>
                            <td colspan="5" class="text-end pe-3">TOTAL:</td>
                            <td id="tot-strength"><?= number_format($reportData['totals']['total_strength']) ?></td>
                            <td id="tot-backlog" class="text-danger"><?= number_format($reportData['totals']['backlog_students']) ?></td>
                            <td id="tot-eligible" class="text-info"><?= number_format($reportData['totals']['tr_eligible_students']) ?></td>
                            <td id="tot-generated" class="text-success"><?= number_format($reportData['totals']['tr_generated']) ?></td>
                            <td id="tot-pending" class="text-warning"><?= number_format($reportData['totals']['tr_pending']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* In-Page CSS for TR Generation Status Report */
.tr-summary-report {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.tr-summary-report .card {
    border-radius: 8px;
}
.tr-summary-report .form-select-lg {
    font-size: 1rem;
}
#tr-report-table th {
    vertical-align: middle;
    font-size: 0.92rem;
    letter-spacing: 0.2px;
}
#tr-report-table td {
    font-size: 0.92rem;
}
#tr-report-table tfoot td {
    font-size: 0.95rem;
}
@media print {
    #header, #footer, #tr-filter-form, .btn-group, .breadcrumb {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: #fff !important;
        padding: 0 !important;
    }
}
</style>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Cascading Dropdowns, Live AJAX & PDF Export Script -->
<?php
$getCoursesUrl  = Url::to(['get-courses']);
$getBranchesUrl = Url::to(['get-branches']);
$getSectionsUrl = Url::to(['get-sections']);
$getDataUrl     = Url::to(['get-data']);
$exportBaseUrl  = Url::to(['export']);

$js = <<<JS
$(function() {
    var \$batch   = $('#select-batch');
    var \$course  = $('#select-course');
    var \$branch  = $('#select-branch');
    var \$section = $('#select-section');
    var \$spinner = $('#filter-spinner');
    var \$export  = $('#btn-export-csv');

    function updateExportLink() {
        var sep = '{$exportBaseUrl}'.indexOf('?') !== -1 ? '&' : '?';
        var url = '{$exportBaseUrl}' + sep + 
            'year=' + encodeURIComponent(\$batch.val() || '') + 
            '&course_id=' + encodeURIComponent(\$course.val() || '') + 
            '&branch_id=' + encodeURIComponent(\$branch.val() || '') + 
            '&section_id=' + encodeURIComponent(\$section.val() || '');
        \$export.attr('href', url);
    }

    // 1. When Batch changes -> Load Courses only (do not auto-load report data)
    \$batch.on('change', function() {
        var year = $(this).val();
        \$course.html('<option value="">-- Loading Courses... --</option>');
        \$branch.html('<option value="">-- Select Branch --</option>');
        \$section.html('<option value="">-- Select Section --</option>');
        
        if (!year) {
            \$course.html('<option value="">-- Select Course --</option>');
            return;
        }

        \$spinner.removeClass('d-none');
        $.getJSON('{$getCoursesUrl}', { year: year }, function(res) {
            \$spinner.addClass('d-none');
            if (res.success) {
                var opts = '<option value="">-- Select Course --</option>';
                $.each(res.data, function(id, name) {
                    opts += '<option value="' + id + '">' + name + '</option>';
                });
                \$course.html(opts);
            } else {
                \$course.html('<option value="">-- Select Course --</option>');
            }
        }).fail(function() {
            \$spinner.addClass('d-none');
            \$course.html('<option value="">-- Select Course --</option>');
        });
    });

    // 2. When Course changes -> Load Branches only (do not auto-load report data)
    \$course.on('change', function() {
        var courseId = $(this).val();
        \$branch.html('<option value="">-- Loading Branches... --</option>');
        \$section.html('<option value="">-- Select Section --</option>');

        if (!courseId) {
            \$branch.html('<option value="">-- Select Branch --</option>');
            return;
        }

        \$spinner.removeClass('d-none');
        $.getJSON('{$getBranchesUrl}', { course_id: courseId }, function(res) {
            \$spinner.addClass('d-none');
            if (res.success) {
                var opts = '<option value="">-- Select Branch --</option>';
                opts += '<option value="all">-- All Branches --</option>';
                $.each(res.data, function(id, name) {
                    opts += '<option value="' + id + '">' + name + '</option>';
                });
                \$branch.html(opts);
            } else {
                \$branch.html('<option value="">-- Select Branch --</option>');
            }
        }).fail(function() {
            \$spinner.addClass('d-none');
            \$branch.html('<option value="">-- Select Branch --</option>');
        });
    });

    // 3. When Branch changes -> Load Sections only (do not auto-load report data)
    \$branch.on('change', function() {
        var branchId = $(this).val();
        var courseId = \$course.val();
        \$section.html('<option value="">-- Loading Sections... --</option>');

        if (!branchId || branchId === 'all') {
            if (branchId === 'all') {
                \$section.html('<option value="">-- Select Section --</option><option value="all">-- All Sections --</option>');
            } else {
                \$section.html('<option value="">-- Select Section --</option>');
            }
            return;
        }

        \$spinner.removeClass('d-none');
        $.getJSON('{$getSectionsUrl}', { course_id: courseId, branch_id: branchId }, function(res) {
            \$spinner.addClass('d-none');
            if (res.success) {
                var opts = '<option value="">-- Select Section --</option>';
                opts += '<option value="all">-- All Sections --</option>';
                $.each(res.data, function(id, name) {
                    opts += '<option value="' + id + '">' + name + '</option>';
                });
                \$section.html(opts);
            } else {
                \$section.html('<option value="">-- Select Section --</option>');
            }
        }).fail(function() {
            \$spinner.addClass('d-none');
            \$section.html('<option value="">-- Select Section --</option>');
        });
    });

    // 4. Generate button click -> Load report data
    $('#btn-generate').on('click', function() {
        var year = \$batch.val();
        var courseId = \$course.val();

        if (!year) {
            alert('Please select a Batch first.');
            \$batch.focus();
            return;
        }
        if (!courseId) {
            alert('Please select a Course.');
            \$course.focus();
            return;
        }

        loadReportData();
    });

    // 5. Reset button click -> Reset all dropdowns & clear table to prompt
    $('#btn-reset').on('click', function() {
        \$batch.val('');
        \$course.html('<option value="">-- Select Course --</option>');
        \$branch.html('<option value="">-- Select Branch --</option>');
        \$section.html('<option value="">-- Select Section --</option>');

        // Reset Table Body to prompt
        var emptyPrompt = '<tr>' +
            '<td colspan="10" class="text-muted py-5 text-center">' +
                '<i class="bi bi-info-circle fs-4 text-primary d-block mb-2"></i>' +
                'Please select filter options and click <strong>Generate Report</strong> to view data.' +
            '</td>' +
        '</tr>';
        $('#tr-table-body').html(emptyPrompt);
        $('#table-row-count').text('Showing 0 record(s)');

        // Reset Totals Row
        $('#tot-strength').text('0');
        $('#tot-backlog').text('0');
        $('#tot-eligible').text('0');
        $('#tot-generated').text('0');
        $('#tot-pending').text('0');

        updateExportLink();
    });

    // Function to fetch report data and render table
    function loadReportData() {
        var year     = \$batch.val();
        var courseId = \$course.val();
        var branchId = \$branch.val();
        var secId    = \$section.val();

        updateExportLink();

        \$spinner.removeClass('d-none');
        $('#btn-generate').prop('disabled', true);

        $.getJSON('{$getDataUrl}', {
            year: year,
            course_id: courseId,
            branch_id: branchId,
            section_id: secId
        }, function(res) {
            \$spinner.addClass('d-none');
            $('#btn-generate').prop('disabled', false);

            if (!res.success) return;

            var d = res.data;
            var rows = d.rows;
            var totals = d.totals;

            $('#table-row-count').text('Showing ' + rows.length + ' record(s)');

            // Render Table Body
            var html = '';
            if (!rows || rows.length === 0) {
                html = '<tr><td colspan="10" class="text-muted py-4 text-center">No records found for the selected criteria.</td></tr>';
            } else {
                $.each(rows, function(idx, r) {
                    html += '<tr>' +
                        '<td>' + r.sl + '</td>' +
                        '<td class="fw-semibold">' + r.batch + '</td>' +
                        '<td><span title="' + r.course_full + '">' + r.course + '</span></td>' +
                        '<td><span title="' + r.branch_full + '">' + r.branch + '</span></td>' +
                        '<td class="fw-semibold text-start ps-3">' + r.section + '</td>' +
                        '<td class="fw-bold">' + Number(r.total_strength).toLocaleString() + '</td>' +
                        '<td class="fw-bold text-danger">' + Number(r.backlog_students).toLocaleString() + '</td>' +
                        '<td class="fw-bold text-primary">' + Number(r.tr_eligible_students).toLocaleString() + '</td>' +
                        '<td class="fw-bold text-success">' + Number(r.tr_generated).toLocaleString() + '</td>' +
                        '<td class="fw-bold text-warning">' + Number(r.tr_pending).toLocaleString() + '</td>' +
                    '</tr>';
                });
            }
            $('#tr-table-body').html(html);

            // Update Totals Row
            $('#tot-strength').text(Number(totals.total_strength).toLocaleString());
            $('#tot-backlog').text(Number(totals.backlog_students).toLocaleString());
            $('#tot-eligible').text(Number(totals.tr_eligible_students).toLocaleString());
            $('#tot-generated').text(Number(totals.tr_generated).toLocaleString());
            $('#tot-pending').text(Number(totals.tr_pending).toLocaleString());
        }).fail(function() {
            \$spinner.addClass('d-none');
            $('#btn-generate').prop('disabled', false);
            alert('Failed to load report data. Please try again.');
        });
    }
});
JS;

$this->registerJs($js);
