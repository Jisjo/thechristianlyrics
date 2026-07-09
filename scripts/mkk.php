<?php
/**
 * mkk.php - MKK audit + fixes + JSON feed + live HTML dashboard (category 28).
 *
 * SECURITY: set $SECRET below. When set, every request needs ?key=THAT.
 *
 * USAGE (append &key=YOURKEY):
 *   /mkk.php?key=K                  -> AUDIT text report (read-only)
 *   /mkk.php?mode=html&key=K        -> LIVE dashboard, per-post done/pending (read-only)
 *   /mkk.php?mode=json&key=K        -> machine-readable JSON (read-only; for Claude web_fetch)
 *   /mkk.php?key=K&fk=1             -> full FK / meta / title dump (read-only)
 *   /mkk.php?mode=fix&key=K         -> FIX dry run
 *   /mkk.php?mode=fix&live=1&key=K  -> FIX live (applies changes)
 *
 * Read-only by default. Writes only with mode=fix&live=1.
 */

require_once __DIR__ . '/wp-load.php';

$SECRET = 'CHANGE_ME_pick_a_random_string';   // <-- set your key; '' = no check
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
if (isset($_GET['mode']) && $_GET['mode'] === 'html') $MODE = 'html';

/* ---------- FIX MODE ---------- */
if ($MODE === 'fix') {
    header('Content-Type: text/plain; charset=utf-8');
    $FIXES = array(
        3166  => array('Song 83 - Papathin van vishathe (TOC ids)',
            array('malayalam-lyrics' => 'm', 'manglish-lyrics' => 'm-1')),
        10068 => array('Song 160 - Vaanalokathezhunnellinaal (video swap)',
            array('ZrUVd40FQq0' => 'bsd90DN7k6E')),
    );
    echo 'MODE: FIX - ' . ($LIVE ? 'LIVE (applying)' : 'DRY RUN (add &live=1 to apply)') . "\n" . str_repeat('=', 60) . "\n";
    foreach ($FIXES as $pid => $fix) {
        list($label, $map) = $fix; $post = get_post($pid);
        echo "Post {$pid}: {$label}\n";
        if (!$post) { echo "  ERROR: not found\n"; continue; }
        $content = $post->post_content; $new = $content;
        foreach ($map as $from => $to) {
            $n = substr_count($new, $from);
            printf("  '%s' -> '%s' : %d\n", $from, $to, $n);
            if ($n > 0) $new = str_replace($from, $to, $new);
        }
        if ($new === $content) echo "  No changes needed.\n";
        elseif ($LIVE) { $r = wp_update_post(array('ID'=>$pid,'post_content'=>$new), true);
            echo is_wp_error($r) ? '  ERROR: '.$r->get_error_message()."\n" : "  APPLIED.\n"; }
        else echo "  (dry run)\n";
    }
    exit;
}

/* ---------- BUILD AUDIT DATA (shared) ---------- */
$q = new WP_Query(array('cat'=>$CAT,'post_status'=>'publish','posts_per_page'=>-1,'fields'=>'ids','orderby'=>'title','order'=>'ASC'));
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
        'num'=>$num,'id'=>$pid,'title'=>$title,
        'fk'=>($fk!==''),'meta'=>$meta_state,'meta_len'=>$dlen,'title_set'=>($rtit!==''),
        'img'=>($tid?true:false),'alt'=>($tid?($alt!==''):false),
        'toc_ok'=>!(strpos($c,'malayalam-lyrics')!==false||strpos($c,'manglish-lyrics')!==false),
        'manglish'=>(stripos($c,'Manglish')!==false),
        'junk'=>(stripos($c,'swcfpc')!==false||stripos($c,'<meta http-equiv')!==false||strpos($c,'Maglish')!==false),
    );
    $issues = array(); $keys = array();
    if (!$r['fk'])              { $issues[]='FK missing';           $keys[]='fk';       $tally['fk']++; }
    if ($meta_state==='missing'){ $issues[]='META missing';         $keys[]='meta';     $tally['meta']++; }
    elseif ($meta_state==='long'){ $issues[]="META {$dlen}>160";     $keys[]='meta';     $tally['meta_long']++; }
    if (!$r['title_set'])       { $tally['title']++; }
    if (!$r['img'])             { $issues[]='no featured image';    $keys[]='img';      $tally['img']++; }
    elseif (!$r['alt'])         { $issues[]='ALT empty';            $keys[]='alt';      $tally['alt']++; }
    if (!$r['toc_ok'])          { $issues[]='non-standard TOC ids'; $keys[]='toc';      $tally['toc']++; }
    if (!$r['manglish'])        { $issues[]='no Manglish section';  $keys[]='manglish'; $tally['manglish']++; }
    if ($r['junk'])             { $issues[]='junk markup';          $keys[]='junk';     $tally['junk']++; }
    $r['issues']=$issues; $r['keys']=$keys;
    $r['status']= empty($issues) ? 'done' : 'pending';
    if (empty($issues)) $tally['clean']++;
    $rows[] = $r;
}

/* ---------- JSON MODE ---------- */
if ($MODE === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('generated'=>date('c'),'category'=>$CAT,'total'=>count($rows),'summary'=>$tally,'posts'=>$rows), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}

/* ---------- HTML DASHBOARD (read-only, live) ---------- */
if ($MODE === 'html') {
    header('Content-Type: text/html; charset=utf-8');
    $e = function($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); };
    $yes = '<span class="c ok">&#10003;</span>';
    $no  = '<span class="c no">&#10007;</span>';
    $warn= '<span class="c wn">!</span>';
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>MKK Live Tracker</title><style>';
    echo 'body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f5f3ee;color:#2e2a4f;font-size:14px}';
    echo '.wrap{max-width:1150px;margin:0 auto;padding:0 16px 60px}';
    echo 'header{background:#2e2a4f;color:#f3f1ea;padding:20px 0;border-bottom:3px solid #b0862f}header .wrap{padding-bottom:0}';
    echo 'h1{margin:0 0 3px;font-size:22px}.sub{color:#c9c4d9;font-size:13px}';
    echo '.stats{display:flex;flex-wrap:wrap;gap:8px;margin:16px 0 8px}';
    echo '.chip{background:#fff;border:1px solid #e6e1d6;border-radius:9px;padding:7px 11px;cursor:pointer;min-width:78px}';
    echo '.chip:hover{border-color:#b0862f}.chip.on{border-color:#2e2a4f;box-shadow:inset 0 0 0 1px #2e2a4f}';
    echo '.chip .n{font-size:18px;font-weight:600;display:block;line-height:1}.chip .l{font-size:11px;color:#8a8577;text-transform:uppercase}';
    echo '.chip.warn .n{color:#b34535}.chip.ok .n{color:#2f7d5b}';
    echo '.ctl{display:flex;gap:10px;align-items:center;margin:6px 0 12px}#q{flex:1;padding:9px 11px;border:1px solid #e6e1d6;border-radius:8px;font:inherit}';
    echo '.cnt{font-size:12px;color:#8a8577}';
    echo '.tw{overflow-x:auto;border:1px solid #e6e1d6;border-radius:12px;background:#fff}';
    echo 'table{border-collapse:collapse;width:100%;min-width:820px}th,td{padding:8px 9px;text-align:left;border-bottom:1px solid #eee;white-space:nowrap}';
    echo 'thead th{position:sticky;top:0;background:#efece4;font-size:11px;text-transform:uppercase;color:#4a4570}';
    echo 'td.nm{white-space:normal;min-width:230px}td.id,td.num{font-family:ui-monospace,monospace;font-size:12px;color:#4a4570}';
    echo '.c{display:inline-flex;align-items:center;justify-content:center;width:24px;height:20px;border-radius:5px;font-weight:600}';
    echo '.ok{color:#2f7d5b;background:#e7f2eb}.no{color:#b34535;background:#f7e6e2}.wn{color:#c6871b;background:#fbf1dc}';
    echo '.bd{border-radius:20px;padding:2px 9px;font-size:11px;font-weight:600}.bd.d{background:#e7f2eb;color:#2f7d5b}.bd.p{background:#fbf1dc;color:#a3671b}';
    echo '.pend{font-size:11px;color:#b34535}.na{color:#c9c4d9}';
    echo '</style></head><body>';
    echo '<header><div class="wrap"><h1>MKK Live Tracker</h1><div class="sub">Category '.$CAT.' &middot; '.count($rows).' published posts &middot; live as of '.$e(date('d M Y H:i')).' &middot; read-only</div></div></header>';
    echo '<div class="wrap">';
    $chip = function($k,$n,$l,$cls='') { return '<button class="chip '.$cls.'" data-f="'.$k.'"><span class="n">'.$n.'</span><span class="l">'.$l.'</span></button>'; };
    echo '<div class="stats">';
    echo $chip('all',count($rows),'All');
    echo $chip('pending',count($rows)-$tally['clean'],'Pending','warn');
    echo $chip('done',$tally['clean'],'Done','ok');
    echo $chip('meta',$tally['meta']+$tally['meta_long'],'Meta','warn');
    echo $chip('toc',$tally['toc'],'TOC ids','warn');
    echo $chip('junk',$tally['junk'],'Junk','warn');
    echo $chip('img',$tally['img'],'No image','warn');
    echo $chip('manglish',$tally['manglish'],'No Mangl.','warn');
    echo $chip('fk',$tally['fk'],'FK','warn');
    echo '</div>';
    echo '<div class="ctl"><input id="q" type="search" placeholder="Search song name, number, or post ID"><span class="cnt" id="cnt"></span></div>';
    echo '<div class="tw"><table><thead><tr><th>Song#</th><th>ID</th><th>Song</th><th>FK</th><th>Meta</th><th>Img</th><th>Alt</th><th>TOC</th><th>Mangl.</th><th>Junk</th><th>Status</th><th>Pending</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        $meta_cell = $r['meta']==='ok' ? $yes : ($r['meta']==='long' ? $warn : $no);
        $srch = strtolower($r['title'].' '.$r['num'].' '.$r['id']);
        echo '<tr data-status="'.$r['status'].'" data-keys="'.implode(' ',$r['keys']).'" data-s="'.$e($srch).'">';
        echo '<td class="num">'.($r['num']?:'-').'</td><td class="id">'.$r['id'].'</td><td class="nm">'.$e($r['title']).'</td>';
        echo '<td>'.($r['fk']?$yes:$no).'</td>';
        echo '<td>'.$meta_cell.'</td>';
        echo '<td>'.($r['img']?$yes:$no).'</td>';
        echo '<td>'.($r['img']?($r['alt']?$yes:$no):'<span class="c na">-</span>').'</td>';
        echo '<td>'.($r['toc_ok']?$yes:$no).'</td>';
        echo '<td>'.($r['manglish']?$yes:$no).'</td>';
        echo '<td>'.($r['junk']?$no:$yes).'</td>';
        echo '<td>'.($r['status']==='done'?'<span class="bd d">done</span>':'<span class="bd p">'.count($r['issues']).' left</span>').'</td>';
        echo '<td class="pend">'.$e(implode(', ',$r['issues'])).'</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
    echo '<p style="font-size:12px;color:#8a8577;margin-top:14px">rank_math_title is intentionally not tracked (template default). Green = done, red = pending, ! = meta over 160 chars. Read-only view; refresh for live state.</p>';
    echo '</div>';
    echo '<script>var f="all",q="";var rows=[].slice.call(document.querySelectorAll("tbody tr"));var chips=[].slice.call(document.querySelectorAll(".chip"));function draw(){chips.forEach(function(c){c.classList.toggle("on",c.dataset.f===f&&f!=="all")});var n=0;rows.forEach(function(r){var ok=(f==="all")||(f==="pending"&&r.dataset.status==="pending")||(f==="done"&&r.dataset.status==="done")||(r.dataset.keys.split(" ").indexOf(f)>-1);if(ok&&q){ok=r.dataset.s.indexOf(q)>-1}r.style.display=ok?"":"none";if(ok)n++});document.getElementById("cnt").textContent=n+" of "+rows.length+" songs"}chips.forEach(function(c){c.onclick=function(){f=(f===c.dataset.f&&c.dataset.f!=="all")?"all":c.dataset.f;draw()}});document.getElementById("q").addEventListener("input",function(e){q=e.target.value.toLowerCase();draw()});draw();</script>';
    echo '</body></html>';
    exit;
}

/* ---------- FK DUMP / AUDIT TEXT ---------- */
header('Content-Type: text/plain; charset=utf-8');
echo "MKK AUDIT (category {$CAT}) - " . count($rows) . " published posts\n";
echo 'Mode: ' . ($FK ? 'FULL FK/META DUMP' : 'ISSUES ONLY') . " (read-only)\n" . str_repeat('=', 66) . "\n";
if ($FK) {
    foreach ($rows as $r) {
        $fkv = get_post_meta($r['id'],'rank_math_focus_keyword',true);
        $dsc = get_post_meta($r['id'],'rank_math_description',true);
        $ttl = get_post_meta($r['id'],'rank_math_title',true);
        echo "[{$r['id']}] {$r['title']}\n   FK   : ".($fkv!==''?$fkv:'(none)')."\n   META : ".($dsc!==''?$dsc."  [{$r['meta_len']}]":'(none)')."\n   TITLE: ".($ttl!==''?$ttl:'(none / template default)')."\n".str_repeat('-',66)."\n";
    }
    exit;
}
foreach ($rows as $r) {
    if ($r['status']==='pending') { echo "[{$r['id']}] {$r['title']}\n"; foreach ($r['issues'] as $i) echo "   - {$i}\n"; }
}
echo str_repeat('=',66)."\nSUMMARY\n";
echo '  posts scanned         : '.count($rows)."\n";
echo '  FK missing            : '.$tally['fk']."\n";
echo '  META missing          : '.$tally['meta']."\n";
echo '  META >160 chars       : '.$tally['meta_long']."\n";
echo '  rank_math_title unset : '.$tally['title']." (template default - low priority)\n";
echo '  no featured image     : '.$tally['img']."\n";
echo '  featured ALT empty    : '.$tally['alt']."\n";
echo '  non-standard TOC ids  : '.$tally['toc']."\n";
echo '  no Manglish section   : '.$tally['manglish']."\n";
echo '  junk markup           : '.$tally['junk']."\n";
echo '  fully clean           : '.$tally['clean']."\n";
