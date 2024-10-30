<?php
/*
* Plugin Name:   Like computy
* Version:       1.4.5
* Text Domain:   like-computy
* Plugin URI:    https://computy.ru/blog/plugin-like-computy
* Description:    Displaying like buttons for an article, page or product card using the [buttons_like_computy] shortcode.
* Author:        computy
* Author URI:    https://computy.ru
*/
if ( !defined( 'ABSPATH' ) ) exit;
define( 'LIKE_COMPUTY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'LIKE_COMPUTY_PLUGIN_URL' ) ) {
    // Return http://site.ru/wp-content/plugins/like-computy/
    define( 'LIKE_COMPUTY_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}
define( 'LIKE_COMPUTY_VERSION', '1.4.5' );

/*Страница админки*/
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( LIKE_COMPUTY_PLUGIN_DIR . '/admin/settings.php' );
    add_action( 'init', array( 'Like_Computy_Admin', 'init' ) );
}
/*Страница админки*/



/*Функция, которая запускается при активации плагина*/
register_activation_hook( __FILE__, 'like_computy_activate' );
function like_computy_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'like_computy';

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		post_id mediumint(10) NOT NULL,
		session_id varchar(60) DEFAULT '' NOT NULL,
		vote mediumint(10) NOT NULL,
		date_vote datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

//работа с сессиями
function like_computy_session_start()
{
    $sn = session_name();
    if (isset($_COOKIE[$sn])) {
        $sessid = $_COOKIE[$sn];
    } elseif (isset($_GET[$sn])) {
        $sessid = $_GET[$sn];
    } else {
        return session_start(['read_and_close' => true]);
    }

    if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $sessid)) {
        return false;
    }
    return session_start(['read_and_close' => true]);
}

add_action('mu_plugin_loaded', 'myStartSession', 1);
function myStartSession() {
    if (!isset($_SESSION)) {
        like_computy_session_start();
    }

}


/*Обработка ajax*/
function js_variables(){
    $variables = array (
        'ajax_url' => admin_url('admin-ajax.php'),
        'is_mobile' => wp_is_mobile()
    );
    echo('<script type="text/javascript">window.wp_data = '.json_encode($variables).
        ';</script>'
    );
}
add_action('wp_head','js_variables');

if( wp_doing_ajax() ) {
    add_action('wp_ajax_get_like_computy_value', 'get_like_computy_value_callback');
    add_action('wp_ajax_nopriv_get_like_computy_value', 'get_like_computy_value_callback');
}
function get_like_computy_value_callback(){
    //тут обработка первого аякс запроса
$sesid = sanitize_text_field($_POST['sesid']);
$postid = sanitize_text_field($_POST['postid']);
$voteid = sanitize_text_field($_POST['voteid']);

//проверяем есть ли голос у этого sesid в этой postid
    // подготавливаем данные
    global $wpdb;
    $table_name = $wpdb->prefix . 'like_computy';

    $table_lc = $wpdb->get_results( "SELECT * FROM " . $table_name. " WHERE  post_id = '$postid' AND session_id = '$sesid'" );
$i=0;
    foreach ( $table_lc as $tlc ){
        $i++;
    }
    if($i>0){
          //если выбран этот же голос, то ничего не делаем
        $table_lc = $wpdb->get_row( "SELECT * FROM " . $table_name. " WHERE  post_id = '$postid' AND session_id = '$sesid'" );
       $golos_stoit = $table_lc->vote;
        if($golos_stoit == $voteid){
            echo 'etotgolos';
        }else{
            //если выбран другой голос, то меняем (удаляем один и добавляем другой)
            $wpdb->update( $table_name, array('vote' => $voteid, 'date_vote' => date("Y-m-d H:i:s"),),array('post_id'=>$postid,'session_id'=>$sesid));
            echo 'drugoygolos';
        }


    }else{

       //голоса у этого sesid нет, значит можно добавлять
        $wpdb->insert( $table_name, array(
            'post_id' => $postid,
            'session_id' => $sesid,
            'vote' => $voteid,
            'date_vote' => date("Y-m-d H:i:s"),
        ), array("%s", "%s", "%s", "%s")  );
        echo 'voteadd';
    }
}
/*Обработка ajax*/



/*добавляем стили на фронте*/
function like_computy_styles() {
    wp_register_style( 'like-computy-style', plugin_dir_url( __FILE__ ) . 'view/like-computy-style.css' );
    wp_enqueue_style( 'like-computy-style' );
}
add_action( 'wp_enqueue_scripts', 'like_computy_styles' );

/*добавляем скрипты на фронте*/
function like_computy_script() {
    wp_register_script( 'like-computy-script', plugin_dir_url( __FILE__ ) . 'view/like-computy-script.js', array( 'jquery' ), null, true );
    wp_enqueue_script( 'like-computy-script' );
}
add_action( 'wp_enqueue_scripts', 'like_computy_script' );


/*функция вывода лайков*/
function get_like_computy_buttons_template(){
    require_once (LIKE_COMPUTY_PLUGIN_DIR.'view/template_buttons.php');
    return like_computy_buttons();
}



/*вывод лайков с помощью шорткода*/
add_shortcode( 'buttons_like_computy', 'like_computy_shortcode' );
function like_computy_shortcode(){
    $userid = get_current_user_id();//  id пользователя
    return get_like_computy_buttons_template();
}
/*вывод лайков с помощью шорткода*/

/*вывод самых популярных */
add_shortcode( 'popular_like_computy', 'popular_like_computy_shortcode' );
function popular_like_computy_shortcode($atts){
    $atts = shortcode_atts( [
        'count' => 5,
    ], $atts );
    $limit = $atts['count'];
    global $wpdb;
    $table_name = $wpdb->prefix . 'like_computy';

     $table_lc = $wpdb->get_results( "SELECT post_id, COUNT(vote) as vote FROM " . $table_name. " GROUP BY post_id ORDER BY vote DESC LIMIT $limit " );

   $table_lc = json_decode(json_encode($table_lc), true);

    $students = [];
    $k='';
    foreach ($table_lc as $value=>$item){
        $students[$k] = $item['post_id'];
        $k++;
    }


    $query = new WP_Query( [
        'post__in'  => $students,
        'orderby' => 'post__in'
    ] );
    echo '<ul>';
    while  ($query->have_posts() ) : $query->the_post(); ?>
        <li class="item-like-computy"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></li>
    <?php endwhile; wp_reset_postdata();
    echo '</ul>';
}


/*вывод самых популярных */

/*вывод вконце страницы или записи*/
/*add_filter('the_content', 'like_computy_before_after');
function like_computy_before_after($content) {
    if(is_page() || is_single()) {
        $aftercontent =    get_like_computy_buttons_template();;
        $fullcontent =  $content . $aftercontent;
    } else {
        $fullcontent = $content;
    }
    return $fullcontent;
}*/
/*вывод вконце страницы или записи*/
