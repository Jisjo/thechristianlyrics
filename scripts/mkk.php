<?php
/**
 * mkk.php - MKK audit + fixes + JSON feed (category 28).
 *
 * SECURITY: set $SECRET below. When set, every request needs ?key=THAT.
 *   (Empty string '' disables the check - not recommended.)
 *
 * USAGE - place in WordPress root, open in browser (append &key=YOURKEY):
 *   /mkk.php?key=K                  -> AUDIT text report (read-only)
 *   /mkk.php?key=K&fk=1             -> full FK / meta / title dump (read-only)
 *   /mkk.php?mode=json&key=K        -> machine-readable JSON (read-only; for Claude web_fetch)
 *   /mkk.php?mode=fix&key=K         -> FIX dry run (writes nothing)
 *   /mkk.php?mode=fix&live=1&key=K  -> FIX live (applies changes)
 *
 * Read-only by default. Writes only with mode=fix&live=1.
 * Fixes touch ASCII tokens only (no Malayalam handling). Delete after a live run.
 */

require_once __DIR__ . '/wp-load.php';

/* ---------- KEY GUARD ---------- */
$SECRET = 'CHANGE_ME_pick_a_random_string';   // <-- set your key here; '' = no check
$key = isset($_GET['key']) ? $_GET['key'] : '';
if ($SECRET !== '' && !hash_equals($SECRET, $key)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Forbidden - missing or invalid key.\n";
    exit;
}

$CAT  = isset($_GET['cat']) ? (int) $_GET['cat'] : 28;
$LIVE = isset($_GET['live']) && $_GET['live'] === '1';
$FK   = isset($_GET['fk']) && $_GET['fk'] === '1';
$MODE = 'audit';
if (isset($_GET['mode']) && $_GET['mode'] === 'fix')  $MODE = 'fix';
if (isset($_GET['mode']) && $_GET['mode'] === 'json') $MODE = 'json';

/* ---------- FIX MODE ---------- */
if ($MODE === 'fix') {
    header('Content-Type: text/plain; charset=utf-8');
    $FIXES = array(
        3166  => array('Song 83 - Papathin van vishathe (TOC ids)',
            array('malayalam-lyrics' => 'm', 'manglish-lyrics' => 'm-1')),
        10068 => array('Song 160 - Vaanalokathezhunnellinaal (video swap)',
            array('ZrUVd40FQq0' => 'bsd90DN7k6E')),
    );
    echo 'MODE: FIX - ' . ($LIVE ? 'LIVE (applying)' : 'DRY RUN (add &live=1 to apply)') . "\n";
    echo str_repeat('=', 60) . "\n";
    foreach ($FIXES as $pid => $fix) {
        list($label, $map) = $fix;
        $post = get_post($pid);
        echo "Post {$pid}: {$label}\n";
        if (!$post) { echo "  ERROR: not found\n" . str_repeat('-', 60) . "\n"; continue; }
        $content = $post->post_content; $new = $content;
        foreach ($map as $from => $to) {
            $n = substr_count($new, $from);
            printf("  '%s' -> '%s' : %d occurrence(s)\n", $from, $to, $n);
            if ($n > 0) $new = str_replace($from, $to, $new);
        }
        if ($new === $content) { echo "  No changes needed.\n"; }
        elseif ($LIVE) { $r = wp_update_post(array('ID'=>$pid,'post_content'=>$new), true);
            echo is_wp_error($r) ? '  ERROR: '.$r->get_error_message()."\n" : "  APPLIED.\n"; }
        else { echo "  (dry run - not written)\n"; }
        echo str_repeat('-', 60) . "\n";
    }
    exit;
}

/* ---------- BUILD AUDIT DATA (shared by audit / fk / json) ---------- */
$q = new WP_Query(array(
    'cat' => $CAT, 'post_status' => 'publish', 'posts_per_page' => -1,
    'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC',
));
$ids = $q->posts;
$rows = array();
$tally = array('fk'=>0,'meta'=>0,'meta_long'=>0,'title'=>0,'img'=>0,'alt'=>0,'toc'=>0,'manglish'=>0,'junk'=>0,'clean'=>0);

foreach ($ids as $pid) {
    $title = get_the_title($pid);
    $num   = preg_match('/^\s*(\d+)\./', $title, $mm) ? (int) $mm[1] : 0;
    $c     = get_post_field('post_content', $pid);
    $fk    = trim((string) get_post_meta($pid, 'rank_math_focus_keyword', true));
    $desc  = trim((string) get_post_meta($pid, 'rank_math_description', true));
    $rtit  = trim((string) get_post_meta($pid, 'rank_math_title', true));
    $tid   = get_post_thumbnail_id($pid);
    $alt   = $tid ? trim((string) get_post_meta($tid, '_wp_attachment_image_alt', true)) : '';
    $dlen  = $desc !== '' ? mb_strlen($desc) : 0;

    $meta_state = $desc === '' ? 'missing' : ($dlen > 160 ? 'long' : 'ok');
    $r = array(
        'num' => $num, 'id' => $pid, 'title' => $title,
        'fk' => ($fk !== ''), 'meta' => $meta_state, 'meta_len' => $dlen,
        'title_set' => ($rtit !== ''),
        'img' => ($tid ? true : false), 'alt' => ($tid ? ($alt !== '') : false),
        'toc_ok' => !(strpos($c,'malayalam-lyrics')!==false || strpos($c,'manglish-lyrics')!==false),
        'manglish' => (stripos($c,'Manglish')!==false),
        'junk' => (stripos($c,'swcfpc')!==false || stripos($c,'<meta http-equiv')!==false || strpos($c,'Maglish')!==false),
    );
    $issues = array();
    if (!$r['fk'])            { $issues[]='FK missing';            $tally['fk']++; }
    if ($meta_state==='missing'){ $issues[]='META missing';        $tally['meta']++; }
    elseif ($meta_state==='long'){ $issues[]="META {$dlen}>160";    $tally['meta_long']++; }
    if (!$r['title_set'])     { $tally['title']++; }                 // low priority; not counted as an "issue"
    if (!$r['img'])           { $issues[]='no featured image';     $tally['img']++; }
    elseif (!$r['alt'])       { $issues[]='ALT empty';             $tally['alt']++; }
    if (!$r['toc_ok'])        { $issues[]='non-standard TOC ids';  $tally['toc']++; }
    if (!$r['manglish'])      { $issues[]='no Manglish section';   $tally['manglish']++; }
    if ($r['junk'])           { $issues[]='junk markup';           $tally['junk']++; }
    $r['issues'] = $issues;
    $r['status'] = empty($issues) ? 'done' : 'pending';
    if (empty($issues)) $tally['clean']++;
    $rows[] = $r;
}

/* ---------- JSON MODE (for Claude web_fetch) ---------- */
if ($MODE === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'generated' => date('c'),
        'category'  => $CAT,
        'total'     => count($rows),
        'summary'   => $tally,
        'posts'     => $rows,
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/* ---------- FK DUMP / AUDIT TEXT ---------- */
header('Content-Type: text/plain; charset=utf-8');
echo "MKK AUDIT (category {$CAT}) - " . count($rows) . " published posts\n";
echo 'Mode: ' . ($FK ? 'FULL FK/META DUMP' : 'ISSUES ONLY') . " (read-only)\n";
echo str_repeat('=', 66) . "\n";

if ($FK) {
    foreach ($rows as $r) {
        $fkv = get_post_meta($r['id'], 'rank_math_focus_keyword', true);
        $dsc = get_post_meta($r['id'], 'rank_math_description', true);
        $ttl = get_post_meta($r['id'], 'rank_math_title', true);
        echo "[{$r['id']}] {$r['title']}\n";
        echo '   FK   : ' . ($fkv !== '' ? $fkv : '(none)') . "\n";
        echo '   META : ' . ($dsc !== '' ? $dsc . "  [{$r['meta_len']}]" : '(none)') . "\n";
        echo '   TITLE: ' . ($ttl !== '' ? $ttl : '(none / template default)') . "\n";
        echo str_repeat('-', 66) . "\n";
    }
    exit;
}

foreach ($rows as $r) {
    if ($r['status'] === 'pending') {
        echo "[{$r['id']}] {$r['title']}\n";
        foreach ($r['issues'] as $i) echo "   - {$i}\n";
    }
}
echo str_repeat('=', 66) . "\n";
echo "SUMMARY\n";
echo '  posts scanned         : ' . count($rows) . "\n";
echo '  FK missing            : ' . $tally['fk'] . "\n";
echo '  META missing          : ' . $tally['meta'] . "\n";
echo '  META >160 chars       : ' . $tally['meta_long'] . "\n";
echo '  rank_math_title unset : ' . $tally['title'] . " (template default - low priority)\n";
echo '  no featured image     : ' . $tally['img'] . "\n";
echo '  featured ALT empty    : ' . $tally['alt'] . "\n";
echo '  non-standard TOC ids  : ' . $tally['toc'] . "\n";
echo '  no Manglish section   : ' . $tally['manglish'] . "\n";
echo '  junk markup           : ' . $tally['junk'] . "\n";
echo '  fully clean           : ' . $tally['clean'] . "\n";
