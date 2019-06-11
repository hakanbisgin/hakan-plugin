<?php
class HakanWidget extends WP_Widget {

    function __construct() {
        // Instantiate the parent object
        parent::__construct( false, 'Hakan Top 10 Liked Posts' );
    }

    function widget( $args, $instance ) {
        // Widget output


        $willprint = '<div class="wrap">';
        $willprint .= '<h2> Top 10 liked POSTs :) </h2>';
        $stats_result = $this->get_top_ten_posts();
        if (!empty($stats_result)) {

            $willprint .= "<ul class='hakan_widget_list'>";
            foreach ($stats_result as $sr){
                $willprint .= "
                    <li class='hakan_widget_link'><a class='hakan_widget_link' href='".get_permalink($sr->ID)."'><strong>$sr->name</strong></a>
                    &nbsp&nbsp<span class='hakan_badge'>$sr->cnt</span></li>
                ";
            }

            $willprint .= '</ul>';
        }

        $willprint .= '</div>';
        echo $willprint;
    }

    function get_top_ten_posts(){
        global $wpdb;
        $query = "SELECT p.ID, p.post_title as name, COUNT(pl.ID) as cnt
FROM {$wpdb->prefix}posts as p
LEFT JOIN {$wpdb->prefix}post_likes as pl
ON p.ID = pl.post_id
WHERE post_type = 'post' AND 
post_status = 'publish'
GROUP BY p.ID  
ORDER BY COUNT(pl.ID) DESC
LIMIT 10";

        return $wpdb->get_results($query);

    }

    function update( $new_instance, $old_instance ) {
        // Save widget options
    }

    function form( $instance ) {
        // Output admin widget options form
    }
}





?>