<?php
/**
 * The template part for displaying the main logo on header
 *
 * @package Sober
 */

$logo_type = sober_get_option( 'logo_type' );

if ( 'text' == $logo_type ) :
	$logo       = sober_get_option( 'logo_text' );
else:
	$logo       = sober_get_option( 'logo' );
	$logo_light = sober_get_option( 'logo_light' );

	if ( ! $logo && ! $logo_light ) {
		$logo       = $logo ? $logo : get_theme_file_uri( '/images/logo.svg' );
		$logo_light = $logo_light ? $logo_light : get_theme_file_uri( '/images/logo-light.svg' );
	} elseif ( ! $logo_light && $logo ) {
		$logo_light = $logo;
	} elseif ( ! $logo && $logo_light ) {
		$logo = $logo_light;
	}

	$logo_width  = sober_get_option( 'logo_width' );
	$logo_width  = $logo_width ? ' width="' . esc_attr( $logo_width ) . '"' : '';
	$logo_height = sober_get_option( 'logo_height' );
	$logo_height = $logo_height ? ' height="' . esc_attr( $logo_height ) . '"' : '';
endif;
?>

<a href="<?php echo esc_url( home_url() ) ?>" class="logo">
	<?php if ( 'text' == $logo_type ) : ?>
		<span class="logo-text"><?php echo esc_html( $logo ) ?></span>
	<?php else : ?>
		<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" class="logo-dark" <?php echo $logo_width . $logo_height ?>>
		<img src="<?php echo esc_url( $logo_light ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>" class="logo-light" <?php echo $logo_width . $logo_height ?>>
	<?php endif; ?>
</a>

<?php if ( is_front_page() && is_home() ) : ?>
	<h1 class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</h1>
<?php else : ?>
	<p class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
	</p>
<?php endif; ?>

<?php if ( ( $description = get_bloginfo( 'description', 'display' ) ) || is_customize_preview() ) : ?>
	<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
<?php endif; ?>