<?php
/**
 * MKK Batch SEO + Cleanup Script — Session 11
 * thechristianlyrics.com
 *
 * Tasks:
 *  1. Set rank_math_focus_keyword for 22 LIST A posts (derive from Manglish first line)
 *  2. Update rank_math_description for all 31 posts (new template)
 *  3. Clear all 4 Elementor meta fields for all 31 posts
 *  4. Set alt_text on 14 media items = FK of associated post
 *
 * Usage:
 *   1. Upload to WordPress root (same folder as wp-load.php)
 *   2. Visit: yoursite.com/mkk-seo-batch-s11.php?mode=dry   (REVIEW first)
 *   3. Visit: yoursite.com/mkk-seo-batch-s11.php?mode=live  (EXECUTE)
 *   4. DELETE this file immediately after use
 */

require_once(__DIR__ . '/wp-load.php');

if (!current_user_can('manage_options')) {
    die('Admin access required. Please log in to WP admin first.');
}

$mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'dry';

// ==================== HTML HEADER ====================
?><!DOCTYPE html>
<html><head><meta charset="utf-8"><title>MKK Batch SEO — S11</title>
<style>
body { font-family: 'Courier New', monospace; padding: 20px; max-width: 1400px; margin: 0 auto; background: #fafafa; }
h1 { border-bottom: 3px solid #333; padding-bottom: 10px; }
h2 { margin-top: 40px; border-bottom: 2px solid #666; padding-bottom: 8px; }
table { border-collapse: collapse; width: 100%; margin: 15px 0; font-size: 13px; }
th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
th { background: #e8e8e8; }
tr:nth-child(even) { background: #f5f5f5; }
.ok { color: green; font-weight: bold; }
.warn { color: #cc7700; font-weight: bold; }
.err { color: red; font-weight: bold; }
.skip { color: #999; }
.fk-cell { max-width: 200px; word-break: break-word; }
.meta-cell { max-width: 350px; word-break: break-word; font-size: 11px; }
.live-banner { background: #ffe0e0; border: 3px solid red; padding: 15px; margin: 20px 0; text-align: center; font-size: 18px; }
.dry-banner { background: #e0f0ff; border: 3px solid #0066cc; padding: 15px; margin: 20px 0; text-align: center; font-size: 18px; }
</style></head><body>
<?php

echo $mode === 'live'
    ? '<div class="live-banner">LIVE MODE — Changes will be written to the database</div>'
    : '<div class="dry-banner">DRY RUN — No changes will be made. Review the output below.</div>';

echo "<h1>MKK Batch SEO Update — Session 11</h1>";

// ==================== CONFIGURATION ====================

$list_a_ids = [1642, 1581, 1696, 1382, 1562, 1588, 1654, 164, 1682, 1690, 1702, 1708, 1710, 1719, 1742, 3165, 3372, 9502, 10068, 10075, 3166, 1951];
$list_b_ids = [3374, 1851, 3375, 943, 1207, 3377, 1785, 3370, 12091];
$all_ids    = array_merge($list_a_ids, $list_b_ids);

// Media items: media_id => post_id (14 new images from S10)
$media_map = [
    12107 => 1642,  // Song 36
    12106 => 1562,  // Song 23
    12105 => 1588,  // Song 27
    12104 => 1654,  // Song 38
    12103 => 164,   // Song 39
    12102 => 1682,  // Song 42
    12101 => 1719,  // Song 48
    12100 => 3374,  // Song 117
    12099 => 1851,  // Song 64
    12098 => 3375,  // Song 118
    12097 => 1785,  // Song 55
    12096 => 1207,  // Song 429
    12095 => 3370,  // Song 113
    12094 => 12091, // Song 401
];

// Known lyricist tag slugs -> English display names
$lyricist_slug_map = [
    't-j-varkey'        => 'T.J. Varkey',
    'k-v-simon'         => 'K.V. Simon',
    'very-rev-t-n-koshy'=> 'Very Rev. T.N. Koshy',
    'i-k-kurian'        => 'I.K. Kurian',
    'rev-k-p-philip'    => 'Rev. K.P. Philip',
    'p-m-kochukuru'     => 'P.M. Kochukuru',
    'chekottasshan'     => 'Chekottasshan',
    'moshavatsalam'     => 'Moshavatsalam',
    'charles-john'      => 'Charles John',
    'mani-john-kochunj' => 'Mani John Kochunj',
    't-d-george'        => 'T.D. George',
    'p-d-john'          => 'P.D. John',
    'sadhu-kochoonju'   => 'Sadhu Kochoonju',
    'v-nagel'           => 'V. Nagel',
    'anonymous'         => '',
];

// ==================== HELPER FUNCTIONS ====================

function extract_manglish_title($raw_title) {
    $parts = preg_split('/\s+[-]+\s+/', $raw_title, 2);
    if (count($parts) < 2) {
        // Try en/em dash
        $parts = preg_split('/\s+[\x{2013}\x{2014}]\s+/u', $raw_title, 2);
        if (count($parts) < 2) return false;
    }
    $manglish = trim($parts[1]);
    return mb_convert_case($manglish, MB_CASE_TITLE, 'UTF-8');
}

function extract_manglish_first_line($content) {
    $pos = mb_stripos($content, 'Manglish Lyrics');
    if ($pos === false) return false;

    $after = mb_substr($content, $pos);

    if (!preg_match('/<p[^>]*>(.*?)<\/p>/si', $after, $m)) return false;

    $text = str_ireplace(['<br />', '<br/>', '<br>'], "\n", $m[1]);
    $text = strip_tags($text);

    foreach (explode("\n", $text) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (preg_match('/^\d+\.?$/', $line)) continue;
        if (preg_match('/^(pallavi|charanangal|chorus)$/i', $line)) continue;
        return $line;
    }
    return false;
}

function derive_fk($first_line) {
    if (empty($first_line)) return false;
    $words = preg_split('/\s+/', trim($first_line));
    if (count($words) < 2) return false;

    $n = 3;
    if (isset($words[2]) && mb_strlen($words[2]) <= 4) $n = 4;
    $slice = array_slice($words, 0, min($n, count($words)));

    $fk = mb_strtolower(implode(' ', $slice));
    return mb_strtoupper(mb_substr($fk, 0, 1)) . mb_substr($fk, 1);
}

function extract_lyricist($content) {
    global $lyricist_slug_map;

    if (!preg_match('/<tr>\s*<td>\s*Lyricist\s*<\/td>\s*<td>(.*?)<\/td>\s*<\/tr>/si', $content, $m)) {
        return false;
    }

    if (preg_match('/\/tag\/([^\/]+)\//', $m[1], $s)) {
        $slug = $s[1];
        if (isset($lyricist_slug_map[$slug]) && $lyricist_slug_map[$slug] !== '') {
            return $lyricist_slug_map[$slug];
        }
        return ucwords(str_replace('-', ' ', $slug));
    }

    $plain = trim(strip_tags($m[1]));
    return $plain !== '' ? $plain : false;
}

function build_meta($song_name, $lyricist) {
    if ($lyricist) {
        $meta = "{$song_name} lyrics from Marthoma Kristheeya Keerthanangal by {$lyricist}. Read Malayalam & Manglish lyrics and listen online.";
        if (mb_strlen($meta) <= 160) return $meta;
        $meta = "{$song_name} lyrics from Marthoma Kristheeya Keerthanangal by {$lyricist}. Read Malayalam & Manglish lyrics.";
        if (mb_strlen($meta) <= 160) return $meta;
    }
    $meta = "{$song_name} lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.";
    if (mb_strlen($meta) <= 160) return $meta;
    $meta = "{$song_name} lyrics from Marthoma Kristheeya Keerthanangal. Read Malayalam & Manglish lyrics.";
    if (mb_strlen($meta) <= 160) return $meta;
    $meta = "{$song_name} lyrics from Marthoma Kristheeya Keerthanangal.";
    return mb_substr($meta, 0, 160);
}

// ==================== TASK 1 and 2: FK + META + ELEMENTOR ====================

echo "<h2>Task 1 and 2: FK + Meta Description + Elementor Cleanup (31 posts)</h2>";
echo "<table>";
echo "<tr><th>#</th><th>List</th><th>ID</th><th>Title (excerpt)</th><th>Manglish 1st Line</th><th class='fk-cell'>New FK</th><th class='fk-cell'>Existing FK</th><th class='meta-cell'>New Meta</th><th>Len</th><th>Lyricist</th><th>Elem?</th><th>Action</th></tr>";

$post_fk_map = [];
$counters = ['fk_set' => 0, 'meta_set' => 0, 'elem_cleared' => 0, 'no_manglish' => 0, 'errors' => 0];

$row = 0;
foreach ($all_ids as $pid) {
    $row++;
    $list = in_array($pid, $list_a_ids) ? 'A' : 'B';
    $post = get_post($pid);

    if (!$post) {
        echo "<tr><td>{$row}</td><td>{$list}</td><td>{$pid}</td><td colspan='9' class='err'>POST NOT FOUND</td></tr>";
        $counters['errors']++;
        continue;
    }

    $title   = $post->post_title;
    $content = $post->post_content;

    $manglish_title = extract_manglish_title($title);
    $first_line     = extract_manglish_first_line($content);
    $new_fk         = derive_fk($first_line);
    $existing_fk    = get_post_meta($pid, 'rank_math_focus_keyword', true);
    $lyricist       = extract_lyricist($content);
    $song_name      = $manglish_title ?: preg_replace('/^\d+\.\s*/', '', $title);
    $new_meta       = build_meta($song_name, $lyricist);
    $meta_len       = mb_strlen($new_meta);

    $e1 = get_post_meta($pid, '_elementor_edit_mode', true);
    $e2 = get_post_meta($pid, '_elementor_template_type', true);
    $e3 = get_post_meta($pid, '_elementor_data', true);
    $e4 = get_post_meta($pid, '_elementor_page_settings', true);
    $has_elem = ($e1 !== '' || $e2 !== '' || $e3 !== '' || !empty($e4));

    $fk_to_set = $new_fk ?: $existing_fk;
    $post_fk_map[$pid] = $fk_to_set;

    $flags = [];
    if (!$first_line) { $flags[] = "<span class='warn'>NO MANGLISH</span>"; $counters['no_manglish']++; }
    if (empty($fk_to_set)) $flags[] = "<span class='err'>NO FK</span>";
    if ($meta_len > 160) $flags[] = "<span class='err'>META>160</span>";

    $actions = [];
    if ($fk_to_set && $fk_to_set !== $existing_fk) $actions[] = 'FK';
    $actions[] = 'META';
    if ($has_elem) $actions[] = 'ELEM';
    $action_str = implode('+', $actions);

    if (empty($flags)) $flags[] = "<span class='ok'>OK</span>";

    echo "<tr>";
    echo "<td>{$row}</td><td>{$list}</td><td>{$pid}</td>";
    echo "<td>" . esc_html(mb_substr($title, 0, 45)) . "</td>";
    echo "<td style='font-size:11px'>" . esc_html($first_line ?: '---') . "</td>";
    echo "<td class='fk-cell'>" . esc_html($new_fk ?: '---') . "</td>";
    echo "<td class='fk-cell'>" . esc_html($existing_fk ?: '---') . "</td>";
    echo "<td class='meta-cell'>" . esc_html($new_meta) . "</td>";
    echo "<td" . ($meta_len > 160 ? " class='err'" : " class='ok'") . ">{$meta_len}</td>";
    echo "<td>" . esc_html($lyricist ?: '---') . "</td>";
    echo "<td>" . ($has_elem ? "<span class='warn'>YES</span>" : "<span class='ok'>---</span>") . "</td>";
    echo "<td>" . implode(' ', $flags) . " => {$action_str}</td>";
    echo "</tr>";

    if ($mode === 'live') {
        if ($fk_to_set) {
            update_post_meta($pid, 'rank_math_focus_keyword', $fk_to_set);
            $counters['fk_set']++;
        }
        if ($meta_len <= 160) {
            update_post_meta($pid, 'rank_math_description', $new_meta);
            $counters['meta_set']++;
        }
        delete_post_meta($pid, '_elementor_edit_mode');
        delete_post_meta($pid, '_elementor_template_type');
        delete_post_meta($pid, '_elementor_data');
        delete_post_meta($pid, '_elementor_page_settings');
        if ($has_elem) $counters['elem_cleared']++;
    }
}
echo "</table>";

// ==================== TASK 3: ALT TEXT ON MEDIA ====================

echo "<h2>Task 3: Alt Text on 14 Media Items</h2>";
echo "<table><tr><th>#</th><th>Media ID</th><th>Post ID</th><th>FK = Alt Text</th><th>Current Alt</th><th>Action</th></tr>";

$alt_count = 0;
$alt_set   = 0;
foreach ($media_map as $mid => $pid) {
    $alt_count++;
    $fk          = isset($post_fk_map[$pid]) ? $post_fk_map[$pid] : '';
    $current_alt = get_post_meta($mid, '_wp_attachment_image_alt', true);

    if (empty($fk)) {
        $action = "<span class='warn'>SKIP - no FK</span>";
    } elseif ($current_alt === $fk) {
        $action = "<span class='skip'>Already correct</span>";
    } else {
        $action = "<span class='ok'>SET</span>";
        if ($mode === 'live') {
            update_post_meta($mid, '_wp_attachment_image_alt', $fk);
            $alt_set++;
        }
    }

    echo "<tr>";
    echo "<td>{$alt_count}</td><td>{$mid}</td><td>{$pid}</td>";
    echo "<td>" . esc_html($fk ?: '---') . "</td>";
    echo "<td>" . esc_html($current_alt ?: '---') . "</td>";
    echo "<td>{$action}</td>";
    echo "</tr>";
}
echo "</table>";

// ==================== SUMMARY ====================

echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li>Posts processed: {$row} / " . count($all_ids) . "</li>";
if ($mode === 'live') {
    echo "<li>FK set/updated: {$counters['fk_set']}</li>";
    echo "<li>Meta descriptions set: {$counters['meta_set']}</li>";
    echo "<li>Elementor fields cleared: {$counters['elem_cleared']} posts</li>";
    echo "<li>Alt texts set: {$alt_set} / " . count($media_map) . "</li>";
}
echo "<li class='warn'>Posts with NO Manglish section: {$counters['no_manglish']} (FK falls back to existing value)</li>";
if ($counters['errors'] > 0) echo "<li class='err'>Errors: {$counters['errors']}</li>";
echo "</ul>";

if ($mode === 'dry') {
    echo "<div class='dry-banner' style='margin-top:30px'>";
    echo "<p><strong>DRY RUN COMPLETE - no changes made.</strong></p>";
    echo "<p>Review the table above. Check:<br>";
    echo "- Every FK looks correct (derived from Manglish first line)<br>";
    echo "- Every meta is 160 chars or less<br>";
    echo "- Posts flagged NO MANGLISH will keep their existing FK</p>";
    echo "<p style='margin-top:15px'><a href='?mode=live' style='color:red;font-size:20px;font-weight:bold;text-decoration:underline;'>WARNING: EXECUTE ALL CHANGES</a></p>";
    echo "</div>";
} else {
    echo "<div style='background:#e0ffe0;border:3px solid green;padding:15px;margin:20px 0;text-align:center'>";
    echo "<p style='font-size:20px;color:green;font-weight:bold'>ALL CHANGES APPLIED</p>";
    echo "<p style='color:red;font-weight:bold;font-size:16px'>DELETE THIS FILE NOW:<br><code>rm mkk-seo-batch-s11.php</code></p>";
    echo "</div>";
}

echo "</body></html>";
