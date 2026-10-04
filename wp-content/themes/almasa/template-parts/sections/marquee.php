<?php
/**
 * Gold marquee band of service names.
 *
 * @package Almasa
 */

defined( 'ABSPATH' ) || exit;

$services = almasa_get_services();
if ( ! $services ) {
	return;
}
$names = wp_list_pluck( $services, 'post_title' );
$names = array_merge( $names, $names );
?>
<div class="almasa-marquee">
	<?php for ( $track = 0; $track < 2; $track++ ) : ?>
		<ul class="almasa-marquee__track"<?php echo $track ? ' aria-hidden="true"' : ''; ?>>
			<?php foreach ( $names as $name ) : ?>
				<li><span class="almasa-marquee__gem" aria-hidden="true"></span><?php echo esc_html( $name ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endfor; ?>
</div>
