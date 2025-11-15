<?php
// Helper function to prevent XSS attacks.
function eh($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Calculate percentages for responses
$rSenang = (int)($report['response_senang'] ?? 0);
$rBiasa = (int)($report['response_biasa'] ?? 0);
$rSedih = (int)($report['response_sedih'] ?? 0);
$totalResponses = $rSenang + $rBiasa + $rSedih;
$pSenang = $totalResponses > 0 ? round(($rSenang / $totalResponses) * 100, 1) : 0;
$pBiasa = $totalResponses > 0 ? round(($rBiasa / $totalResponses) * 100, 1) : 0;
$pSedih = $totalResponses > 0 ? round(($rSedih / $totalResponses) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Aseskan - <?= eh($report['nama_video']); ?></title>
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
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
        }

        .content {
            padding: 10px 0;
        }

        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #344767;
            background-color: #f0f2f5;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 6px;
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

        .comments-section .comment {
            border-bottom: 1px solid #e9ecef;
            padding: 6px 0;
        }

        .comments-section .comment:last-child {
            border-bottom: none;
        }

        .comment-user {
            font-weight: bold;
            color: #000;
        }

        .comments-section {
            margin-top: 20px;
        }

        .chart-table {
            width: 100%;
            border-spacing: 10px;
            border-collapse: separate;
            page-break-inside: avoid;
        }

        .chart-table td {
            width: 50%;
            vertical-align: top;
        }

        .chart-container {
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .chart-container .section-title {
            font-size: 12px;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .chart-container img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .timeline-chart-container {
            width: 100%;
            page-break-inside: avoid;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9px;
            color: #777;
        }

        /* Add this media query for mobile devices */
        @media (max-width: 600px) {
            .chart-table td {
                display: block;
                width: 100%;
            }

            .chart-container img,
            .timeline-chart-container img {
                max-width: 80%;
                /* or any other value that suits your design */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Aseskan</h1>
            <p><?= eh($report['nama_video']); ?></p>
        </div>

        <div class="content">

            <div class="section">
                <div class="section-title">Preview Panel</div>
                <table class="table">
                    <tr>
                        <th>Man</th>
                        <td><?= eh($report['session_male_count'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Woman</th>
                        <td><?= eh($report['session_female_count'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Average Age</th>
                        <td><?= eh(round($report['session_average_age'] ?? 0, 1)); ?></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Preview Engagement Metrics</div>
                <table class="table">
                    <tr>
                        <th>Likeable (Video)</th>
                        <td><?= eh($report['love_count'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Shareable (Video)</th>
                        <td><?= eh($report['share_count'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Likes (Comment)</th>
                        <td><?= eh($comment_stats['likes'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Dislikes (Comment)</th>
                        <td><?= eh($comment_stats['dislikes'] ?? 0); ?></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Preview Responses</div>
                <table class="table">
                    <tr>
                        <th>Likes</th>
                        <td><?= eh($pSenang); ?>%</td>
                    </tr>
                    <tr>
                        <th>Neutral</th>
                        <td><?= eh($pBiasa); ?>%</td>
                    </tr>
                    <tr>
                        <th>Dislikes</th>
                        <td><?= eh($pSedih); ?>%</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Preview Preferences</div>
                <table class="table">
                    <tr>
                        <th>Satisfied</th>
                        <td><?= eh($report['pref_senang'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Normal</th>
                        <td><?= eh($report['pref_biasa'] ?? 0); ?></td>
                    </tr>
                    <tr>
                        <th>Disappointed</th>
                        <td><?= eh($report['pref_marah'] ?? 0); ?></td>
                    </tr>
                </table>
            </div>

            <table class="chart-table">
                <tr>
                    <td>
                        <div class="chart-container">
                            <div class="section-title">Responses Chart</div>
                            <?php if (isset($responseChartImg)): ?>
                                <img src="<?= $responseChartImg ?>" alt="Responses Chart">
                            <?php else: ?>
                                <p>Chart not available.</p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="chart-container">
                            <div class="section-title">Preferences Chart</div>
                            <?php if (isset($preferencesChartImg)): ?>
                                <img src="<?= $preferencesChartImg ?>" alt="Preferences Chart">
                            <?php else: ?>
                                <p>Chart not available.</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="timeline-chart-container">
                <div class="section-title">Preview Timeline</div>
                <div class="chart-container">
                    <?php if (isset($engagementChartImg)): ?>
                        <img src="<?= $engagementChartImg ?>" alt="Engagement Chart" style="width: 100%;">
                    <?php else: ?>
                        <p>Chart not available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="comments-section">
                <div class="section-title">Preview Comments</div>
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <span class="comment-user"><?= eh($comment['username']); ?>:</span>
                            <span><?= eh($comment['komentar']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada komentar.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            Laporan dihasilkan pada <?= date('d M Y, H:i'); ?>
        </div>
    </div>
</body>

</html>