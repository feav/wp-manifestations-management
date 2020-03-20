<?php

// WP_Query arguments
$args = array(
	'post_type'              => array( 'wp_manifestation' ),
	'author'                 => get_current_user_id(),
	'nopaging'               => true,
	'order'                  => 'ASC',
	'orderby'                => 'modified',
);
/* localhost */
$id_edit_page = 249;
$list_page = 242;
$id_created_page = 249;

/* online */
// $id_edit_page = 6812;
// $list_page = 6806;
// $id_created_page = 6810;

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

			  <!-- Button -->
              <!-- <a href="<?php echo  esc_url(get_post_permalink())?>" class="btn-floating btn-action ml-auto mr-4 mdb-color lighten-3"><i
                  class="fa fa-chevron-right pl-1"></i></a> -->

              <a href="<?php echo esc_url( get_page_link( $id_edit_page ) ); ?>?post=<?php echo get_the_ID(); ?>" class="btn-floating btn-action ml-auto mr-4 mdb-color lighten-3"><i
                  class="fa fa-edit" style="margin: 3px;"></i></a>

			  <!-- Card content -->
			  <div class="card-body">

			    <!-- Title -->
			    <h4 class="card-title"><?php the_title(); ?></h4>
			    <hr>
			    <!-- Text -->
			    <div class="card-text">
			    	<?php echo get_excerpt( 100, get_the_ID() ); ?>
			    </div>

			  </div>

			  <!-- Card footer -->
			  <div class="rounded-bottom mdb-color lighten-3 text-center pt-3">
			    <ul class="list-unstyled list-inline font-small">
			      <li class="list-inline-item pr-2 white-text"><i class="fa fa-clock pr-1"></i><?php echo get_the_date(); ?></li>
			      <!-- <li class="list-inline-item pr-2"><a href="#" class="white-text"><i
			            class="fa fa-comments pr-1"></i>12</a></li> -->
			      <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="fa fa-facebook pr-1">
			          </i><?php echo get_post_meta(get_the_ID(), 'postHour', true) ?></a></li>
			      <!-- <li class="list-inline-item"><a href="#" class="white-text"><i class="fa fa-twitter pr-1"> </i>5</a></li> -->
			    </ul>
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
	.mdb-color.lighten-3 {
	    background-color: #333333 !important;
	}
	.mdb-color {
	    background-color: #45526e !important;
	}
	.text-center {
	    text-align: center !important;
	}
	.pt-3, .py-3 {
	    padding-top: 1rem !important;
	}
	.rounded-bottom {
	    border-bottom-right-radius: .25rem !important;
	    border-bottom-left-radius: .25rem !important;
	}
	.card {
	    font-weight: 400;
	    border: 0;
	    -webkit-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
	    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);
	}
	.card {
	    position: relative;
	    display: -ms-flexbox;
	    display: flex;
	    -ms-flex-direction: column;
	    flex-direction: column;
	    min-width: 0;
	    word-wrap: break-word;
	    background-color: #fff;
	    background-clip: border-box;
	    border: 1px solid rgba(0,0,0,0.125);
	    border-radius: .25rem;
	    max-width: 360px;
        height: max-content;
        max-height: max-content;
	}
	.view img, .view video {
	    position: relative;
	    display: block;
	}
	.card-img, .card-img-top {
	    border-top-left-radius: calc(0.25rem - 1px);
	    border-top-right-radius: calc(0.25rem - 1px);
	}
	.card-img, .card-img-top, .card-img-bottom {
	    -ms-flex-negative: 0;
	    flex-shrink: 0;
	    width: 100%;
	}
	img {
	    vertical-align: middle;
	    border-style: none;
	}
	*, *::before, *::after {
	    box-sizing: border-box;
	}
	.view {
	    position: relative;
	    overflow: hidden;
	    cursor: default;
        min-height: 200px;
	}
	a:not([href]):not([tabindex]), a:not([href]):not([tabindex]):focus, a:not([href]):not([tabindex]):hover {
	    color: inherit;
	    text-decoration: none;
	}

	.card .btn-action {
	    margin-top: -1.44rem;
	    margin-bottom: -1.44rem;
        padding: 13px;
        color: white;
	}
	.mdb-color.lighten-3 {
	    background-color: #333333 !important;
	}
	a.waves-effect, a.waves-light {
	    display: inline-block;
	}
	a:not([href]) {
	    color: inherit;
	    text-decoration: none;
	}
	.btn-floating {
	    position: relative;
	    z-index: 1;
	    display: inline-block;
	    padding: 0;
	    margin: 10px;
	    overflow: hidden;
	    vertical-align: middle;
	    cursor: pointer;
	    border-radius: 50%;
	    -webkit-box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18), 0 4px 15px 0 rgba(0,0,0,0.15);
	    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18), 0 4px 15px 0 rgba(0,0,0,0.15);
	    -webkit-transition: all .2s ease-in-out;
	    transition: all .2s ease-in-out;
	    width: 47px;
	    height: 47px;
	}
	.waves-effect {
	    position: relative;
	    overflow: hidden;
	    cursor: pointer;
	    -webkit-user-select: none;
	    -moz-user-select: none;
	    -ms-user-select: none;
	    user-select: none;
	    -webkit-tap-highlight-color: transparent;
	}
	.mdb-color {
	    background-color: #45526e !important;
	}
	.ml-auto, .mx-auto {
	    margin-left: auto !important;
	}
	.mr-4, .mx-4 {
	    margin-right: 1.5rem !important;
	}
	.btn-floating i {
	    display: inline-block;
	    width: inherit;
	    color: #fff;
	    text-align: center;
	}
	.btn-floating i {
	    font-size: 1.25rem;
	    line-height: 47px;
	}
	.fa, .fas {
	    font-weight: 900;
	}
	.card-body {
	    padding-top: 1.5rem;
	    padding-bottom: 0;
	    border-radius: 0 !important;
	}
	.card .card-body h1, .card .card-body h2, .card .card-body h3, .card .card-body h4, .card .card-body h5, .card .card-body h6 {
	    font-weight: 400;
	}
	.card-title {
	    margin-bottom: .75rem;
	}
	h1, h2, h3, h4, h5, h6 {
	    font-weight: 300;
	}
	h4, .h4 {
	    font-size: 1.5rem;
	}
	h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
	    margin-bottom: .5rem;
	    font-weight: 500;
	    line-height: 1.2;
	}
	h1, h2, h3, h4, h5, h6 {
	    margin-top: 0;
	    margin-bottom: .5rem;
	}
	*, *::before, *::after {
	    box-sizing: border-box;
	}
	hr {    
		margin-top: .1rem;
    	margin-bottom: .1rem;
	    border: 0;
	    border-top: 1px solid rgba(0,0,0,0.1);
	}
	hr {
	    box-sizing: content-box;
	    height: 0;
	    overflow: visible;
	}
	.card .card-body .card-text {
	    font-size: .9rem;
	    font-weight: 400;
	    color: #747373;
	}
	.card-text:last-child {
	    margin-bottom: 0;
	}
		
	.font-small {
	    font-size: .9rem;
	}
	.list-inline {
	    padding-left: 0;
	    list-style: none;
	}
	.list-unstyled {
	    padding-left: 0;
	    list-style: none;
	}
	ol, ul, dl {
	    margin-top: 0;
	    margin-bottom: 1rem;
	}
	.card-title {
	    margin-bottom: .75rem;
	    padding: 10px;
	}
	.card .card-body .card-text {
	    font-size: .9rem;
	    font-weight: 400;
	    color: #747373;
	    padding: 10px;
	}
	.list-unstyled {
	    padding-left: 0;
	    list-style: none;
	    display: flex;
	    justify-content: space-around;
	}
	li.list-inline-item a, li.list-inline-item {
	    color: white !important;
	    font-weight: 700;
	}
	.fa, .fas {
	    margin: 2px;
	}
	.card-block {
	    display: inline-block;
	    margin: 10px;
	}

</style>