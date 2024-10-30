<?php

function like_computy_buttons1(){
    global $wpdb;
 $iconpack='pack1';
 $post_id = get_the_ID();
 $sesid = session_id();
 $table_name = $wpdb->prefix . 'like_computy';



    $count1 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '1'" );
    $count2 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '2'" );
    $count3 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '3'" );
    $count4 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '4'" );
    $count5 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '5'" );
    $count6 = $wpdb->get_var( "SELECT COUNT(*) FROM " . $table_name. " WHERE  post_id = '$post_id' AND vote= '6'" );


    $table_lc = $wpdb->get_row( "SELECT * FROM " . $table_name. " WHERE  post_id = '$post_id' AND session_id = '$sesid'" );
    if(isset($table_lc->vote)){  $golos_stoit = $table_lc->vote;}else{$golos_stoit = '';}

    if($golos_stoit==1){$you_vote1 = 'active';}else{$you_vote1 ='';}
    if($golos_stoit==2){$you_vote2 = 'active';}else{$you_vote2 ='';}
    if($golos_stoit==3){$you_vote3 = 'active';}else{$you_vote3 ='';}
    if($golos_stoit==4){$you_vote4 = 'active';}else{$you_vote4 ='';}
    if($golos_stoit==5){$you_vote5 = 'active';}else{$you_vote5 ='';}
    if($golos_stoit==6){$you_vote6 = 'active';}else{$you_vote6 ='';}
    return '
    <div class="like_computy">
     <input class="vashvote" name="vashvote" type="hidden" value="'.$golos_stoit.'">
     <input class="sesid" name="sesid" type="hidden" value="'.$sesid.'">
     <input class="postid" name="postid" type="hidden" value="'.$post_id.'">
     
    <a href="#" class="emoji-item '.$iconpack.'-s1 '.$you_vote1.'" data-type="1" title="'. __( 'Like', 'like-computy' ).'" data-title="'. __( 'Нравится', 'like-computy' ).'">
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count1.'</span></a>
    
    <a href="#" class="emoji-item '.$iconpack.'-s6 '.$you_vote6.'" data-type="6" title="'. __( 'I do not like', 'like-computy' ).'" data-title="'. __( 'Не нравится', 'like-computy' ).'" >
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count6.'</span></a>
    
    <a href="#" class="emoji-item '.$iconpack.'-s2 '.$you_vote2.'" data-type="2" title="'. __( 'Ha-ha', 'like-computy' ).'" data-title="'. __( 'Ха-Ха', 'like-computy' ).'" >
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count2.'</span></a>
    
    <a href="#" class="emoji-item '.$iconpack.'-s3 '.$you_vote3.'" data-type="3" title="'. __( 'Amazingly', 'like-computy' ).'" data-title="'. __( 'Удивительно', 'like-computy' ).'" >
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count3.'</span></a>
    
    <a href="#" class="emoji-item '.$iconpack.'-s4 '.$you_vote4.'" data-type="4" title="'. __( 'Sad', 'like-computy' ).'" data-title="'. __( 'Грустно', 'like-computy' ).'" >
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count4.'</span></a>
    
    <a href="#" class="emoji-item '.$iconpack.'-s5 '.$you_vote5.'" data-type="5" title="'. __( 'Flagrantly', 'like-computy' ).'" data-title="'. __( 'Возмутительно', 'like-computy' ).'" >
     <div class="lc-svg-icon"></div>
    <span class="lc-value">'.$count5.'</span></a>
    </div> 
           ';
}

function like_computy_buttons() {
    global $wpdb;
    $iconpack = 'pack1';
    $post_id = get_the_ID();
    $sesid = session_id();
    $table_name = $wpdb->prefix . 'like_computy';

    // Используем один запрос для получения всех данных
    $counts = $wpdb->get_results($wpdb->prepare(
        "SELECT vote, COUNT(*) as count FROM " . $table_name . " WHERE post_id = %d GROUP BY vote",
        $post_id
    ), ARRAY_A);

    // Преобразуем результат в массив для удобства
    $vote_counts = array_fill(1, 6, 0);
    foreach ($counts as $row) {
        $vote_counts[$row['vote']] = $row['count'];
    }

    $table_lc = $wpdb->get_row($wpdb->prepare(
        "SELECT vote FROM " . $table_name . " WHERE post_id = %d AND session_id = %s",
        $post_id, $sesid
    ));

    // Устанавливаем активные голоса
    $golos_stoit = isset($table_lc->vote) ? $table_lc->vote : '';
    $you_votes = array_fill(1, 6, '');
    if ($golos_stoit) {
        $you_votes[$golos_stoit] = 'active';
    }

    // Формируем HTML
    $output = '<div class="like_computy">
        <input class="vashvote" name="vashvote" type="hidden" value="' . esc_attr($golos_stoit) . '">
        <input class="sesid" name="sesid" type="hidden" value="' . esc_attr($sesid) . '">
        <input class="postid" name="postid" type="hidden" value="' . esc_attr($post_id) . '">';

    for ($i = 1; $i <= 6; $i++) {
        if($i===1){
            $name_vote = __( 'Like', 'like-computy' );
        }elseif($i===2){
            $name_vote = __( 'Ha-ha', 'like-computy' );
        }elseif($i===3){
            $name_vote = __( 'Amazingly', 'like-computy' );
        }elseif($i===4){
            $name_vote = __( 'Sad', 'like-computy' );
        }elseif($i===5){
            $name_vote = __( 'Flagrantly', 'like-computy' );
        }elseif($i===6){
            $name_vote = __( 'I do not like', 'like-computy' );
        }
        $output .= '<a href="#" class="emoji-item ' . esc_attr($iconpack) . '-s' . $i . ' ' . esc_attr($you_votes[$i]) . '" data-type="' . $i . '" title="' . esc_attr($name_vote) . '" data-title="' . esc_attr($name_vote) . '">
            <div class="lc-svg-icon"></div>
            <span class="lc-value">' . esc_html($vote_counts[$i]) . '</span></a>';
    }

    $output .= '</div>';
    return $output;
}
