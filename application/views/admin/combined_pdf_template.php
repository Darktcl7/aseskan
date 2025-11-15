<?php
// Helper function to prevent XSS attacks.
function eh($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Calculate percentages for responses
$rSenang = (int)($combined['total_response_senang'] ?? 0);
$rBiasa = (int)($combined['total_response_biasa'] ?? 0);
$rSedih = (int)($combined['total_response_sedih'] ?? 0);
$totalResponses = $rSenang + $rBiasa + $rSedih;
$pSenang = $totalResponses > 0 ? round(($rSenang / $totalResponses) * 100, 1) : 0;
$pBiasa = $totalResponses > 0 ? round(($rBiasa / $totalResponses) * 100, 1) : 0;
$pSedih = $totalResponses > 0 ? round(($rSedih / $totalResponses) * 100, 1) : 0;

// Prepare width styles
$widthSenang = $pSenang . '%';
$widthBiasa = $pBiasa . '%';
$widthSedih = $pSedih . '%';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Combined Report - <?= eh($project_name); ?></title>
    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            background-color: #344767;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 1px 4px;
            background-color: #17c1e8;
            color: white;
            border-radius: 2px;
            font-size: 6px;
            margin: 0 2px;
        }

        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #344767;
            background-color: #f0f2f5;
            padding: 6px 10px;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .stats-label {
            font-size: 9px;
            color: #666;
            display: block;
            margin-bottom: 3px;
        }

        .stats-value {
            font-size: 16px;
            font-weight: bold;
            color: #344767;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            border-bottom: 1px solid #e9ecef;
            padding: 8px;
            text-align: left;
        }

        .table tr:last-child th,
        .table tr:last-child td {
            border-bottom: none;
        }

        .table th {
            background-color: #f8f9fa;
            width: 40%;
            font-weight: bold;
        }

        .table td {
            background-color: #fff;
            width: 60%;
        }

        .progress-bar-container {
            width: 100%;
            height: 15px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 3px 0;
        }

        .progress-bar {
            height: 100%;
            text-align: center;
            color: white;
            font-size: 8px;
            line-height: 15px;
        }

        .progress-success {
            background-color: #82d616;
        }

        .progress-warning {
            background-color: #fb8c00;
        }

        .progress-danger {
            background-color: #ea0606;
        }

        .comment-box {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 4px;
            border-left: 3px solid #17c1e8;
        }

        .comment-header {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
        }

        .comment-body {
            font-size: 9px;
            margin-bottom: 3px;
        }

        .comment-footer {
            font-size: 8px;
            color: #666;
        }

        .two-column {
            display: table;
            width: 100%;
        }

        .column {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }

        /* Responsive for mobile devices */
        @media (max-width: 600px) {
            .two-column {
                display: block;
            }

            .column {
                display: block;
                width: 100%;
                padding: 10px 0;
            }

            .column img {
                max-height: 200px !important;
            }

            .section-title {
                font-size: 12px;
            }

            .table th,
            .table td {
                font-size: 8px;
                padding: 4px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Aseskan</h1>
            <p><?= eh($project_name); ?></p>
        </div>

        <!-- Preview Panel -->
        <div class="section">
            <div class="section-title">Preview Panel</div>
            <table class="table">
                <tr>
                    <th>Man</th>
                    <td><?= $combined['total_male_count']; ?></td>
                </tr>
                <tr>
                    <th>Woman</th>
                    <td><?= $combined['total_female_count']; ?></td>
                </tr>
                <tr>
                    <th>Average Age</th>
                    <td><?= $combined['average_age']; ?></td>
                </tr>
            </table>
        </div>

        <!-- Preview Engagement Metrics -->
        <div class="section">
            <div class="section-title">Preview Engagement Metrics</div>
            <table class="table">
                <tr>
                    <th>Likeable (Video)</th>
                    <td><?= $combined['total_love_count']; ?></td>
                </tr>
                <tr>
                    <th>Shareable (Video)</th>
                    <td><?= $combined['total_share_count']; ?></td>
                </tr>
                <tr>
                    <th>Likes (Comment)</th>
                    <td>
                        <?php 
                        $total_comment_likes = 0;
                        foreach ($all_comments as $comment) {
                            if ($comment['is_like'] == 1) $total_comment_likes++;
                        }
                        echo $total_comment_likes;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Dislikes (Comment)</th>
                    <td>
                        <?php 
                        $total_comment_dislikes = 0;
                        foreach ($all_comments as $comment) {
                            if ($comment['is_dislike'] == 1) $total_comment_dislikes++;
                        }
                        echo $total_comment_dislikes;
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Preview Responses -->
        <div class="section">
            <div class="section-title">Preview Responses</div>
            <table class="table">
                <tr>
                    <th>Likes</th>
                    <td><?= $pSenang; ?>%</td>
                </tr>
                <tr>
                    <th>Neutral</th>
                    <td><?= $pBiasa; ?>%</td>
                </tr>
                <tr>
                    <th>Dislikes</th>
                    <td><?= $pSedih; ?>%</td>
                </tr>
            </table>
        </div>

        <!-- Preview Preferences -->
        <div class="section">
            <div class="section-title">Preview Preferences</div>
            <table class="table">
                <tr>
                    <th>Satisfied</th>
                    <td><?= $combined['total_pref_senang']; ?></td>
                </tr>
                <tr>
                    <th>Normal</th>
                    <td><?= $combined['total_pref_biasa']; ?></td>
                </tr>
                <tr>
                    <th>Disappointed</th>
                    <td><?= $combined['total_pref_marah']; ?></td>
                </tr>
            </table>
        </div>

        <!-- Charts Section -->
        <div class="section">
            <div class="section-title">Visual Charts</div>
            <div class="two-column">
                <div class="column">
                    <strong style="display: block; margin-bottom: 5px;">Responses Chart:</strong>
                    <?php if (isset($responseChartImg)): ?>
                        <img src="<?= $responseChartImg ?>" alt="Responses Chart" style="width: 100%; max-height: 250px;">
                    <?php else: ?>
                        <p style="text-align: center; color: #666;">Chart not available</p>
                    <?php endif; ?>
                </div>
                <div class="column">
                    <strong style="display: block; margin-bottom: 5px;">Preferences Chart:</strong>
                    <?php if (isset($preferencesChartImg)): ?>
                        <img src="<?= $preferencesChartImg ?>" alt="Preferences Chart" style="width: 100%; max-height: 250px;">
                    <?php else: ?>
                        <p style="text-align: center; color: #666;">Chart not available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Preview Timeline -->
        <div class="section">
            <div class="section-title">Preview Timeline (Love & Share)</div>
            <?php if (isset($engagementChartImg)): ?>
                <img src="<?= $engagementChartImg ?>" alt="Timeline Chart" style="width: 100%; max-height: 300px;">
            <?php else: ?>
                <p style="text-align: center; color: #666;">Chart not available</p>
            <?php endif; ?>
        </div>

        <!-- Sessions Breakdown -->
        <div class="section">
            <div class="section-title">Sessions Breakdown</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>Date</th>
                        <th>Resp.</th>
                        <th>Loves</th>
                        <th>Shares</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($combined['sessions'] as $session): 
                        $session_respondents = intval($session['session_male_count']) + intval($session['session_female_count']);
                    ?>
                        <tr>
                            <td><?= eh($session['nama_video']); ?></td>
                            <td><?= date('d M Y', strtotime($session['last_updated'])); ?></td>
                            <td><?= $session_respondents; ?></td>
                            <td><?= $session['love_count']; ?></td>
                            <td><?= $session['share_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- All Comments -->
        <?php if (!empty($all_comments)): ?>
            <div class="section">
                <div class="section-title">All Comments (<?= count($all_comments); ?> total)</div>
                <?php 
                $displayed_comments = 0;
                foreach ($all_comments as $comment): 
                    if ($displayed_comments >= 30) break;
                ?>
                    <div class="comment-box">
                        <div class="comment-header">
                            <?= eh($comment['username']); ?> 
                            <span class="badge">Session <?= $comment['session_number']; ?></span>
                            <?php if ($comment['is_like'] == 1): ?>
                                <span style="color: green; font-weight: bold;">[Like]</span>
                            <?php elseif ($comment['is_dislike'] == 1): ?>
                                <span style="color: red; font-weight: bold;">[Dislike]</span>
                            <?php endif; ?>
                        </div>
                        <div class="comment-body"><?= eh($comment['komentar']); ?></div>
                        <div class="comment-footer"><?= date('d M Y, H:i', strtotime($comment['created_at'])); ?></div>
                    </div>
                <?php 
                    $displayed_comments++;
                endforeach; 
                ?>
                <?php if (count($all_comments) > 30): ?>
                    <div style="text-align: center; font-style: italic; margin-top: 10px;">
                        ... and <?= count($all_comments) - 30; ?> more comments
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e9ecef; font-size: 8px; color: #666;">
            <p>Generated on <?= date('d F Y, H:i:s'); ?></p>
            <p>Aseskan Combined Report &copy; 2025</p>
        </div>
    </div>
</body>

</html>
