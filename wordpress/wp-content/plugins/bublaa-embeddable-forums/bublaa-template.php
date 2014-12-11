<?php
/*
Template Name: Bublaa Template
*/
global $bublaa;
$bublaa_options = $bublaa->get_options();
?>

<?php
	get_header();
?>

<div id="bublaa-container">
<?php
	init_bublaa();
?>
</div>

<?php
if($bublaa_options["showFooter"]) {
	get_footer();
}
?>