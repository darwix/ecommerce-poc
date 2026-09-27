<?php get_header(); ?>
<main id="content" class="wrap page-wrap">
<?php while (have_posts()) : the_post(); ?>
	<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
	<?php the_excerpt(); ?>
<?php endwhile; ?>
</main>
<?php get_footer();
