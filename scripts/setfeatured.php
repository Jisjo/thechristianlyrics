<?php
/**
 * setfeatured.php  —  one-time featured-image + alt-text setter for the 47 MKK posts.
 * Upload to site root (same folder as mkk.php), then:
 *   DRY RUN : https://thechristianlyrics.com/setfeatured.php?key=mkk-7hq2p9x4
 *   APPLY   : https://thechristianlyrics.com/setfeatured.php?key=mkk-7hq2p9x4&live=1
 * Delete the file after the live run.
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

if (($_GET['key'] ?? '') !== 'mkk-7hq2p9x4') { http_response_code(403); die('forbidden'); }
header('Content-Type: text/plain; charset=utf-8');
$live = (($_GET['live'] ?? '') === '1');

// post_id => [media_id, alt_text]
$map = [
    3361  => [12172, 'Aadithya Chandraadikale'],
    1375  => [12171, 'Aathmaave Unarukaneram'],
    5034  => [12170, 'Aathmave Vanneduka Vishuddha'],
    2826  => [12169, 'Anugrahathode Ippol'],
    11762 => [12168, 'Bhoovasikal Sarvarume'],
    11770 => [12167, 'Daivahitham Anusarikkunnath'],
    3159  => [12166, 'Deva Devanu Mangalam'],
    6540  => [12165, 'En Jeevan Njaan'],
    1908  => [12164, 'Enikkai Chinthi Nin'],
    3157  => [12163, 'Ennaalum Sthuthikkanam'],
    6755  => [12162, 'Ente Daivam Svargga'],
    6515  => [12161, 'Ethra Ethra Sreshdam'],
    1455  => [12160, 'Karthaneyippakali Lenne'],
    3359  => [12159, 'Karunakaranam Parane Sharanam'],
    11773 => [12158, 'Kurishedutthen Yeshuvine'],
    1958  => [12157, 'Mahathwa Prabhu Maricha'],
    3168  => [12156, 'Maname Pukazhthidu Nee'],
    104   => [12155, 'Mannavane Mahonnathaa'],
    2301  => [12154, 'Manuvela Ninakku Vandhanam'],
    3169  => [12153, 'Manuvelin Sthuthiye Padiduvan'],
    1967  => [12152, 'Neeyozhike Neeyozhike'],
    11772 => [12151, 'Ninnishttam Deva Aayidatte'],
    3337  => [12150, 'Nithyavandanam Ninakku'],
    3367  => [12149, 'Onnumillaykayil Ninnenne Ninnude'],
    2210  => [12148, 'Paadi Thuthi Maname'],
    2311  => [12147, 'Paadum Parama Rakshakan'],
    3164  => [12146, 'Paduvin Sahajare Kuduvin'],
    2341  => [12145, 'Pahimam Jagadeeswara'],
    3360  => [12144, 'Para Devaa Svarggapura'],
    2188  => [12143, 'Parama Pithave Vandanam'],
    1845  => [12142, 'Parishudha Parane Sthuthi'],
    3170  => [12141, 'Rakshakane Ninakku Keerthanam'],
    11767 => [12140, 'Sarva Manushare Paranu'],
    1525  => [12139, 'Senayin Yehovaye Vaanasenayode'],
    1497  => [12138, 'Shudha Shudha Shudha'],
    1762  => [12137, 'Sree Yeshu Deva'],
    1817  => [12136, 'Sthothram Cheyyum Njaan'],
    1824  => [12135, 'Sthothram Enneshu Para'],
    1803  => [12134, 'Sthothram Shree Manuvelane'],
    3174  => [12133, 'Sthuthi Sthuthi Ninakke'],
    3363  => [12132, 'Sthuthichiduvin Kerthanangal'],
    3366  => [12131, 'Sthuthigeetham Paaduka Naam'],
    96    => [12130, 'Theernnu Pakal Kaalam'],
    3368  => [12129, 'Vaazthen Dehi Swar'],
    1836  => [12128, 'Yeshu Naathaa Nin'],
    1568  => [12127, 'Yeshu Sannidhi Mama'],
    1898  => [12126, 'Yesu Mathi Enikku'],
];

echo $live ? "=== LIVE RUN ===\n\n" : "=== DRY RUN (add &live=1 to apply) ===\n\n";
$ok = 0; $skip = 0; $err = 0;

foreach ($map as $pid => $info) {
    list($mid, $alt) = $info;
    $post = get_post($pid);
    if (!$post || $post->post_type !== 'post') { echo "ERR  post $pid not found\n"; $err++; continue; }
    if (!wp_get_attachment_url($mid))          { echo "ERR  media $mid not found (post $pid)\n"; $err++; continue; }

    $cur = (int) get_post_thumbnail_id($pid);
    if ($live) {
        set_post_thumbnail($pid, $mid);
        update_post_meta($mid, '_wp_attachment_image_alt', $alt);
        $now = (int) get_post_thumbnail_id($pid);
        $mark = ($now === $mid) ? 'OK ' : 'FAIL';
        echo "$mark  post $pid  thumb $cur -> $now   alt=\"$alt\"\n";
        ($now === $mid) ? $ok++ : $err++;
    } else {
        echo "DRY  post $pid  thumb $cur -> $mid   alt=\"$alt\"\n";
        $ok++;
    }
}

echo "\n--- " . ($live ? "applied" : "planned") . ": $ok  |  errors: $err  |  total: " . count($map) . " ---\n";
if ($live) echo "Now purge LiteSpeed cache, then delete this file.\n";
