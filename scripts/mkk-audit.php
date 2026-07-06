<?php
/**
 * MKK Audit Script — Read-only, just displays current FK + Meta for all 31 posts
 * DELETE after use.
 */
require_once(__DIR__ . '/wp-load.php');
if (!current_user_can('manage_options')) die('Admin required.');

$all_ids = [1642,1581,1696,1382,1562,1588,1654,164,1682,1690,1702,1708,1710,1719,1742,3165,3372,9502,10068,10075,3166,1951,3374,1851,3375,943,1207,3377,1785,3370,12091];
$list_a = [1642,1581,1696,1382,1562,1588,1654,164,1682,1690,1702,1708,1710,1719,1742,3165,3372,9502,10068,10075,3166,1951];

$media_map = [12107=>1642,12106=>1562,12105=>1588,12104=>1654,12103=>164,12102=>1682,12101=>1719,12100=>3374,12099=>1851,12098=>3375,12097=>1785,12096=>1207,12095=>3370,12094=>12091];

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>MKK Audit</title>";
echo "<style>body{font-family:monospace;padding:20px;max-width:1600px;margin:0 auto}table{border-collapse:collapse;width:100%;font-size:12px}th,td{border:1px solid #ccc;padding:5px 8px;text-align:left}th{background:#e8e8e8}.err{color:red;font-weight:bold}.ok{color:green}.warn{color:#cc7700}</style></head><body>";
echo "<h1>MKK Live Audit — FK + Meta + Alt Text</h1>";

echo "<h2>Posts (31)</h2>";
echo "<table><tr><th>#</th><th>List</th><th>ID</th><th>Title</th><th>FK (live)</th><th>Meta Description (live)</th><th>Meta Len</th><th>Manglish 1st Line</th></tr>";

$row = 0;
foreach ($all_ids as $pid) {
    $row++;
    $list = in_array($pid, $list_a) ? 'A' : 'B';
    $post = get_post($pid);
    if (!$post) { echo "<tr><td>$row</td><td>$list</td><td>$pid</td><td colspan='5' class='err'>NOT FOUND</td></tr>"; continue; }

    $fk = get_post_meta($pid, 'rank_math_focus_keyword', true);
    $meta = get_post_meta($pid, 'rank_math_description', true);
    $meta_len = mb_strlen($meta);

    // Extract Manglish first line for comparison
    $content = $post->post_content;
    $first_line = '—';
    $pos = mb_stripos($content, 'Manglish Lyrics');
    if ($pos !== false) {
        $after = mb_substr($content, $pos);
        if (preg_match('/<p[^>]*>(.*?)<\/p>/si', $after, $m)) {
            $text = str_ireplace(['<br />', '<br/>', '<br>'], "\n", $m[1]);
            $text = strip_tags($text);
            foreach (explode("\n", $text) as $line) {
                $line = trim($line);
                if ($line === '' || preg_match('/^\d+\.?$/', $line) || preg_match('/^(pallavi|charanangal|chorus)$/i', $line)) continue;
                $first_line = $line;
                break;
            }
        }
    }

    $title = mb_substr($post->post_title, 0, 50);
    $fk_class = empty($fk) ? 'err' : 'ok';
    $meta_class = ($meta_len > 160) ? 'err' : (($meta_len == 0) ? 'err' : 'ok');

    echo "<tr>";
    echo "<td>$row</td><td>$list</td><td>$pid</td>";
    echo "<td>" . esc_html($title) . "</td>";
    echo "<td class='$fk_class'>" . esc_html($fk ?: 'EMPTY') . "</td>";
    echo "<td>" . esc_html($meta ?: 'EMPTY') . "</td>";
    echo "<td class='$meta_class'>$meta_len</td>";
    echo "<td style='font-size:11px;color:#666'>" . esc_html(mb_substr($first_line, 0, 60)) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Media Alt Text (14)</h2>";
echo "<table><tr><th>#</th><th>Media ID</th><th>Post ID</th><th>Alt Text (live)</th></tr>";
$mc = 0;
foreach ($media_map as $mid => $pid) {
    $mc++;
    $alt = get_post_meta($mid, '_wp_attachment_image_alt', true);
    echo "<tr><td>$mc</td><td>$mid</td><td>$pid</td><td>" . esc_html($alt ?: 'EMPTY') . "</td></tr>";
}
echo "</table>";

echo "<p style='margin-top:30px;color:red;font-weight:bold'>DELETE THIS FILE: rm mkk-audit.php</p>";
echo "</body></html>";
