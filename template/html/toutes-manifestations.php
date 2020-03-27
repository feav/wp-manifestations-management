<?php
// WP_Query arguments
$args = array(
	'post_type'              => array( 'wp_manifestation' ),
	'nopaging'               => true,
	'orderby' => 'publish_date', 
    'order' => 'ASC',
    'post_status'            => array( 'publish','future'),    
    'meta_query'        => array(
            array(
                'key'       => "postVisibility",
                'value'     => 1
            )
        ),
);
/* localhost */
$id_edit_page = 249;
$list_page = 242;
$id_created_page = 249;

/* online */
$id_edit_page = 3312;
$list_page = 3307;
$id_created_page = 3310;
$list_all = 3303;
$list_all_passed = 7164;

$passed_first = $list_all_passed==get_the_ID();
function get_excerpt( $count, $id ) {
	$permalink = get_permalink($id);
	$excerpt = get_the_content();
	$excerpt = strip_tags($excerpt);
	$excerpt = substr($excerpt, 0, $count);
	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
	$excerpt = '<p>'.$excerpt.'... <a href="'.$permalink.'">Lire plus</a></p>';
	return $excerpt;
}
function is_selectable($id , $passed_first){
    $thematique = get_post_meta($id, 'postThematique', true);
    $pays = get_post_meta($id, 'postCountry', true);
    $ville = get_post_meta($id, 'postTown', true);
    $date = get_post_meta($id, 'postDate', true);
    if(isset($_GET['dateDebut']) && $_GET['dateDebut']){
        $debut = $_GET['dateDebut'];
        $datetime1 = new DateTime($debut.' '.'00:00:00');
        $datetime2 = new DateTime($date.' '.'00:00:00');
        if($datetime1 > $datetime2){
            return false;
        }
    }else{
        if(!$passed_first){
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($date.' '.'00:00:00');
            if($datetime1 > $datetime2){
                return false;
            }
        }
    }
    if(isset($_GET['dateFin']) && $_GET['dateFin']){
        $fin = $_GET['dateFin'];
        $datetime1 = new DateTime($fin.' '.'00:00:00');
        $datetime2 = new DateTime($date.' '.'00:00:00');
        if($datetime1 < $datetime2)
            return false;
    }else{ 
        if($passed_first){
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($date.' '.'00:00:00');
            if($datetime1 < $datetime2)
                return false;
        }
    }

    if(isset($_GET['thematique']) && $_GET['thematique']){
        $theme = $_GET['thematique'];
        if(strpos( strtolower($thematique) ,  strtolower($theme)  ) == false)
            return false;
    }


    if(isset($_GET['pays'])  && $_GET['pays']){
        $pays_ = $_GET['pays'];
        if(strpos( strtolower($pays) ,  strtolower($pays_)  ) === false)
            return false;
    }

    if(isset($_GET['ville'])  && $_GET['ville']){
        $ville_ = $_GET['ville'];
        if(strpos( strtolower($ville) ,  strtolower($ville_)  ) === false )
            return false;
    }


    return true;
}
$page=1;
if(isset($_GET['pagen']))
    $page = $_GET['pagen'];
$num_of_current_item = 0;
$num_per_page = 10; 
function is_printable(){
    $num_per_page = 10;
    global $page;
    if(isset($_GET['pagen']))
    $page = $_GET['pagen'];
    global  $num_of_current_item;
    $num_of_current_item++;
    $min  = $num_per_page*($page - 1);
    $max  = $min + $num_per_page;
    if($num_of_current_item >= $min && $num_of_current_item <= $max)
        return true;
    return false;
}
       
// function filter_by_popularity($query)
// {
//         if ($query->is_main_query()) {
//                 $query->set('meta_key', "postDate");
//                 $query->set('orderby', 'postDate DESC');
//         }
    
// }
// add_action('pre_get_posts', 'filter_by_popularity');
// The Query
$query_manifestations = new WP_Query( $args );
$how_many = 0;

?>

<form action="#" method="get" class="filter-menu">
<div style="display:flex;justify-content: space-around;" class="filter-menu">
    <div>
        <label>Date</label>
        <input type="date" name="dateDebut" placeholder="Debut de la periode"
        <?php 
            if(isset($_GET['dateDebut']))
                echo "value="."'".$_GET['dateDebut']."'";
            else if(!$passed_first)
                echo "value="."'".date('Y-m-d')."'";
        ?>
        >
        <input type="date" name="dateFin" placeholder="Fin de la periode"
        <?php 
            if(isset($_GET['dateFin']))
                echo "value="."'".$_GET['dateFin']."'";
            else if($passed_first)
                echo "value="."'".date('Y-m-d')."'";
        ?>
        >
    </div>
    <div>
        <label>Pays</label>
        <input type="text" name="pays" placeholder="Pays ou a lieu la manifestation"
        <?php 
            if(isset($_GET['pays']))
                echo "value="."'".$_GET['pays']."'";
        ?>
        >
    </div>
    <div>
        <label>Ville</label>
        <input type="text" name="ville" placeholder="Ville ou a lieu la manifestation"
        <?php 
            if(isset($_GET['ville']))
                echo "value="."'".$_GET['ville']."'";
        ?>
        >
    </div>
    <div>
        <label>Thematique</label>

        <?php $themes = explode(',', "Rassemblement,Salon – Exposition,Raid – Rallye – Circuit – Courses,Ventes aux enchères,Bourses – Ventes,Divers");?>
                <select name="thematique" <?php echo $dissabled ?> id="thematique">
                    <?php 
                    
                        echo "<option value=''>Pas de Filtre</option>";
                    foreach ($themes as $key => $value) {
                        $selected = '';
                        if(isset($_GET['thematique']) && $value === $_GET['thematique'])
                            $selected = "selected='selected'";
                       echo "<option value='".$value."' ".$selected .">".$value."</option>";
                    }

                    ?>
                </select>


    </div>
    <div>
        <button class="btn" type="submit"><i class="fas fa-search" style="font-size: 20px;margin-top: 35px;"></i></button>
    </div>
</div>
<?php
// The Loop

if ( $query_manifestations->have_posts() ) {
	?>
	<div>
	<?php
	while ( $query_manifestations->have_posts() ) {
		$query_manifestations->the_post();
        if(is_selectable(get_the_ID(),$passed_first)){
        $how_many++;

        if(is_printable( )){
		?>
		<div class="card-block filter-menu">
			<!-- Card -->
			<div class="card">

			  <!-- Card image -->
			  <div class="view overlay relative-posy">
			    <img class="card-img-top" src="<?php echo get_the_post_thumbnail_url()?>" alt="Card image cap">
			    <a>
			      <div class="mask rgba-white-slight"></div>
			    </a>
			  </div>

			  <div class="content">
                   <!-- Title -->
                <h4 class="card-title" style="margin: 0;"><?php the_title().' - '.get_the_ID(); ?></h4>
                <span style="font-size: 17px;font-weight: 200;"><?php echo get_post_meta(get_the_ID(), 'postCountry', true) ?>, <?php echo get_post_meta(get_the_ID(), 'postTown', true) ?> </span><br>
                <span style="font-size: 15px;font-weight: 600;color: #083c4c;"><?php echo get_post_meta(get_the_ID(), 'postDate', true) ?>   <?php echo get_post_meta(get_the_ID(), 'postHour', true) ?> </span>
                <hr>
                <!-- Text -->
                <div class="card-text">
                    <?php echo get_excerpt( 250, get_the_ID() ); ?>
                </div>
                <a  style="padding: 10px;background: #3e3228;color:white" href="<?php echo get_permalink(get_the_ID()); ?>">Voir la Manifestation</a>
              </div>


			</div>
			<!-- Card -->
		</div>


		<?php
            }
        }
	}
		?>
	</div>
    <?php $num_pages = ceil($how_many/$num_per_page);
    ?>

    <div style="display: flex;" class="paggination">
        <?php for($key = 1; $key <= $num_pages; $key++) {?>
            <button style="max-width: 25px; margin: 5px;" type="submit" value="<?php echo  $key ?>" name="pagen" class="<?php if($page == $key) echo 'selected' ?>" ><?php echo  $key ?></button>
        <?php } ?>
    </div>
	<?php
} else {
	?>
	Aucune Manifestation trouvee
	<?php
}

// Restore original Post Data
wp_reset_postdata();

?>

</form>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
    .paggination button{
        background: white;
        border: 1px solid black;
        color: black;
    }
    .paggination button.selected{
        background: black;
        border: 1px solid black;
        color: white;
    }
    .card {
        display: flex;    width: 100%;    flex-direction: row;    height: max-content;    justify-content: flex-end;
    }
    .card div.content {
        padding: 10px;
        width: 50%;
        padding-left: 30px;
    }
    .card-img-top{
        transform: scale(1);
        transition: transform .7s cubic-bezier(0.42, 0, 0.15, 0.59);
        height: 100%;
        width: 100%;
    }
    .card-img-top:hover{
        transform: scale(1.15);
    }
    .card .view.overlay {
        overflow: hidden;
        max-width: 450px;
        min-width: 200px;
        width: 50%;
        max-height: 300px;
    }
    .card {
        display: flex;
        margin-top: 20px;
    }
    .card hr {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    @media screen and (max-width: 600px) {
        .card .view.overlay {
            max-height: 190px;
        }
        .card-block > div.card > div, .card-block > div.card > div .view, .card-block > div.card > div img{
            width: 100% !important;
        }
        .card {
            width: 96%;
            min-height: 545px;
        }
        .card-block > div.card {
            display: block;
        }


        .filter-menu{
            flex-direction: column;
        }
        
        .relative-posy{
            position: relative;
        }
    }
	.card-block {
    position: relative;
}


</style>