<?php
/**
 * Barre de recherche immobilière (formulaire GET vers l'archive des biens).
 *
 * « Opération » en onglets (radios masqués) au-dessus d'une rangée de champs :
 * type, localisation, chambres (minimum), budget max. Le formulaire fonctionne
 * seul (GET classique) ; assets/js/search.js l'enrichit ensuite (facettes :
 * options sans résultat désactivées ; module search_ajax : résultats sans
 * rechargement sur l'archive).
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

if ( ! $wpis_action ) {
	return;
}

// Facettes + recherche instantanée : script chargé seulement là où le formulaire s'affiche.
wpis_enqueue_search_script();

/**
 * Seuils proposés pour le champ « Chambres » (minimum).
 *
 * @param int[] $choices Nombres de chambres.
 */
$wpis_bedroom_choices = apply_filters( 'wpis_search_bedroom_choices', array( 1, 2, 3, 4, 5 ) );
?>
<form role="search" method="get" action="<?php echo esc_url( $wpis_action ); ?>"
	class="wpis-search wpis-searchform wpis-searchform--<?php echo esc_attr( $wpis_variant ); ?> <?php echo esc_attr( ! empty( $args['class'] ) ? $args['class'] : '' ); ?>"
	data-wpis-search>

	<!-- Opération (onglets / radios masqués) -->
	<div class="wpis-searchform-tabs" role="radiogroup" aria-label="<?php esc_attr_e( 'Opération', 'hello-immosync' ); ?>">
		<label class="wpis-searchform-tab">
			<input type="radio" name="wpis_purpose" value="" <?php checked( $wpis_current['purpose'], '' ); ?>>
			<span><?php esc_html_e( 'Toutes', 'hello-immosync' ); ?></span>
		</label>
		<?php foreach ( array_reverse( $wpis_options['purposes'] ) as $wpis_opt ) : ?>
			<label class="wpis-searchform-tab">
				<input type="radio" name="wpis_purpose" value="<?php echo esc_attr( $wpis_opt ); ?>" <?php checked( $wpis_current['purpose'], $wpis_opt ); ?>>
				<span><?php echo esc_html( $wpis_opt ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>

	<!-- Champs -->
	<div class="wpis-searchform-fields">

		<!-- Type de bien -->
		<label class="wpis-searchform-field">
			<span class="wpis-field-label"><?php esc_html_e( 'Type de bien', 'hello-immosync' ); ?></span>
			<select name="wpis_category" class="wpis-searchform-control">
				<option value=""><?php esc_html_e( 'Tous types', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_options['categories'] as $wpis_opt ) : ?>
					<option value="<?php echo esc_attr( $wpis_opt ); ?>" <?php selected( $wpis_current['category'], $wpis_opt ); ?>><?php echo esc_html( $wpis_opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Localisation -->
		<label class="wpis-searchform-field">
			<span class="wpis-field-label"><?php esc_html_e( 'Localisation', 'hello-immosync' ); ?></span>
			<select name="wpis_city" class="wpis-searchform-control">
				<option value=""><?php esc_html_e( 'Toutes les villes', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_options['cities'] as $wpis_opt ) : ?>
					<option value="<?php echo esc_attr( $wpis_opt ); ?>" <?php selected( $wpis_current['city'], $wpis_opt ); ?>><?php echo esc_html( $wpis_opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Chambres (minimum) -->
		<label class="wpis-searchform-field">
			<span class="wpis-field-label"><?php esc_html_e( 'Chambres', 'hello-immosync' ); ?></span>
			<select name="wpis_bedrooms" class="wpis-searchform-control">
				<option value=""><?php esc_html_e( 'Indifférent', 'hello-immosync' ); ?></option>
				<?php foreach ( $wpis_bedroom_choices as $wpis_bed ) : ?>
					<option value="<?php echo esc_attr( $wpis_bed ); ?>" <?php selected( $wpis_current['bedrooms'], (string) $wpis_bed ); ?>>
						<?php
						/* translators: %d : nombre minimum de chambres. */
						printf( esc_html__( '%d+', 'hello-immosync' ), (int) $wpis_bed );
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>

		<!-- Budget max -->
		<label class="wpis-searchform-field">
			<span class="wpis-field-label"><?php esc_html_e( 'Budget max', 'hello-immosync' ); ?></span>
			<input type="number" name="wpis_price_max" min="0" step="10000"
				value="<?php echo esc_attr( $wpis_current['price_max'] ); ?>"
				placeholder="<?php esc_attr_e( 'Sans limite', 'hello-immosync' ); ?>"
				class="wpis-searchform-control">
		</label>

		<!-- Submit -->
		<button type="submit" class="wpis-searchform-submit">
			<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Rechercher', 'hello-immosync' ); ?>
		</button>

	</div>
</form>
