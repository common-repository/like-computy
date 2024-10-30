<?php
/*class admin page*/
class Like_Computy_Admin {
    public function __construct()
    {

        $this->plugin_name = LIKE_COMPUTY;

    }
    public  static function init() {
        add_action( 'admin_menu', array( 'Like_Computy_Admin', 'add_admin_menu' ) );/* инициализируем меню в админке*/
        add_action( 'admin_enqueue_scripts', array( 'Like_Computy_Admin', 'load_scripts' ) );/*Загружаем скрипты и стили*/
        add_action( 'admin_init', array( 'Like_Computy_Admin', 'plugin_settings' ) );/*Вывод настроек в меню*/

      add_filter( 'plugin_action_links_like-computy/like-computy.php' , array( 'Like_Computy_Admin', 'like_plugin_settings_link' ) ); /*добавляем ссылку на настройки на странице плагинов */
    }


    public static function plugin_settings() {
        /*Вывод настроек в меню*/


    }


    public static function like_plugin_settings_link( $links ) {
        /*добавляем ссылку на настройки на странице плагинов */
        $settings_link = '<a href="admin.php?page=like_computy_options">'.__( 'Settings and results', 'like-computy' ).'</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    public static function add_admin_menu() {
        /* инициализируем меню в админке*/
        $menu_title =  __('Like computy', 'like-computy');
        add_menu_page( $menu_title, $menu_title, 'edit_others_posts', 'like_computy_options', array( 'Like_Computy_Admin', 'like_computy_options'  ), 'dashicons-thumbs-up', 20 );

            }


    public static function load_scripts() {
        /*Загружаем скрипты и стили*/
        wp_register_style( 'like-computy-style-admin', plugin_dir_url( __FILE__ ) . '/like-computy-style-admin.css', array(), LIKE_COMPUTY_VERSION );
        wp_enqueue_style( 'like-computy-style-admin' );
    }


    public static function like_computy_options(){

        /*Страница меню*/
        if( current_user_can('manage_options') ){?>

            <div class="wrap like-computy-admin">

            <h2><?php echo _e( 'Like computy', 'like-computy' ), ' v', LIKE_COMPUTY_VERSION; ?></h2>
            <p><?php echo __( 'With the support of', 'like-computy' );?> <a href="https://computy.ru" target="_blank" title="Разработка и поддержка сайтов на WordPress"> computy </a> <br>
                <a href="https://yoomoney.ru/to/410011302808683" target="_blank"><?php echo __( 'Throw money at me!', 'like-computy' );?></a><br>
                <a href="https://computy.ru/blog/plugin-like-computy" target="_blank"><?php echo __( 'About plugin', 'like-computy' );?></a>
            </p>
            <hr>
            <h2><?php echo _e( 'Plugin description', 'like-computy' ); ?></h2>
            <p><?php echo __( 'This plugin adds to your site a voting system consisting of six votes: like, dislike, funny, amazing, sad, scary.', 'like-computy' ); ?></p>
            <p><img src="<?php echo LIKE_COMPUTY_PLUGIN_URL;?>admin/img/1.jpg" alt=""></p>
            <p><?php echo __( 'On each page, a visitor can put only one vote. Both registered and non-registered users can vote. <br> Plugin is protected from cheating votes.', 'like-computy' ); ?></p>
            <p><?php echo __( 'For the plugin to work, you need to insert the <b> [buttons_like_computy] </b> shortcode in the article editor. <br> If you want to insert the shortcode into the php files of your theme, insert this code:', 'like-computy' ); ?><b> &#60;?php echo do_shortcode( '[buttons_like_computy]' ); ?&#62;</b></p>

            <p><?php echo __( 'To display a list of the most popular articles, use the <b>[popular_like_computy count="5"]</b> shortcode, where count is the number of posts displayed in descending order. Popularity is calculated by the total number of likes received.', 'like-computy' ); ?></p>

        <?php }
    }


}