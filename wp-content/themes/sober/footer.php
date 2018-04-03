<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Sober
 */
?>
		<?php do_action( 'sober_after_content' ); ?>
	</div><!-- #content -->

	<?php do_action( 'sober_before_footer' ) ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<?php do_action( 'sober_footer' ) ?>
	</footer><!-- #colophon -->

	<?php do_action( 'sober_after_footer' ) ?>

</div><!-- #page -->

<?php do_action( 'sober_after_site' ) ?>

<?php wp_footer(); ?>

</body>
</html>
