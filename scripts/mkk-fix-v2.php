<?php
/**
 * MKK Fix Script — Session 11 (v2)
 * Fixes bad FK, meta, and alt text from the first script run.
 *
 * Root cause: First script matched "Manglish Lyrics" in TOC link, not H2 heading.
 * This version: hardcodes 11 known FKs + uses fixed H2 parser for the rest.
 *
 * Usage: Upload to WP root -> ?mode=dry (review) -> ?mode=live (execute) -> DELETE
 */
require_once(__DIR__ . '/wp-load.php');
if (!current_user_can('manage_options')) die('Admin required.');

$mode = isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : 'dry';
?><!DOCTYPE html>
<html><head><meta charset="utf-8"><title>MKK Fix v2</title>
<style>
body{font-family:monospace;padding:20px;max-width:1600px;margin:0 auto}
table{border-collapse:collapse;width:100%;font-size:12px;margin:15px 0}
th,td{border:1px solid #ccc;padding:5px 8px;text-align:left}
th{background:#e8e8e8}
.ok{color:green;font-weight:bold}.err{color:red;font-weight:bold}.warn{color:#cc7700;font-weight:bold}
.changed{background:#fff3cd}.skip{color:#999}
</style></head><body>
<?php
echo $mode === 'live'
    ? '<div style="background:#ffe0e0;border:3px solid red;padding:15px;margin:20px 0;text-align:center;font-size:18px">LIVE MODE</div>'
    : '<div style="background:#e0f0ff;border:3px solid #0066cc;padding:15px;margin:20px 0;text-align:center;font-size:18px">DRY RUN</div>';

echo "<h1>MKK Fix Script v2</h1>";

// ==================== HARDCODED CORRECT FKs ====================
// These 11 are verified from actual post content
$hardcoded_fk = [
    1642  => 'Yeshuve dhyanikkumbol njan',          // Song 36 (3 words, "njan" ≤4 but it's the title ending)
    1785  => 'Yeshu mahonnathane',                   // Song 55
    1851  => 'Aadukalkuvendi jeevane vedinjathaam',  // Song 64
    3370  => 'Paadin inpageetham inneshu',           // Song 113 (3 words, "inneshu" is next word since script had it wrong)
    3374  => 'Athyathbhuthame athyathbhuthame paavana', // Song 117
    3375  => 'Jaathanayi jaathanayinnu loka',        // Song 118
    3377  => 'Vaagdatha sampoorthiyaayi thiruvavathaaram', // Song 120
    943   => 'Mahathwa prabhu maricha',              // Song 150
    1207  => 'Varika paraaparane ee yogathil',       // Song 429 ("ee" ≤4 -> 4 words)
    12091 => 'Ninsannidhiyil daivame poornasanthosham', // Song 401
    // Song 26 (1581) has NO Manglish section - keep whatever exists or leave empty
];

// ==================== HARDCODED SONG NAMES for meta ====================
// Clean Manglish names extracted from titles (no Malayalam, no garbled text)
$song_names = [
    1642  => 'Yeshuve Dhyanikkumbol Njan',
    1581  => 'Aashisham Nalkename',
    1696  => 'Yeshuvin Naamam',
    1382  => 'Koode Paarkka Neram',
    1562  => 'Varuvin Naam Yahovakku',
    1588  => 'Anugrahakkadale',
    1654  => 'Shree Yeshu Naamame Thirunamam',
    164   => 'Yeshu Ennulla Naamame',
    1682  => 'Naadha Choriyaname',
    1690  => 'Shreeyeshu Naamam',
    1702  => 'Naathane En Yeshuve',
    1708  => 'Hallelujah Hallelujah Hallelujah Amen',
    1710  => 'Senakalil Paran',
    1719  => 'Yeshu Deva Yeshu Naayaka',
    1742  => 'Deva Nandana Vandanam',
    3165  => 'Paadam Vandikkunnen Thirukrupa',
    3372  => 'Krysthavare Vandanekkunarin',
    9502  => 'Athbudhane Yeshu Nadha',
    10068 => 'Vaanalokathezhunnellinaal Sreeyeshu',
    10075 => 'Paapikalin Rakshakan',
    3166  => 'Papathin Van Vishathe Ozhippan',
    1951  => 'Njan Varunnu Krushinkal',
    3374  => 'Athyathbhuthame Athyathbhuthame',
    1851  => 'Aadukalkuvendi Jeevane',
    3375  => 'Jaathanayi Jaathanayinnu',
    943   => 'Mahathwa Prabhu Maricha',
    1207  => 'Varika Paraa Parane',
    3377  => 'Vaagdatha Sampoorthiyaayi',
    1785  => 'Yeshu Mahonnathane',
    3370  => 'Paadin Inpageetham',
    12091 => 'Kristhuvin Sannidhi',
];

// ==================== HARDCODED LYRICISTS (post_id => English name) ====================
// Only include posts where lyricist is confirmed and tag link exists
$post_lyricists = [
    1581  => 'T.J. Varkey',       // Song 26
    1654  => 'T.J. Varkey',       // Song 38
    1702  => 'Moshavatsalam',     // Song 45
    1851  => 'T.J. Varkey',       // Song 64
    1785  => 'T.J. Varkey',       // Song 55
    3374  => 'Rev. K.P. Philip',  // Song 117
    3375  => 'Rev. K.P. Philip',  // Song 118
    1207  => 'P.D. John',         // Song 429
    12091 => 'Moshavatsalam',     // Song 401
];

// ==================== ALL POST IDs ====================
$all_ids = [1642,1581,1696,1382,1562,1588,1654,164,1682,1690,1702,1708,1710,1719,1742,3165,3372,9502,10068,10075,3166,1951,3374,1851,3375,943,1207,3377,1785,3370,12091];
$list_a = [1642,1581,1696,1382,1562,1588,1654,164,1682,1690,1702,1708,1710,1719,1742,3165,3372,9502,10068,10075,3166,1951];

// Media: media_id => post_id
$media_map = [12107=>1642,12106=>1562,12105=>1588,12104=>1654,12103=>164,12102=>1682,12101=>1719,12100=>3374,12099=>1851,12098=>3375,12097=>1785,12096=>1207,12095=>3370,12094=>12091];

// ==================== FIXED PARSER ====================

/**
 * FIXED: Extract Manglish first line by matching the actual H2 heading tag,
 * not just the text "Manglish Lyrics" (which appears in TOC first).
 */
function extract_manglish_first_line_v2($content) {
    // Match the actual H2 heading containing "Manglish Lyrics"
    if (!preg_match('/<h2[^>]*>[^<]*Manglish Lyrics[^<]*<\/h2>/i', $content, $match, PREG_OFFSET_CAPTURE)) {
        return false;
    }

    $after = substr($content, $match[0][1] + strlen($match[0][0]));

    // Find first <p> after this H2
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
    return mb_substr("{$song_name} lyrics from Marthoma Kristheeya Keerthanangal.", 0, 160);
}

// ==================== PROCESS ====================

echo "<h2>Posts (31) — Old vs New</h2>";
echo "<table><tr><th>#</th><th>List</th><th>ID</th><th>Song Name</th><th>Old FK</th><th>New FK</th><th>FK Source</th><th>New Meta</th><th>Len</th><th>Lyricist</th><th>Changed?</th></tr>";

$post_fk_final = [];
$counters = ['fk_fixed' => 0, 'meta_fixed' => 0, 'unchanged' => 0, 'errors' => 0];

$row = 0;
foreach ($all_ids as $pid) {
    $row++;
    $list = in_array($pid, $list_a) ? 'A' : 'B';
    $post = get_post($pid);
    if (!$post) {
        echo "<tr><td>$row</td><td>$list</td><td>$pid</td><td colspan='8' class='err'>NOT FOUND</td></tr>";
        $counters['errors']++;
        continue;
    }

    $old_fk = get_post_meta($pid, 'rank_math_focus_keyword', true);
    $old_meta = get_post_meta($pid, 'rank_math_description', true);

    // --- Determine correct FK ---
    if (isset($hardcoded_fk[$pid])) {
        $new_fk = $hardcoded_fk[$pid];
        $fk_source = 'HARDCODED';
    } else {
        // Use fixed parser
        $first_line = extract_manglish_first_line_v2($post->post_content);
        $new_fk = derive_fk($first_line);
        $fk_source = $new_fk ? 'PARSED (v2)' : 'KEEP OLD';
        if (!$new_fk) $new_fk = $old_fk; // keep existing if parser fails
    }
    $post_fk_final[$pid] = $new_fk;

    // --- Build correct meta ---
    $song_name = isset($song_names[$pid]) ? $song_names[$pid] : '';
    $lyricist = isset($post_lyricists[$pid]) ? $post_lyricists[$pid] : false;
    $new_meta = build_meta($song_name, $lyricist);
    $meta_len = mb_strlen($new_meta);

    // --- Check changes ---
    $fk_changed = ($new_fk !== $old_fk);
    $meta_changed = ($new_meta !== $old_meta);
    $any_change = $fk_changed || $meta_changed;

    $change_label = [];
    if ($fk_changed) $change_label[] = 'FK';
    if ($meta_changed) $change_label[] = 'META';
    $change_str = $any_change ? implode('+', $change_label) : '<span class="skip">no change</span>';

    $row_class = $any_change ? ' class="changed"' : '';
    echo "<tr$row_class>";
    echo "<td>$row</td><td>$list</td><td>$pid</td>";
    echo "<td>" . esc_html($song_name) . "</td>";
    echo "<td style='font-size:11px'>" . esc_html(mb_substr($old_fk, 0, 40)) . "</td>";
    echo "<td style='font-size:11px'><strong>" . esc_html(mb_substr($new_fk, 0, 40)) . "</strong></td>";
    echo "<td>$fk_source</td>";
    echo "<td style='font-size:10px'>" . esc_html(mb_substr($new_meta, 0, 80)) . "...</td>";
    echo "<td" . ($meta_len > 160 ? " class='err'" : " class='ok'") . ">$meta_len</td>";
    echo "<td>" . esc_html($lyricist ?: '---') . "</td>";
    echo "<td>$change_str</td>";
    echo "</tr>";

    if ($mode === 'live' && $any_change) {
        if ($fk_changed && $new_fk) {
            update_post_meta($pid, 'rank_math_focus_keyword', $new_fk);
            $counters['fk_fixed']++;
        }
        if ($meta_changed && $meta_len <= 160) {
            update_post_meta($pid, 'rank_math_description', $new_meta);
            $counters['meta_fixed']++;
        }
    } else {
        $counters['unchanged']++;
    }
}
echo "</table>";

// ==================== ALT TEXT ====================

echo "<h2>Media Alt Text (14)</h2>";
echo "<table><tr><th>#</th><th>Media</th><th>Post</th><th>Old Alt</th><th>New Alt (=FK)</th><th>Changed?</th></tr>";

$alt_fixed = 0;
$mc = 0;
foreach ($media_map as $mid => $pid) {
    $mc++;
    $old_alt = get_post_meta($mid, '_wp_attachment_image_alt', true);
    $new_alt = isset($post_fk_final[$pid]) ? $post_fk_final[$pid] : '';
    $changed = ($new_alt && $old_alt !== $new_alt);

    $row_class = $changed ? ' class="changed"' : '';
    echo "<tr$row_class>";
    echo "<td>$mc</td><td>$mid</td><td>$pid</td>";
    echo "<td style='font-size:11px'>" . esc_html(mb_substr($old_alt, 0, 40)) . "</td>";
    echo "<td style='font-size:11px'><strong>" . esc_html(mb_substr($new_alt, 0, 40)) . "</strong></td>";
    echo "<td>" . ($changed ? '<span class="warn">FIX</span>' : '<span class="skip">same</span>') . "</td>";
    echo "</tr>";

    if ($mode === 'live' && $changed) {
        update_post_meta($mid, '_wp_attachment_image_alt', $new_alt);
        $alt_fixed++;
    }
}
echo "</table>";

// ==================== SUMMARY ====================
echo "<h2>Summary</h2><ul>";
if ($mode === 'live') {
    echo "<li>FK fixed: {$counters['fk_fixed']}</li>";
    echo "<li>Meta fixed: {$counters['meta_fixed']}</li>";
    echo "<li>Alt texts fixed: $alt_fixed</li>";
}
echo "<li>Unchanged: {$counters['unchanged']}</li>";
echo "<li>Errors: {$counters['errors']}</li></ul>";

if ($mode === 'dry') {
    echo "<p style='margin-top:20px'><strong>DRY RUN — no changes made.</strong></p>";
    echo "<p>Yellow rows = will change. Review Old vs New columns.</p>";
    echo "<p><a href='?mode=live' style='color:red;font-size:18px;font-weight:bold'>EXECUTE FIXES</a></p>";
} else {
    echo "<p style='color:green;font-size:18px;font-weight:bold'>FIXES APPLIED</p>";
    echo "<p style='color:red;font-weight:bold'>DELETE: rm mkk-fix-v2.php</p>";
}
echo "</body></html>";
