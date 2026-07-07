<?php
/**
 * mkk-fix-v3.php  —  thechristianlyrics.com  (Session 12)
 * Fully HARDCODED updater for LIST A + LIST B (31 posts).
 *
 * Sets, per post:
 *   - rank_math_focus_keyword   (verified Manglish first line, cleaned)
 *   - rank_math_description      (<=160 chars, verified)
 * Sets, per media (14 items):
 *   - _wp_attachment_image_alt   (= first 3 words of the post's FK)
 *
 * Does NOT touch: rank_math_title, Elementor meta, post content, TOC.
 *
 * USAGE (from WP root):
 *   Dry run  :  wp eval-file mkk-fix-v3.php            (default, no writes)
 *   Live run :  wp eval-file mkk-fix-v3.php live
 *   Browser  :  /mkk-fix-v3.php?dry=1   or   /mkk-fix-v3.php?live=1
 *
 * After a successful live run, DELETE this file from the server.
 */

if ( ! defined( 'ABSPATH' ) ) {
    // Allow direct browser execution: locate wp-load.php from this file's dir.
    $wp_load = __DIR__ . '/wp-load.php';
    if ( file_exists( $wp_load ) ) { require_once $wp_load; }
}

// ---- Mode detection -------------------------------------------------------
$LIVE = false;
if ( PHP_SAPI === 'cli' ) {
    $LIVE = in_array( 'live', $argv, true );
} else {
    $LIVE = isset( $_GET['live'] );
    header( 'Content-Type: text/plain; charset=utf-8' );
}
$MODE = $LIVE ? 'LIVE' : 'DRY-RUN';

// ---- Data: post_id => [ fk, meta ] ---------------------------------------
// 31 posts (LIST A 22 + LIST B 9). All values verified in Session 12.
$POSTS = array(
    // id      focus_keyword                                  meta_description
    1642  => array( 'Yeshuve dhyanikkumbol njan',            'Yeshuve Dhyanikkumbol Njan lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1581  => array( 'Aashisham nalkename mashihaaye',        'Aashisham Nalkename lyrics from Marthoma Kristheeya Keerthanangal by T.J. Varkey. Read Malayalam & Manglish lyrics and listen online.' ),
    1696  => array( 'Yeshuvin naamam madhurimanaamam',       'Yeshuvin Naamam lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1382  => array( 'Koode paarkka neram',                   'Koode Paarkka Neram lyrics from Marthoma Kristheeya Keerthanangal by Rev. T. Koshy. Read Malayalam & Manglish lyrics and listen online.' ),
    1562  => array( 'Varuvin naam yahovaykku',               'Varuvin Naam Yahovakku lyrics from Marthoma Kristheeya Keerthanangal by Yusthus Joseph. Read Malayalam & Manglish lyrics and listen online.' ),
    1588  => array( 'Anugrahakkadale ezhunnalli varikayi',   'Anugrahakkadale lyrics from Marthoma Kristheeya Keerthanangal by Sadhu Kochukunju Upadesi. Read Malayalam & Manglish lyrics and listen online.' ),
    1654  => array( 'Shree yeshu naamame',                   'Shree Yeshu Naamame Thirunamam lyrics from Marthoma Kristheeya Keerthanangal by T.J. Varkey. Read Malayalam & Manglish lyrics and listen online.' ),
    164   => array( 'Yeshu ennulla naamamey',                'Yeshu Ennulla Naamame lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1682  => array( 'Naadha choriyaname nin krupa',          'Naadha Choriyaname lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1690  => array( 'Shree yeshu naamam',                    'Shreeyeshu Naamam lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1702  => array( 'Naathane en yeshuve',                   'Naathane En Yeshuve lyrics from Marthoma Kristheeya Keerthanangal by Moshavatsalam. Read Malayalam & Manglish lyrics and listen online.' ),
    1708  => array( 'Hallelujah hallelujah hallelujah',      'Hallelujah Hallelujah Hallelujah Amen lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1710  => array( 'Senakalil paran yahovaa',               'Senakalil Paran lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1719  => array( 'Yeshu devaa yeshu',                     'Yeshu Deva Yeshu Naayaka lyrics from Marthoma Kristheeya Keerthanangal by Yusthus Joseph. Read Malayalam & Manglish lyrics and listen online.' ),
    1742  => array( 'Deva nandanaa vandanam',                'Deva Nandana Vandanam lyrics from Marthoma Kristheeya Keerthanangal by Yusthus Joseph. Read Malayalam & Manglish lyrics and listen online.' ),
    3165  => array( 'Paadam vandikkunnen thirukrupa',        'Paadam Vandikkunnen Thirukrupa lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    3372  => array( 'Krysthavare vandanekkunarin kristhu',   'Krysthavare Vandanekkunarin lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    9502  => array( 'Adbhuthane yesunatha atyunnatha',       'Athbudhane Yeshu Nadha lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    10068 => array( 'Vaanalokathezhunnellinaal sreeyeshu naathan', 'Vaanalokathezhunnellinaal Sreeyeshu lyrics from Marthoma Kristheeya Keerthanangal by Yusthus Joseph. Read Malayalam & Manglish lyrics and listen online.' ),
    10075 => array( 'Paapikalin rakshakan thaa',             'Paapikalin Rakshakan lyrics from Marthoma Kristheeya Keerthanangal by K.V. Simon. Read Malayalam & Manglish lyrics and listen online.' ),
    3166  => array( 'Papathin van vishathe',                 'Papathin Van Vishathe Ozhippan lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1951  => array( 'Njan varunnu krushinkal',               'Njan Varunnu Krushinkal lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    // ---- LIST B (9) ----
    3374  => array( 'Athyathbhuthame athyathbhuthame paavana','Athyathbhuthame Athyathbhuthame lyrics from Marthoma Kristheeya Keerthanangal by Rev. K.P. Philip. Read Malayalam & Manglish lyrics and listen online.' ),
    1851  => array( 'Aadukalkuvendi jeevane vedinjathaam',   'Aadukalkuvendi Jeevane lyrics from Marthoma Kristheeya Keerthanangal by T.J. Varkey. Read Malayalam & Manglish lyrics and listen online.' ),
    3375  => array( 'Jaathanayi jaathanayinnu loka',         'Jaathanayi Jaathanayinnu lyrics from Marthoma Kristheeya Keerthanangal by Rev. K.P. Philip. Read Malayalam & Manglish lyrics and listen online.' ),
    943   => array( 'Mahathwa prabhu maricha',               'Mahathwa Prabhu Maricha lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1207  => array( 'Varika paraaparane ee yogathil',        'Varika Paraa Parane lyrics from Marthoma Kristheeya Keerthanangal by P.D. John. Read Malayalam & Manglish lyrics and listen online.' ),
    3377  => array( 'Vaagdatha sampoorthiyaayi thiruvavathaaram', 'Vaagdatha Sampoorthiyaayi lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    1785  => array( 'Yeshu mahonnathane',                    'Yeshu Mahonnathane lyrics from Marthoma Kristheeya Keerthanangal by T.J. Varkey. Read Malayalam & Manglish lyrics and listen online.' ),
    3370  => array( 'Paadin inpageetham inneshu',            'Paadin Inpageetham lyrics from Marthoma Kristheeya Keerthanangal. Read full Malayalam & Manglish lyrics and listen online.' ),
    12091 => array( 'Ninsannidhiyil daivame poornasanthosham','Kristhuvin Sannidhi lyrics from Marthoma Kristheeya Keerthanangal by Moshavatsalam. Read Malayalam & Manglish lyrics and listen online.' ),
);

// ---- Data: media_id => alt_text (first 3 words of the post's FK) ----------
$MEDIA = array(
    12107 => 'Yeshuve dhyanikkumbol njan',
    12106 => 'Varuvin naam yahovaykku',
    12105 => 'Anugrahakkadale ezhunnalli varikayi',
    12104 => 'Shree yeshu naamame',
    12103 => 'Yeshu ennulla naamamey',
    12102 => 'Naadha choriyaname nin',
    12101 => 'Yeshu devaa yeshu',
    12100 => 'Athyathbhuthame athyathbhuthame paavana',
    12099 => 'Aadukalkuvendi jeevane vedinjathaam',
    12098 => 'Jaathanayi jaathanayinnu loka',
    12097 => 'Yeshu mahonnathane',
    12096 => 'Varika paraaparane ee',
    12095 => 'Paadin inpageetham inneshu',
    12094 => 'Ninsannidhiyil daivame poornasanthosham',
);

// ---- Run ------------------------------------------------------------------
echo "=== mkk-fix-v3.php  [$MODE] ===\n\n";

$errors = 0;

echo "--- POSTS (FK + meta) ---\n";
foreach ( $POSTS as $id => $row ) {
    list( $fk, $meta ) = $row;
    $len = mb_strlen( $meta );
    $flag = ( $len > 160 ) ? '  !!! OVER 160' : '';
    if ( $flag ) { $errors++; }

    $post = get_post( $id );
    if ( ! $post ) {
        echo sprintf( "  [MISSING] id %d not found\n", $id );
        $errors++;
        continue;
    }

    echo sprintf( "  #%-6d fk=\"%s\"\n", $id, $fk );
    echo sprintf( "          meta(%d)=\"%s\"%s\n", $len, $meta, $flag );

    if ( $LIVE ) {
        update_post_meta( $id, 'rank_math_focus_keyword', $fk );
        update_post_meta( $id, 'rank_math_description',   $meta );
    }
}

echo "\n--- MEDIA (alt text) ---\n";
foreach ( $MEDIA as $mid => $alt ) {
    $att = get_post( $mid );
    if ( ! $att || $att->post_type !== 'attachment' ) {
        echo sprintf( "  [MISSING] media %d not an attachment\n", $mid );
        $errors++;
        continue;
    }
    echo sprintf( "  media %-6d alt=\"%s\"\n", $mid, $alt );
    if ( $LIVE ) {
        update_post_meta( $mid, '_wp_attachment_image_alt', $alt );
    }
}

echo "\n=== SUMMARY ===\n";
echo sprintf( "Posts: %d | Media: %d | Problems: %d | Mode: %s\n",
    count( $POSTS ), count( $MEDIA ), $errors, $MODE );
if ( ! $LIVE ) {
    echo "No changes written. Re-run with 'live' to apply.\n";
} else {
    echo "Changes APPLIED. Now delete this file from the server.\n";
}
