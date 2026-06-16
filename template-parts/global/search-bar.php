<?php
/**
 * Barre de recherche immobilière (formulaire GET vers l'archive des biens).
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['variant' => 'hero'|'inline', 'class' => string].
 */

defined( 'ABSPATH' ) || exit;

$wpis_options = wpis_get_filter_options();
$wpis_current = wpis_current_filters();
$wpis_action  = get_post_type_archive_link( 'wpis_estates' );
$wpis_variant = ! empty( $args['variant'] ) ? $args['variant'] : 'inline';
$wpis_is_hero = ( 'hero' === $wpis_variant );

if ( ! $wpis_action ) {
	return;
}

$wpis_wrap_class = $wpis_is_hero
	? 'bg-cream/95 shadow-2xl backdrop-blur'
	: 'bg-white border border-line';
?>
<form role="search" method="get" action="<?php echo esc_url( $wpis_action ); ?>"
	class="wpis-search rounded-[var(--radius-card)] <?php echo esc_attr( $wpis_wrap_class ); ?> <?php echo esc_attr( ! empty( $args['class'] ) ? $args['class'] : '' ); ?>">
	<div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-card)] bg-line sm:grid-cols-2 lg:grid-cols-[1.2fr_1fr_1fr_0.8fr_auto]">

		<!-- Opération -->
		<label class="flex flex-col bg-cream px-5 py-4">
			<span class="wpis-field-label"><?php esc_html_e( 'Opération', 'hello-immosync' ); ?></span>
			<select name="wpis_purpose" class="bg-transparent font-body text-sm text-ink focus:outline-none">
				<option value=""><?php esc_html_e( 'Toutes', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_options['purposes'] as $wpis_opt ) : ?>
					<option value="<?php echo esc_attr( $wpis_opt ); ?>" <?php selected( $wpis_current['purpose'], $wpis_opt ); ?>><?php echo esc_html( $wpis_opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Type de bien -->
		<label class="flex flex-col bg-cream px-5 py-4">
			<span class="wpis-field-label"><?php esc_html_e( 'Type de bien', 'hello-immosync' ); ?></span>
			<select name="wpis_category" class="bg-transparent font-body text-sm text-ink focus:outline-none">
				<option value=""><?php esc_html_e( 'Tous types', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_options['categories'] as $wpis_opt ) : ?>
					<option value="<?php echo esc_attr( $wpis_opt ); ?>" <?php selected( $wpis_current['category'], $wpis_opt ); ?>><?php echo esc_html( $wpis_opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Localisation -->
		<label class="flex flex-col bg-cream px-5 py-4">
			<span class="wpis-field-label"><?php esc_html_e( 'Localisation', 'hello-immosync' ); ?></span>
			<select name="wpis_city" class="bg-transparent font-body text-sm text-ink focus:outline-none">
				<option value=""><?php esc_html_e( 'Toutes les villes', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_options['cities'] as $wpis_opt ) : ?>
					<option value="<?php echo esc_attr( $wpis_opt ); ?>" <?php selected( $wpis_current['city'], $wpis_opt ); ?>><?php echo esc_html( $wpis_opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Budget max -->
		<label class="flex flex-col bg-cream px-5 py-4">
			<span class="wpis-field-label"><?php esc_html_e( 'Budget max', 'hello-immosync' ); ?></span>
			<input type="number" name="wpis_price_max" min="0" step="10000"
				value="<?php echo esc_attr( $wpis_current['price_max'] ); ?>"
				placeholder="<?php esc_attr_e( 'Sans limite', 'hello-immosync' ); ?>"
				class="bg-transparent font-body text-sm text-ink placeholder:text-mist focus:outline-none">
		</label>

		<!-- Submit -->
		<button type="submit" class="flex items-center justify-center gap-2 bg-ink px-7 py-4 font-body text-sm font-medium uppercase tracking-[0.12em] text-cream transition-colors hover:bg-brand-dark">
			<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Rechercher', 'hello-immosync' ); ?>
		</button>

	</div>
</form>
