<?php

function get_page_count_for_tags(){
    global $wpdb;
    $query = "select COUNT(t.name) as cnt
FROM {$wpdb->prefix}terms as t
LEFT JOIN {$wpdb->prefix}term_taxonomy as ta
ON t.term_id = ta.term_id
WHERE ta.taxonomy = 'post_tag'";
    return (int)ceil((int)$wpdb->get_var($query)/10);

}

function get_stats($page_no = 1){
    global $wpdb;
    $query = "select t.name, COUNT(pl.id) as cnt
FROM {$wpdb->prefix}terms as t
LEFT JOIN {$wpdb->prefix}term_taxonomy as ta
ON t.term_id = ta.term_id
LEFT JOIN {$wpdb->prefix}term_relationships as tr
ON ta.term_taxonomy_id = tr.term_taxonomy_id
LEFT JOIN {$wpdb->prefix}posts as p
ON tr.object_id = p.ID
LEFT JOIN {$wpdb->prefix}post_likes as pl
ON p.ID = pl.post_id
WHERE ta.taxonomy = 'post_tag'
GROUP BY t.name
ORDER BY COUNT(pl.id) DESC
LIMIT 10 OFFSET ".(((int)$page_no-1) * 10);
    //SELECT COUNT(id) FROM {$wpdb->prefix}post_likes WHERE post_id= %d AND user_id= %s"
    return $wpdb->get_results($query);

}

function print_pagination_for_tags(){
    $page_count = get_page_count_for_tags();
    $page_list = "<ul>";

    for($i = 1; $i <= $page_count; $i++){
        $page_list .= "<li class='pagination_link'><a href='?page=hakan-stats&page_no=$i'>$i</a></li>";
    }

    $page_list .= "</ul>";
    echo $page_list;
}

function show_stats($stats_result){

    if (!empty($stats_result)) {
        ?>
        <table class="hakan_table">
            <thead>
            <tr>
                <td>etiket adı</td>
                <td>like sayısı</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stats_result as $sr){ ?>
                <tr>
                    <td><?=$sr->name?></td>
                    <td><?=$sr->cnt?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
    }

}

$page_no = (int)$_GET['page_no'];
if(isset($page_no) && !is_null($page_no) && !empty($page_no)){
    $stats_result = get_stats($page_no);

}else {
    $stats_result = get_stats();
}

?>
<div class="wrap">
    <h1>Top Liked Tags</h1>
    <?php

    show_stats($stats_result);
    print_pagination_for_tags();
    ?>
</div>

