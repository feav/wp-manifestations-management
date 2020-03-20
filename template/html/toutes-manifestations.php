<?php

// WP_Query arguments
$args = array(
	'post_type'              => array( 'wp_manifestation' ),
	'nopaging'               => true,
	'order'                  => 'ASC',
	'orderby'                => 'modified',
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
$id_edit_page = 6812;
$list_page = 6806;
$id_created_page = 6810;

function get_excerpt( $count, $id ) {
	$permalink = get_permalink($id);
	$excerpt = get_the_content();
	$excerpt = strip_tags($excerpt);
	$excerpt = substr($excerpt, 0, $count);
	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
	$excerpt = '<p>'.$excerpt.'... <a href="'.$permalink.'">Lire plus</a></p>';
	return $excerpt;
}

// The Query
$query_manifestations = new WP_Query( $args );

// The Loop
if ( $query_manifestations->have_posts() ) {
	?>
    <a class="btn primary" href="<?php echo esc_url( get_page_link( $id_created_page ) ); ?>">Ajouter une nouvelle Manifestation</a>
	<div>
	<?php
	while ( $query_manifestations->have_posts() ) {
		$query_manifestations->the_post();
		?>
		<div class="card-block">
			<!-- Card -->
			<div class="card">

			  <!-- Card image -->
			  <div class="view overlay">
			    <img class="card-img-top" src="<?php echo get_the_post_thumbnail_url()?>" alt="Card image cap">
			    <a>
			      <div class="mask rgba-white-slight"></div>
			    </a>
			  </div>

			  <div class="content">
                   <!-- Title -->
                <h4 class="card-title" style="margin: 0;"><?php the_title(); ?></h4>
                <span style="font-size: 17px;font-weight: 200;"><?php echo get_post_meta(get_the_ID(), 'postCountry', true) ?>, <?php echo get_post_meta(get_the_ID(), 'postTown', true) ?> </span><br>
                <span style="font-size: 15px;font-weight: 600;color: #083c4c;"><?php echo get_post_meta(get_the_ID(), 'postDate', true) ?> <?php echo get_post_meta(get_the_ID(), 'postHour', true) ?> </span>
                <hr>
                <!-- Text -->
                <div class="card-text">
                    <?php echo get_excerpt( 250, get_the_ID() ); ?>
                </div>
                <button style="padding: 10px;background: #3e3228;"><a style="color:white" href="<?php echo get_permalink(get_the_ID()); ?>">Voir la Manifestation</a></button>
              </div>


			</div>
			<!-- Card -->
		</div>


		<?php
	}
		?>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
    .card {
        display: flex;    width: 100%;    flex-direction: row;    height: max-content;
    }
    .card div.content {
        padding: 10px;
        width: 50%;
        padding-left: 30px;
    }
    .card-img-top{
        transform: scale(1);
        transition: transform .7s cubic-bezier(0.42, 0, 0.15, 0.59);
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
    }
</style>