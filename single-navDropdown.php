<?php disallow_direct_load('single-announcement.php');?>
<?php get_header(); the_post();?>

		<?= $post->toHTML(); ?>

<?php get_footer();?>
