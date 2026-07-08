<?php
/**
 * mkk.php - MKK audit + structural fixes in one file (category 28).
 *
 * USAGE - place in WordPress root, open in browser:
 *   /mkk.php                    -> AUDIT (default, read-only): issues + summary
 *   /mkk.php?fk=1               -> AUDIT full FK / meta / title dump
 *   /mkk.php?mode=fix           -> FIX dry run (shows changes, writes nothing)
 *   /mkk.php?mode=fix&live=1     -> FIX live (applies changes)
 *
 * Default is always read-only. Writes happen ONLY with mode=fix&live=1.
 * Server-side: fixes touch ASCII tokens only (no Malayalam handling).
 * Delete from root when done (harmless in audit mode, but tidy up).
 */

require_once __DIR__ . '/wp-load.php';
header('Content-Type: text/plain; charset=utf-8');

$CAT  = isset($_GET['cat']) ? (int) $_GET['cat'] : 28;
$MODE = (isset($_GET['mode']) && $_GET['mode'] === 'fix') ? 'fix' : 'audit';
$LIVE = isset($_GET['live']) && $_GET['live'] === '1';
$FK   = isset($_GET['fk']) && $_GET['fk'] === '1';

/* ---------- FIX MODE ---------- */
if ($MODE === 'fix') {
    // post_id => [ label, [ from => to ] ]
    $FIXES = array(
        3166  => array('Song 83 - Papathin van vishathe (TOC ids)',
            array('malayalam-lyrics' => 'm', 'manglish-lyrics' => 'm-1')),
        10068 => array('Song 160 - Vaanalokathezhunnellinaal (video swap)',
            array('ZrUVd40FQq0' => 'bsd90DN7k6E')),
    );

    echo 'MODE: FIX - ' . ($LIVE ? 'LIVE (applying)' : 'DRY RUN (add &live=1 to apply)') . "\n";
    echo str_repeat('=', 66) . "\n";

    foreach ($FIXES as $pid => $fix) {
        list($label, $map) = $fix;
        $post = get_post($pid);
        echo "Post {$pid}: {$label}\n";
        if (!$post) { echo "  ERROR: not found\n" . str_repeat('-', 66) . "\n"; continue; }

        $content = $post->post_content;
        $new = $content;
        foreach ($map as $from => $to) {
            $n = substr_count($new, $from);
            printf("  '%s' -> '%s' : %d occurrence(s)\n", $from, $to, $n);
            if ($n > 0) $new = str_replace($from, $to, $new);
        }
        if ($new === $content) {
            echo "  No changes needed - already fixed. (idempotent)\n";
        } elseif ($LIVE) {
            $r = wp_update_post(array('ID' => $pid, 'post_content' => $new), true);
            echo is_wp_error($r) ? '  ERROR: ' . $r->get_error_message() . "\n" : "  APPLIED.\n";
        } else {
            echo "  (dry run - not written)\n";
        }
        echo str_repeat('-', 66) . "\n";
    }

    echo "\nAfter LIVE run, in Gutenberg:\n";
    echo " - Post 3166: 'Attempt Recovery' on TOC block, then Save.\n";
    echo " - Post 10068: open, confirm new video, Save (refreshes oEmbed).\n";
    exit;
}

/* ---------- AUDIT MODE (read-only) ---------- */
$q = new WP_Query(array(
    'cat' => $CAT, 'post_status' => 'publish', 'posts_per_page' => -1,
    'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC',
));
$ids = $q->posts;

echo "MKK AUDIT (category {$CAT}) - " . count($ids) . " published posts\n";
echo 'Mode: ' . ($FK ? 'FULL FK/META DUMP' : 'ISSUES ONLY') . " (read-only)\n";
echo str_repeat('=', 66) . "\n";

$tally = array('fk'=>0,'meta'=>0,'meta_long'=>0,'title'=>0,'img'=>0,'alt'=>0,'toc'=>0,'manglish'=>0,'junk'=>0);
$with_issue = 0;

foreach ($ids as $pid) {
    $title = get_the_title($pid);
    $c     = get_post_field('post_content', $pid);
    $fk    = trim((string) get_post_meta($pid, 'rank_math_focus_keyword', true));
    $desc  = trim((string) get_post_meta($pid, 'rank_math_description', true));
    $rtit  = trim((string) get_post_meta($pid, 'rank_math_title', true));
    $tid   = get_post_thumbnail_id($pid);
    $alt   = $tid ? trim((string) get_post_meta($tid, '_wp_attachment_image_alt', true)) : '';
    $dlen  = $desc !== '' ? mb_strlen($desc) : 0;

    if ($FK) {
        echo "[{$pid}] {$title}\n";
        echo '   FK   : ' . ($fk !== '' ? $fk : '(none)') . "\n";
        echo '   META : ' . ($desc !== '' ? $desc . "  [{$dlen}]" : '(none)') . "\n";
        echo '   TITLE: ' . ($rtit !== '' ? $rtit : '(none / template default)') . "\n";
        echo str_repeat('-', 66) . "\n";
        continue;
    }

    $issues = array();
    if ($fk === '')                 { $issues[]='FK missing';              $tally['fk']++; }
    if ($desc === '')               { $issues[]='META missing';            $tally['meta']++; }
    elseif ($dlen > 160)            { $issues[]="META {$dlen} chars >160"; $tally['meta_long']++; }
    if ($rtit === '')               { $issues[]='TITLE not set (manual)';  $tally['title']++; }
    if (!$tid)                      { $issues[]='no featured image';       $tally['img']++; }
    elseif ($alt === '')            { $issues[]='featured img ALT empty';  $tally['alt']++; }
    if (strpos($c,'malayalam-lyrics')!==false || strpos($c,'manglish-lyrics')!==false)
                                    { $issues[]='non-standard TOC ids';    $tally['toc']++; }
    if (stripos($c,'Manglish')===false) { $issues[]='NO Manglish section'; $tally['manglish']++; }
    if (stripos($c,'swcfpc')!==false || stripos($c,'<meta http-equiv')!==false || strpos($c,'Maglish')!==false)
                                    { $issues[]='junk (swcfpc/meta/Maglish)'; $tally['junk']++; }

    if ($issues) {
        $with_issue++;
        echo "[{$pid}] {$title}\n";
        foreach ($issues as $i) echo "   - {$i}\n";
    }
}

if (!$FK) {
    echo str_repeat('=', 66) . "\n";
    echo "SUMMARY\n";
    echo '  posts scanned         : ' . count($ids) . "\n";
    echo "  posts with >=1 issue  : {$with_issue}\n";
    echo "  FK missing            : {$tally['fk']}\n";
    echo "  META missing          : {$tally['meta']}\n";
    echo "  META >160 chars       : {$tally['meta_long']}\n";
    echo "  rank_math_title unset : {$tally['title']}\n";
    echo "  no featured image     : {$tally['img']}\n";
    echo "  featured ALT empty    : {$tally['alt']}\n";
    echo "  non-standard TOC ids  : {$tally['toc']}\n";
    echo "  no Manglish section   : {$tally['manglish']}\n";
    echo "  junk markup           : {$tally['junk']}\n";
}
