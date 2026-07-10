<?php
/**
 * Template Name: Blank (No Header/Footer)
 * Description: Completely blank canvas for page builders. No header, footer, or sidebar.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'template-blank' ); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<div id="content" class="site-content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
