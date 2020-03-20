<?php 
$args = array(
    'post_type'              => array( 'wp_manifestation' ),
    'nopaging'               => true,
    'order'                  => 'ASC',
    'orderby'                => 'modified',
);

$query = new WP_Query( $args ); 
?>
<?php  
    $title='';
    $content = '';
    $thematique = '';
    $phone = '';
    $email = '';
    $thematique = '';
    $pays = '';
    $ville = '';
    $heure = '';
    $date = '';


 ?>
 
<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
 
<?php
   // global $current_post;
   if ( isset( $_GET['post'] ) ) {
     
        $post_id = $_GET['post'];
        $email = get_post_meta($post_id, 'postEmail', true);
        $phone = get_post_meta($post_id, 'postPhone', true);
        $thematique = get_post_meta($post_id, 'postThematique', true);
        $pays = get_post_meta($post_id, 'postCountry', true);
        $ville = get_post_meta($post_id, 'postTown', true);
        $heure = get_post_meta($post_id, 'postHour', true);
        $date = get_post_meta($post_id, 'postDate', true);
        $visible = get_post_meta($post_id, 'postVisibility', true);

        if ( $_GET['post'] == get_the_ID()  )
        {
            $current_post = get_the_ID() ;
            $title = get_the_title();
            $content = get_the_content();
            if(isset($_POST['postContent'])){
                $post_information = array(
                    'ID' => $current_post,
                    'post_title' =>  wp_strip_all_tags( $_POST['postTitle'] ),
                    'post_content' => $_POST['postContent'],
                    'post_type' => 'post',
                    'post_status' => 'pending'
                );
                $post_id = wp_update_post( $post_information );
            
            }
?>


<div action="" id="manif-details" >
 

    
    <div class="double">
        <div style="width: 100%">
            <fieldset>
                 <label for="postVisibility">Autoriser la manifestation [Derniere Modification <i><?php echo the_modified_date()?> </i>]:</label>
                <select name="postVisibility" id="postVisibility">
                    <option value="0" <?php if(!$visible)echo 'selected="selected"';?>>
                        Cacher la Manifestation sur le Site
                    </option>
                    <option value="1" <?php if($visible)echo 'selected="selected"';?>>
                        Afficher la Manifestation sur le Site
                    </option>
                </select>
            </fieldset>
            <fieldset  >
                <label for="postThematique"><?php _e( 'Thematique :', 'wp_manifestation_manage' ); ?></label>
             
                <input name="postThematique" type="text" id="postThematique" value="<?php echo $thematique; ?>" />
            </fieldset>
            <div class="double">
                <div style="width: 49%">
                             
                    <label for="postEmail"><?php _e( 'Email Organisateur :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postEmail" type="email" id="postEmail" value="<?php echo $email; ?>" />
                </div>

                <div style="width: 49%">
                         
                    <label for="postPhone"><?php _e( 'Telephone Organisateur :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postPhone" type="tel" id="postPhone" value="<?php echo $phone; ?>" />
                </div>
         
            </div>
            <div class="double">
                <div style="width: 49%">
                <label for="postCountry"><?php _e( 'Pays :', 'wp_manifestation_manage' ); ?></label>
         
                <input name="postCountry" type="text" id="postCountry" value="<?php echo $pays; ?>" />
                </div>
                <div style="width: 49%">
                    <label for="postTown"><?php _e( 'Ville:', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postTown" type="text" id="postTown" value="<?php echo $ville; ?>" />
                </div>
         
            </div>
            <div class="double">
                <div style="width: 49%">
                    <label for="postDate"><?php _e( 'Date Programmation:', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postDate" type="date" id="postDate" value="<?php echo $date; ?>" />
                </div>

                <div style="width: 49%">
                    <label for="postHour"><?php _e( 'Heure :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postHour" type="time" id="postHour" value="<?php echo $heure; ?>" />
                </div>
         
            </div>
        </div>
    </div>

</div>
<style type="text/css">
    #manif-details div.double {
        display: flex;
        justify-content: space-between;
    }
    #manif-details label {
        display: block;
        font-size: 15px;
        margin: 5px;
    }
    #manif-details input {
        width: 100%;
        height: 45px;
        color: #7d7d7d;
    }
    #manif-details select {
        height: 40px !important;
    }

</style>
<?php


        }
    }
 
?>
 
<?php endwhile; endif; ?>
<?php wp_reset_query(); ?>