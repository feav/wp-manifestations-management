<?php
/**
* @package WPMM
*/
/*
	Plugin Name: Management des Manifestations
	  Plugin URI: https://www.ars-agency.com
	  Description: Permettre de programmer des manifestations 
	  Version: 1.0
	  Author: ARS GROUP
	  Author URI: http://www.ars-agency.com
	 */

	define('WPMM_PLUGIN_FILE',__FILE__);
	define('WPMM_DIR', plugin_dir_path(__FILE__));
	 
	define('WPMM_URL', plugin_dir_url(__FILE__));

	define('WPMM_API_URL_SITE', get_site_url() . "/");



	class WpManifestationManagement {
		private $post_type;

	    function __construct() {
			$this->post_type = 'wp_manifestation';
			add_action( 'init', array(&$this,'register_post_types'), 0 );
			add_shortcode( 'wpm-mes-manifs', array(&$this,'mes_manifestations') );
			add_shortcode( 'wpm-ajout-manif', array(&$this,'ajout_manifestations') );
			add_shortcode( 'wpm-toutes-manif', array(&$this,'toutes_manifestations') );

			add_action('add_meta_boxes', array(&$this,'wporg_add_custom_box'));
			add_action('save_post', array(&$this,'wporg_save_postdata'));
			add_action('manage_'.$this->post_type.'_posts_custom_column', array(&$this, 'custom_manifestation_columns'), 15, 3);
			add_filter('manage_'.$this->post_type.'_posts_columns', array(&$this, 'manifestation_columns'), 15, 1);
			//add_filter( 'cron_schedules', array(&$this,'add_cron_interval') );
			/**
			** allow other users to show future projets
			**/
			add_filter('get_post_status', array(&$this,'make_future_post_visible_public') , 10, 2);

			add_action( 'pre_get_posts', array(&$this,'wpa84258_admin_posts_sort_last_name') );
			add_action( 'init', array(&$this, 'wpse17478_init' ));
			add_filter( 'get_the_excerpt', array(&$this, 'wpse17478_get_the_excerpt') );

	    }  
	    function wpse17478_get_the_excerpt( $excerpt )
		{
		    if ( '' == $excerpt ) {
		        return '';
		    }
		    return $excerpt;
		}
		function wpse17478_init()
		{
		    remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
		}
	    function make_future_post_visible_public($post_status, $post) {
			    if ($post->post_type == $this->post_type && $post_status == 'future') {
			        return "publish";
			    }
			    return $post_status;
		}
		function wpa84258_admin_posts_sort_last_name( $query ){
		    global $pagenow;
		    if( is_admin()
		        && 'edit.php' == $pagenow
		        && !isset( $_GET['orderby'] )
		        && isset( $_GET['post_type'] ) 
		        && $_GET['post_type'] == $this->post_type 
		    ){
		    		$query->set( 'post_type', $this->post_type ); 
		            $query->set( 'meta_key', 'postVisibility' );
		            $query->set( 'orderby', 'meta_value' );
		            $query->set( 'order', 'ASC' );
		    }
		}



		function add_cron_interval( $schedules ) {
			 $schedules['daily'] = array(
			 'interval' => 86400,	
			 'display' => esc_html__( 'Every Days' ),
			 );
			return $schedules;
		 }
	    function manifestation_columns($defaults ){

				$defaults['manifestation_visible'] = esc_html__('Etat', 'wp_manifestation_manage');
				$defaults['manifestation_date'] = esc_html__('Date', 'wp_manifestation_manage');
				return $defaults;
	    }

	    function custom_manifestation_columns($column_name, $postid){
			if ( $column_name == 'manifestation_visible' ) {
			 	$name = get_post_meta($postid,  'postVisibility',  false )[0];
				if($name ==1){
					echo "<span style='background: #63f597;color: white;padding: 6px;'>Deja Approuvee</span>";
				}else if($name == -1){
					echo "<span style='background: #f56363;color: white;padding: 6px;'> Masque par l'utilisateur</span>";
				}	else {

					echo "<span style='background: #f56363;color: white;padding: 6px;'> En Attente d'Approbation</span>";
				}		

			}else 
			if ( $column_name == 'manifestation_date' ) {
			 	$name = get_post_meta($postid,  'postDate',  false )[0];
				echo $name;
			}

	    }
	     function wpse27856_set_content_type(){
            return "text/html";
        }
	    function notification_email($post_id,$visible){
			
	    }
	    function wpcs_set_email_content_type() {
		    return 'text/html';
		}
	    function wporg_save_postdata($post_id)
		{
		    if (array_key_exists('postThematique', $_POST)) {
		    	$metas = array("postThematique","postEmail","postPhone","postCountry","postTown","postDate","postHour","postVisibility");
		    	$visible = $_POST['postVisibility'];
        		$visibleOld = get_post_meta($post_id, 'postVisibility', true);

        		if(intval($visible) != $visibleOld){
        				/* localhost */
						$id_edit_page = 249;
						$list_page = 242;
						$id_created_page = 249;

						/* online */
						$id_edit_page = 3312;
						$list_page = 3310;
						$id_created_page = 3310;
						$email = get_post_meta($post_id, 'postEmail', true);
				        add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
				        $to =  $email;
				        $subject = 'Votre Manifestation a été Publiée';
				        $link_created = esc_url( get_page_link( $id_created_page ) );
				        $link_post = esc_url( get_page_link( $post_id ) );
				        $link_updated = esc_url( get_page_link( $id_edit_page )."?post=".$post_id );
				        
        				$post_url = get_the_permalink($post_id);
				        $headers = array('From:Manifestation <contact@goldtimerepair.com >');
				        
				        add_filter( 'wp_mail_content_type', array(&$this,'wpcs_set_email_content_type') );

				        $body =  '';
				        if($visible){
					        $body = '
					        <div style="background: #fffbeb;padding: 15px 30px 30px 30px;text-align: center;">
					            <h1>Votre Manifestation a été Publiée</h1>
					            <br>
					            <h2>'.wp_strip_all_tags( get_the_title($post_id) ).'</h2>

					            <p>Voici le lien unique, que vous pouvez partager pour informer votre public des détails de votre manifestation, il suffit de cliquer sur le lien <a href="'.$post_url.'">Ma Manifestation</a></p>
					            <p>Vous pouvez modifier votre manifestation a tout moment , ou décider de ne plus la rendre visible sur notre site en cliquant sur ce lien <a href="'.$link_updated.'">Modifier ma Manifestation</a></p>
					            <p>Vous pouvez créer autant de Manifestations que vous désirez sur notre plateforme en suivant ce lien <a href="'.$link_created.'">Ajouter une autre Manifestation</a></p>

					        </div>
					        ';  
					    }else{

				        $subject = 'Votre Manifestation a été Modérée';
					        $body = '
					        <div style="background: #fffbeb;padding: 15px 30px 30px 30px;text-align: center;">
					            <h1>Votre Manifestation a été Modérée est ne sera plus visible sur le site</h1>
					            <br>
					            <h2>'.wp_strip_all_tags( get_the_title($post_id) ).'</h2>

					            <p>Vous pouvez modifier votre manifestation a tout moment  en cliquant sur ce lien <a href="'.$link_updated.'">Modifier ma Manifestation</a></p>
					            <p>Vous pouvez créer autant de Manifestations que vous désirez sur notre plateforme en suivant ce lien <a href="'.$link_created.'">Ajouter une autre Manifestation</a></p>

					        </div>
					        ';  
					    }
				        wp_mail( $to, $subject, $body, $headers );
        		}
		    	foreach ($_POST as $key => $value) {
		    		if (in_array($key, $metas)) {
					   update_post_meta($post_id,$key,$value);
					}
		    	}

		        
		    }
		}
		function wporg_add_custom_box()
		{
		    $screens = [$this->post_type, 'wporg_cpt'];
		    foreach ($screens as $screen) {
		        add_meta_box(
		            'manifestation_',           // Unique ID
		            'Details de la Manifestation',  // Box title
		            array($this,'wporg_custom_box_html'),  // Content callback, must be of type callable
		            $screen                   // Post type
		        );
		    }
		}
		function wporg_custom_box_html($post)
		{
			include(WPMM_DIR.'template/html/admin-manifestation-detai.php');
		}
		function meta_box_manifestation( $meta_boxes ) {
			$prefix = 'manifestation_';

			$meta_boxes[] = array(
				'id' => 'meta',
				'title' => esc_html__( 'Details', 'wp_manifestation_manage' ),
				'post_types' => array($this->post_type),
				'context' => 'side',
				'priority' => 'high',
				'autosave' => 'false',
				'fields' => array(
					array(
						'id' => $prefix . 'theme',
						'type' => 'text',
						'name' => esc_html__( 'Thematique', 'wp_manifestation_manage' ),
					),
					array(
						'id' => $prefix . 'email',
						'name' => esc_html__( 'Email Organisateur', 'wp_manifestation_manage' ),
						'type' => 'email',
					),
					array(
						'id' => $prefix . 'phone',
						'type' => 'text',
						'name' => esc_html__( 'Telephone Organisateur', 'wp_manifestation_manage' ),
					),
					array(
						'id' => $prefix . 'visible',
						'name' => esc_html__( 'Rendre Visible', 'wp_manifestation_manage' ),
						'type' => 'radio',
						'placeholder' => '',
						'options' => array(esc_html__( 'Cacher###', '###Voir', 'wp_manifestation_manage' ) ),
						'inline' => 'true',
						'std' => '1',
					),
					array(
						'id' => $prefix . 'date',
						'type' => 'date',
						'name' => esc_html__( 'Date Programmation', 'wp_manifestation_manage' ),
					),
					array(
						'id' => $prefix . 'time',
						'name' => esc_html__( 'Heure Programmation', 'wp_manifestation_manage' ),
						'type' => 'time',
					),
					array(
						'id' => $prefix . 'gallery',
						'type' => 'image_advanced',
						'name' => esc_html__( 'Gallery', 'wp_manifestation_manage' ),
					),
				),
			);

			return $meta_boxes;
		}

		// Register Custom Post Type
		function register_post_types() {

			$labels = array(
				'name'                  => _x( 'Manifestations', 'Post Type General Name', 'wp_manifestation_manage' ),
				'singular_name'         => _x( 'Manifestation', 'Post Type Singular Name', 'wp_manifestation_manage' ),
				'menu_name'             => __( 'Manifestations', 'wp_manifestation_manage' ),
				'name_admin_bar'        => __( 'Manifestation', 'wp_manifestation_manage' ),
				'archives'              => __( 'Archive des Manifestations', 'wp_manifestation_manage' ),
				'attributes'            => __( 'Attributs d\'une manifestation', 'wp_manifestation_manage' ),
				'parent_item_colon'     => __( 'Parent de la Manifestation', 'wp_manifestation_manage' ),
				'all_items'             => __( 'Toutes les manifestations', 'wp_manifestation_manage' ),
				'add_new_item'          => __( 'Ajouter une Manifestation', 'wp_manifestation_manage' ),
				'add_new'               => __( 'Ajouter une nouvelle', 'wp_manifestation_manage' ),
				'new_item'              => __( 'Nouvelle Manifestation', 'wp_manifestation_manage' ),
				'edit_item'             => __( 'Editer la manifestation', 'wp_manifestation_manage' ),
				'update_item'           => __( 'Mettre a jour la manifestation', 'wp_manifestation_manage' ),
				'view_item'             => __( 'Voir la Manifestation', 'wp_manifestation_manage' ),
				'view_items'            => __( 'Voir les Manifestations', 'wp_manifestation_manage' ),
				'search_items'          => __( 'Rechercher une Manifestation', 'wp_manifestation_manage' ),
				'not_found'             => __( 'Manifestation non trouvee', 'wp_manifestation_manage' ),
				'not_found_in_trash'    => __( 'Pas trouve dans la corbeille', 'wp_manifestation_manage' ),
				'featured_image'        => __( 'Multiples Images', 'wp_manifestation_manage' ),
				'set_featured_image'    => __( 'Modifer l\'image', 'wp_manifestation_manage' ),
				'remove_featured_image' => __( 'Retirer l\'image', 'wp_manifestation_manage' ),
				'use_featured_image'    => __( 'Utiliser comme image', 'wp_manifestation_manage' ),
				'insert_into_item'      => __( 'inserer dans la manifestation', 'wp_manifestation_manage' ),
				'uploaded_to_this_item' => __( 'Charger dans la manifestations', 'wp_manifestation_manage' ),
				'items_list'            => __( 'Liste de Manifestations', 'wp_manifestation_manage' ),
				'items_list_navigation' => __( 'Les Manifestations', 'wp_manifestation_manage' ),
				'filter_items_list'     => __( 'Filtrer les manifestations', 'wp_manifestation_manage' ),
			);
			$args = array(
				'label'                 => __( 'Manifestation', 'wp_manifestation_manage' ),
				'description'           => __( 'Les differentes manifestations', 'wp_manifestation_manage' ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'thumbnail','excerpt' ),
				'taxonomies'            => array( 'category', 'post_tag' ),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 10,
				'menu_icon'             => 'dashicons-admin-comments',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => 'manifestations',
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
				'show_in_rest'          => true,
			);
			register_post_type( $this->post_type, $args );

		}

	    // Add Shortcode
		function mes_manifestations() {
	    	include(WPMM_DIR.'template/html/menu.php');
			include(WPMM_DIR.'template/html/mes-manifestations.php');
		}

	    function ajout_manifestations() {			
	    	include(WPMM_DIR.'template/html/menu.php');
			include(WPMM_DIR.'template/html/ajouter-manifestation.php');
		}

	    function toutes_manifestations() {
			include(WPMM_DIR.'template/html/menu.php');
			include(WPMM_DIR.'template/html/toutes-manifestations.php');
		}
	}

	new WpManifestationManagement();