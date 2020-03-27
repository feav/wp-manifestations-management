<?php

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
$id = get_the_ID();
?>
<div class="manif-menu-block">
    <a href="<?php echo esc_url( get_page_link( $list_all ) ); ?>" class="manif-menu <?php if($list_all==$id)echo 'active'?>"> <i class="fas fa-list"></i>Toutes Manifestations</a>
    <a href="<?php echo esc_url( get_page_link( $list_all_passed ) ); ?>" class="manif-menu <?php if($list_all_passed==$id)echo 'active'?>"> <i class="fas fa-list"></i>Manifestations Passées</a>
    <a href="<?php echo esc_url( get_page_link( $list_page ) ); ?>"  class="manif-menu <?php if($list_page==$id)echo 'active'?>"> <i class="fas fa-list-ol"></i>Mes Manifestations</a>
    <a href="<?php echo esc_url( get_page_link( $id_created_page ) ); ?>"  class="manif-menu <?php if($id_created_page==$id)echo 'active'?>"> <i class="fas fa-plus"></i>Ajouter une  Manifestations</a>
    
</div>
<style type="text/css">
    .manif-menu {    
        margin: 4px;
        background: #000000;
        padding: 10px;
        text-align: center;
        color: #efc716;
    }
    .manif-menu.active {
        background: #efc714;
        box-shadow: 1px 1px 7px 0px #c7c4c4;
    }
    .manif-menu i {
        font-size: 13px;
        color: white;
        margin: 0;
    }
    .manif-menu-block{
        display: flex;justify-content: space-between;margin: 15px;
    }
    .manif-menu, .manif-menu i {
        margin: 4px;
        background: #000;
        padding: 10px;
        text-align: center;
        color: #cc9527 !important;
        font-weight: 800;
        font: 13px;
    }
    @media screen and (max-width: 600px) {

        .manif-menu-block{
            flex-direction: column;
        }
    }
    .manif-menu.active {
        background: #efc714;
        color: black !important;
        box-shadow: 1px 1px 7px 0px #c7c4c4;
    }

    .manif-menu.active i{
        background: #efc714 !important;
        color: black !important;
    }

</style>