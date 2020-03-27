<?php 
$args = array(
    'post_type'              => array( 'wp_manifestation' ),
    'author'                 => get_current_user_id(),
    'nopaging'               => true,
    'order'                  => 'ASC',
    'orderby'                => 'modified',
);
add_action( 'init', 'wpse17478_init' );
function wpse17478_init()
{
    remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
}
add_filter( 'get_the_excerpt', 'wpse17478_get_the_excerpt' );
function wpse17478_get_the_excerpt( $excerpt )
{
    if ( '' == $excerpt ) {
        return '';
    }
    return $excerpt;
}

$query = new WP_Query( $args ); 
/* localhost */
$id_edit_page = 249;
$list_page = 242;
$id_created_page = 249;

/* online */
$id_edit_page = 3312;
$list_page = 3307;
$id_created_page = 3310;
$list_all = 3303;

$parent_post_id = 0;
$post_id = 0;
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
$action = 'edit-manifestation';
$create_succes = false;
$passed = false;
$dissabled="";
$excerpt = "";
$postVisibility = 0;
$list_image = array();

if(isset($_GET['post'])){
    $post_id = $_GET['post'];
    $parent_post_id = $_GET['post'];
    $email = get_post_meta($post_id, 'postEmail', true);
    $phone = get_post_meta($post_id, 'postPhone', true);
    $thematique = get_post_meta($post_id, 'postThematique', true);
    $pays = get_post_meta($post_id, 'postCountry', true);
    $ville = get_post_meta($post_id, 'postTown', true);
    $heure = get_post_meta($post_id, 'postHour', true);
    $date = get_post_meta($post_id, 'postDate', true);
    $visible = get_post_meta($post_id, 'postVisibility', true);
    foreach ($list_image as $key => $value) {
        $list_image[$key] = get_post_meta($post_id, '_thumbnail_rapport_id_'.$key, true);
    }

    $datetime1 = new DateTime($date.' '.$heure.':00');
    $datetime2 = new DateTime();
    $passed =  ($datetime1 < $datetime2);
    if($passed){
        $dissabled = "disabled='disabled'";
    }
}
if(isset($_POST['postContent'])){
    $old_post_id = $post_id;
    $post_information = array(
        'ID' => $post_id,
        'post_title' =>  wp_strip_all_tags( $_POST['postTitle'] ),
        'post_content' => $_POST['postContent'],
        'post_date' => $_POST['postDate'].' '.$_POST['postHour'].':00',
        'post_type' => 'wp_manifestation',
        'post_status' => 'publish'
        );
    $post_id = wp_insert_post( $post_information );
    $create_succes = true;

    if($old_post_id==0 && $post_id!=0){
        function wpse27856_set_content_type(){
            return "text/html";
        }
        add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );
        $to =  $_POST['postEmail'];
        $subject = 'Votre Manifestation a ete cree avec success';
        $link_created = esc_url( get_page_link( $id_created_page ) );
        $link_post = esc_url( get_page_link( $post_id ) );
        $link_updated = esc_url( get_page_link( $id_edit_page )."?post=".$post_id );
        $body = '
        <div style="background: #fffbeb;padding: 15px 30px 30px 30px;text-align: center;">
            <h1>Votre Manifestation a été créé sur gOld Time Repair avec succès</h1>
            <br>
            <h2>'.wp_strip_all_tags( $_POST['postTitle'] ).'</h2>

            <p>Voici le lien unique, que vous pouvez partager pour informer votre public des détails de votre manifestation, il suffit de cliquer sur le lien <a href="'.$id_edit_page.'">Ma Manifestation</a></p>
            <p>Vous pouvez modifier votre manifestation a tout moment , ou décider de ne plus la rendre visible sur notre site en cliquant sur ce lien <a href="'.$link_updated.'">Modifier ma Manifestation</a></p>
            <p>Vous pouvez créer autant de Manifestations que vous désirez sur notre plateforme en suivant ce lien <a href="'.$link_created.'">Ajouter une autre Manifestation</a></p>

        </div>
        ';  $body = '
        <div style="background: #fffbeb;padding: 15px 30px 30px 30px;text-align: center;">
            <h1>Votre Manifestation a été créé sur gOld Time Repair avec succès</h1>
            <br>
            <h2>'.wp_strip_all_tags( $_POST['postTitle'] ).'</h2>

            <p>Voitre manifestation est en cours de modération, vous serez notifié lorsque votre manifestation sera en ligne</p>
            <p>Vous pouvez modifier votre manifestation a tout moment , ou décider de ne plus la rendre visible sur notre site en cliquant sur ce lien <a href="'.$link_updated.'">Modifier ma Manifestation</a></p>
            <p>Vous pouvez créer autant de Manifestations que vous désirez sur notre plateforme en suivant ce lien <a href="'.$link_created.'">Ajouter une autre Manifestation</a></p>

        </div>
        ';
        $bodyAdmin = '
        <div style="background: #eaeaea;padding: 15px 30px 30px 30px;text-align: center;">
            <h1>Une Manifestation a été crée</h1>
            <br>
            <h2>'.wp_strip_all_tags( $_POST['postTitle'] ).'</h2>
            <p>Une Manifestation a été ajoutée et est en attente de modération <a href="https://www.goldtimerepair.com/wp-admin/post.php?post='. $post_id .'&action=edit">Modérer la Manifestation</a></p>
        </div>
        ';
        $headers = array('From:Manifestation <contact@goldtimerepair.com >');
        function wpcs_set_email_content_type() {
            return 'text/html';
        }
        add_filter( 'wp_mail_content_type', 'wpcs_set_email_content_type' );

        update_post_meta($post_id, 'postVisibility', 0);
        wp_mail( $to, $subject, $body, $headers );
        wp_mail( "goldtimerepair@gmail.com", "Manifestation en attente de modération", $bodyAdmin, $headers );
        //wp_mail( "feavfeav@gmail.com", "Manifestation en attente de modération", $bodyAdmin, $headers );

        ?>

            <script type="text/javascript">
                document.location.href = "<?php 
                    echo get_page_link( $id_edit_page )."?".
                    http_build_query( 
                        array_merge(
                            $_GET, 
                            array('post' => $post_id,'new_post' => 1) 
                        )
                    
                );?>";
            </script>


        <?php

    }
    update_post_meta($post_id, 'postEmail', $_POST['postEmail']);
    update_post_meta($post_id, 'postPhone', $_POST['postPhone']);
    update_post_meta($post_id, 'postThematique', $_POST['postThematique']);
    update_post_meta($post_id, 'postCountry', $_POST['postCountry']);
    update_post_meta($post_id, 'postTown', $_POST['postTown']);
    update_post_meta($post_id, 'postHour', $_POST['postHour']);
    update_post_meta($post_id, 'postDate', $_POST['postDate']);
    if( $_POST['postVisibility']==-1)
    update_post_meta($post_id, 'postVisibility', $_POST['postVisibility']);

}
$parent_post_id = $post_id;
$list_image = array();
for ($i=0; $i < 20; $i++) { 
    $list_image[]= get_post_meta($post_id, '_thumbnail_rapport_id_'.$i, true); 
    // var_dump(get_post_meta($post_id, '_thumbnail_rapport_id_'.$i, true));
}

if(isset($_FILES['postThumbnail']) && $post_id && $_FILES['postThumbnail']['size']){
    // WordPress environment
    require( WPMM_DIR . '/../../../wp-load.php' );
     
    $wordpress_upload_dir = wp_upload_dir();
    // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
    // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
    $i = 1; // number of tries when the file with the same name is already exists
     
    $profilepicture = $_FILES['postThumbnail'];
    $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];
    $new_file_mime = mime_content_type( $profilepicture['tmp_name'] );
     
    if( empty( $profilepicture ) )
        die( 'File is not selected.' );
     
    if( $profilepicture['error'] )
        die( $profilepicture['error'] );
     
    if( $profilepicture['size'] > wp_max_upload_size() )
        die( 'It is too large than expected.' );
     
    if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
        die( 'WordPress doesn\'t allow this type of uploads.' );
     
    while( file_exists( $new_file_path ) ) {
        $i++;
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $profilepicture['name'];
    }
     
    // looks like everything is OK
    if( move_uploaded_file( $profilepicture['tmp_name'], $new_file_path ) ) {
     
     
        $upload_id = wp_insert_attachment( array(
            'guid'           => $new_file_path, 
            'post_mime_type' => $new_file_mime,
            'post_title'     => preg_replace( '/\.[^.]+$/', '', $profilepicture['name'] ),
            'post_content'   => '',
            'post_status'    => 'attachment',
            'post_parent' => $parent_post_id
        ), $new_file_path );
     
        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
     
        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
     
        update_post_meta(  $parent_post_id, '_thumbnail_id',  $upload_id );
        // Show the uploaded file in browser
        //wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );
     
    }
}

    // WordPress environment
    require( WPMM_DIR . '/../../../wp-load.php' );

    // wp_generate_attachment_metadata() won't work if you do not include this file
    require_once( ABSPATH . 'wp-admin/includes/image.php' );


    foreach ($list_image as $key => $value) {
if(isset($_FILES[ 'postThumbnailRapport'.$key ])  && $post_id){
        
        if(isset($_FILES['postThumbnailRapport'.$key]) && $_FILES['postThumbnailRapport'.$key]['size']){
            $wordpress_upload_dir = wp_upload_dir();
            // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
            // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
            $i = 1; // number of tries when the file with the same name is already exists
             
            $profilepicture = $_FILES['postThumbnailRapport'.$key];
            $new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];

            $new_file_mime = mime_content_type( $profilepicture['tmp_name'] );
             
            if( empty( $profilepicture ) )
                die( 'File is not selected.' );
             
            if( $profilepicture['error'] )
                die( $profilepicture['error'] );
             
            if( $profilepicture['size'] > wp_max_upload_size() )
                die( 'It is too large than expected.' );
             
            if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
                die( 'WordPress doesn\'t allow this type of uploads.' );
             
            while( file_exists( $new_file_path ) ) {
                $i++;
                $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $profilepicture['name'];
            }
             
            // looks like everything is OK
            if( move_uploaded_file( $profilepicture['tmp_name'], $new_file_path ) ) {
             
             
                $upload_id = wp_insert_attachment( array(
                    'guid'           => $new_file_path, 
                    'post_mime_type' => $new_file_mime,
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $profilepicture['name'] ),
                    'post_content'   => '',
                    'post_status'    => 'attachment',
                    'post_parent' => $parent_post_id
                ), $new_file_path );
             
             
                // Generate and save the attachment metas into the database
                wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
             
                update_post_meta(  $parent_post_id, '_thumbnail_rapport_id_'.$key,  $upload_id );
                // Show the uploaded file in browser
                // wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );
             
            }
        }
    } 
}
$list_image = array();
for ($i=0; $i < 20; $i++) { 
    $list_image[]= get_post_meta($post_id, '_thumbnail_rapport_id_'.$i, true); 
    // var_dump(get_post_meta($post_id, '_thumbnail_rapport_id_'.$i, true));
}

if(isset($_POST['postRapport'])){
    $old_post_id = $post_id;
    $post_information = array(
        'ID' => $post_id,
        'post_excerpt' => $_POST['postRapport'],
        'post_date' => $_POST['postDate'].' '.$_POST['postHour'].':00',
        'post_type' => 'wp_manifestation',
        'post_status' => 'publish'
        );
    $post_id = wp_update_post( $post_information,true );

}

 ?> 
<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
 
<?php

   if ( isset( $post_id ) ) {

        $email = get_post_meta($post_id, 'postEmail', true);
        $phone = get_post_meta($post_id, 'postPhone', true);
        $thematique = get_post_meta($post_id, 'postThematique', true);
        $pays = get_post_meta($post_id, 'postCountry', true);
        $ville = get_post_meta($post_id, 'postTown', true);
        $heure = get_post_meta($post_id, 'postHour', true);
        $date = get_post_meta($post_id, 'postDate', true);
        $visible = get_post_meta($post_id, 'postVisibility', true);
        if ( $post_id == get_the_ID()  )
        {
            $current_post = get_the_ID() ;
            $title = get_the_title();
            $content = get_the_content();
            $excerpt = get_the_excerpt();
           
        }
    }
 
 endwhile; endif; ?>
<?php if(isset($_GET['new_post'])){ ?>
    <div style="background: black;color: #cc9528;text-align: center;padding: 20px;margin: 30px;">
        Votre Manifestation a été mise en ligne, Vous pouvez la modifier a tout moment et Ajouter de nouvelles.
        <br>
        Apres la Manifestation vous serez invité a fournir un compte rendu ainsi que quelques photos de la manifestation.
        <br>
        Pensez à vérifier votre boite Spam, si vous ne recevez rien dans les minutes qui suivent.
    </div>
<?php } ?>

<br>

<form  id="primaryPostForm"  method="post"  enctype="multipart/form-data">
    <input name="post_id" id="post_id" type="hidden" value="<?php echo $post_id; ?>">
    <input name="action" id="action" type="hidden" value="<?php echo $action; ?>">
    
    <fieldset>
 
        <label for="postTitle"><?php _e( 'Nom de La Manifestation:', 'wp_manifestation_manage' ); ?></label>
 
        <input type="text" <?php echo $dissabled ?> name="postTitle" id="postTitle" value="<?php if($post_id) echo get_the_title($post_id); ?>" class="required" />
 
    </fieldset> 
 

    
    <div class="double">
        <div style="width: 50%">
     
            <label for="postTitle"><?php _e( 'Image a la une :', 'wp_manifestation_manage' ); ?></label>
            <br>
            <?php if ($parent_post_id) { ?>
            <img src="<?php echo get_the_post_thumbnail_url( $parent_post_id );?>" style="width: 500px;" id="avatar-manifestation">

            <label for="postThumbnail" style="width: max-content;background: #e1ab87;padding: 8px;margin-top: 5px;">Modifier L'image a la une</label>
            <input type="file" <?php echo $dissabled ?> name="postThumbnail" id="postThumbnail" value="" class="required" accept="image/png, image/jpeg" style="opacity: 0" />
            <?php  }else{ ?>

            <label class="img" for="postThumbnail">   
             <br>         
                <img id="avatar-manifestation" src="https://getstamped.co.uk/wp-content/uploads/WebsiteAssets/Placeholder.jpg" style="width: 330px;" >
            </label>
            <input type="file" <?php echo $dissabled ?> name="postThumbnail" id="postThumbnail" value="" class="required" accept="image/png, image/jpeg" style="opacity: 0" />
            <?php  } ?>

     
        </div>
        <div style="width: 50%">
            <fieldset  >
                <label for="postThematique"><?php _e( 'Thematique :', 'wp_manifestation_manage' ); ?></label>
             
                <!-- <input name="postThematique" <?php echo $dissabled ?> type="text" id="postThematique" value="<?php echo $thematique; ?>" /> -->
                <?php $themes = explode(',', "Rassemblement,Salon – Exposition,Raid – Rallye – Circuit – Courses,Ventes aux enchères,Bourses – Ventes,Divers");?>
                <select name="postThematique" <?php echo $dissabled ?> id="postThematique">
                    <?php foreach ($themes as $key => $value) {
                                     $selected='';
                                    if($value === $thematique)
                                        $selected = "selected='selected'";
                       echo "<option value='".$value."' ".$selected .">".$value."</option>";
                    }
                    ?>
                </select>
                <input name="postVisibility" <?php echo $dissabled ?> type="hidden" id="postVisibility" value="<?php echo $postVisibility;?>" />

            </fieldset>
            <?php if($post_id && ($postVisibility > 0 || $postVisibility < 0) ){ ?>
            <div class="double">
                <div style="width: 49%">
                             
                    <label for="postThematique"><?php _e( 'Thematique :', 'wp_manifestation_manage' ); ?></label>
             
                <input name="postThematique" <?php echo $dissabled ?> type="text" id="postThematique" value="<?php echo $thematique; ?>" />
                </div>

                <div style="width: 49%">
                         
                    <label for="postPhone"><?php _e( 'Rendre Visible :', 'wp_manifestation_manage' ); ?></label>
             
                    <select name="postVisibility" <?php echo $dissabled ?> id="postVisibility" >
                        <option value="1">Rendre Visible sur le site</option>
                        <option value="-1">Ne pas afficher sur le site</option>
                    </select>
                </div>
         
            </div>
            <?php } ?>


            <div class="double">
                <div style="width: 49%">
                             
                    <label for="postEmail"><?php _e( 'Email Organisateur :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postEmail" <?php echo $dissabled ?> type="email" id="postEmail" value="<?php echo $email; ?>" />
                </div>

                <div style="width: 49%">
                         
                    <label for="postPhone"><?php _e( 'Telephone Organisateur :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postPhone"  <?php echo $dissabled ?> type="text" id="postPhone" value="<?php echo $phone; ?>" />
                </div>
         
            </div>
            <div class="double">
                <div style="width: 49%">
                <label for="postCountry"><?php _e( 'Pays :', 'wp_manifestation_manage' ); ?></label>
         
                <input name="postCountry"   <?php echo $dissabled ?> type="text" id="postCountry" value="<?php echo $pays; ?>" />
                </div>
                <div style="width: 49%">
                    <label for="postTown"><?php _e( 'Ville:', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postTown" type="text" <?php echo $dissabled ?> id="postTown" value="<?php echo $ville; ?>" />
                </div>
         
            </div>
            <?php if($passed){ ?>
                    <div style="background: black;color: #cc9528;text-align: center;padding: 20px;margin: 30px;">
                        Si la manifestation n'a pas eu lieu, vous avez la possibilite de la reporter a une Date ulterieure.
                    </div>
            <?php } ?>
            <div class="double">
                <div style="width: 49%">
                    <label for="postDate"><?php _e( 'Date Programmation:', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postDate" type="date" id="postDate" value="<?php echo $date; ?>" <?php if(!$passed){ ?>min="<?php echo date("Y-m-d")?>"<?php } ?> />
                </div>

                <div style="width: 49%">
                    <label for="postHour"><?php _e( 'Heure :', 'wp_manifestation_manage' ); ?></label>
             
                    <input name="postHour" type="time"  id="postHour" value="<?php echo $heure; ?>" />
                </div>
         
            </div>
        </div>
    </div>

    <fieldset>
                 
        <label for="postContent"><?php _e( 'Description:', 'wp_manifestation_manage' ); ?></label>
 
        <textarea name="postContent" id="postContent" <?php echo $dissabled ?> rows="8" cols="30"><?php if(isset($_GET['post']))echo get_the_content('lire plus', false, $_GET['post']); ?></textarea>
 
    </fieldset>

    <fieldset>
         
        <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
 
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <?php if(!$passed){ ?>
            <div class="manif-menu-block">
                <button  style="font-size: 15px;" type="submit" accesskey="p" id="publish" name="publish" value="Submit"  class="manif-menu"> <i class="fas fa-edit"></i><?php if($post_id){_e( 'Mettre a jour la manifestation', 'wp_manifestation_manage');}else{_e( 'Ajouter la Manifestation', 'wp_manifestation_manage');} ?></button>
                <a href="<?php echo esc_url( get_page_link( $id_created_page ) ); ?>"  class="manif-menu"> <i class="fas fa-plus"></i> Ajouter une autre Manifestations</a>
                
            </div>
        <?php }else{ ?>
            <div class="manif-menu-block">
                <button  style="font-size: 15px;" type="submit" accesskey="p" id="publish" name="publish" value="Submit"  class="manif-menu"> <i class="fas fa-edit"></i>Reprogrammer la Manifestation</button>
                
            </div>
        <?php } ?>
    </fieldset>
 
</form>
<?php if($passed){ ?>
<div style="background: black;color: #cc9528;text-align: center;padding: 20px;margin: 30px;">
        Vous avez la possibilité d'ajouter 20 photos, ainsi qu'un rapport du déroulement de votre événement.
        <br>
        Ceci nous permettra de savoir comment votre manifestation s'est deroulée, pour mettre les futures manifestations en avant.
        <br>
        Cela permettra aussi a vos Manifestants de se remémorer ces moments.
</div>
<form  id="primaryPostForm"  method="post"  enctype="multipart/form-data">
    <input name="post_id" id="post_id" type="hidden" value="<?php echo $post_id; ?>">
    <input name="action" id="action" type="hidden" value="<?php echo $action; ?>">
    
    <fieldset>
 
        <label for="postRapport"><?php _e( 'Compte Rendu de la Manifestation:', 'wp_manifestation_manage' ); ?></label>
         <textarea name="postRapport" id="postRapport" required="required" rows="8" cols="30">
            <?php echo    get_the_excerpt($post_id); ?>
                
            </textarea>
    </fieldset> 
    <div class="double-many">
        <?php foreach ($list_image as $key => $value) { ?>
                <?php
                $c = wp_get_attachment_image_src( $value,'medium' )[0];

                ?>
            <div style="margin: 5px;" <?php if(!$c)echo 'class=" hide-label"'?>>
                <!-- <label for="postTitle"><?php _e( "Images ".(1+$key), 'wp_manifestation_manage' ); ?></label> -->
                <?php if ($c) {  ?>
                    <label class="img" for="postThumbnail<?php echo $key; ?>">         
                        <img src="<?php echo wp_get_attachment_image_src( $value,'medium' )[0];?>" style="width: 500px;" id="avatar-postThumbnail<?php echo $key; ?>">
                    </label>
                <?php  }else{ ?>

                    <label class="img" for="postThumbnail<?php echo $key; ?>" >         
                        <img id="avatar-postThumbnail<?php echo $key; ?>" src="https://getstamped.co.uk/wp-content/uploads/WebsiteAssets/Placeholder.jpg" style="width: 330px;" >
                    </label>
                <?php  } ?>
                <input type="file" name="postThumbnailRapport<?php echo $key;?>" id="postThumbnail<?php echo $key; ?>" class="rapport-img" accept="image/png, image/jpeg" style="display: none" />
            </div>
        <?php } ?>
         <div style="margin: 5px;">
            <label class="img" onclick='jQuery(jQuery(".double-many > div.hide-label")[0]).removeClass("hide-label")' >   
                <img id="avatar-postThumbnail<?php echo $key; ?>" src="https://www.icone-png.com/png/20/20074.png" style="width: 330px;" >
            </label>
        </div>
    </div>

        <input type="hidden" name="submitted" id="submitted" value="true" />
        <div class="manif-menu-block">
                <button  style="font-size: 15px;" type="submit" accesskey="p" id="publish" name="publish" value="Submit"  class="manif-menu"> <i class="fas fa-edit"></i>Mettre a jour le Rapport</button>
                <a href="<?php echo esc_url( get_page_link( $id_created_page ) ); ?>"  class="manif-menu"> <i class="fas fa-plus"></i> Ajouter une autre  Manifestations</a>
                
            </div>
</form>

<?php } ?>
<style type="text/css">
    div.double {
        display: flex;
        justify-content: space-between;
    }
    .double-many > div {
        display: inline-block;
        width: 15%;
    }
    .double-many {
        display: block;
        position: relative;
    }
</style> 
<script type="text/javascript">

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          jQuery('#avatar-manifestation').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }

    jQuery("#postThumbnail").change(function() {
      readURL(this);
    });

      
    <?php foreach ($list_image as $key => $value) { ?>
    jQuery("#postThumbnail<?php echo $key; ?>").change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();        
            reader.onload = function(e) {
              jQuery('#avatar-postThumbnail<?php echo $key; ?>').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    <?php } ?>

</script>
<style type="text/css">
    
        input[type="text"], input[type="time"], input[type="date"], textarea {
            border: 1px solid #bcbcbc;
            width: 100%;
            font-size: 16px;
            padding: 10px 10px;
            margin: 0 0 23px 0;
            height: auto;
        }
        input:disabled, input[readonly], textarea:disabled, textarea[readonly] {
            background-color: #dcdcdc !important;
            cursor: not-allowed;
        }
        .double-many > div.hide-label{
            display: none;
        }
    @media screen and (max-width: 600px) {
        div.double {
            display: block;
        }
        .card,.card-block {
            width: 100%;
        }
        div.double > div {
            width: 99% !important;
        }
        div.double > div label.img img ,div.double > div label.img ,div.double > div img  {
            width: 100% !important;
        }
    }
</style>

