<?php
/**
 * Bloc énergie / PEB de la fiche bien.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid    = get_the_ID();
$wpis_energy = wpis_get_energy( $wpis_pid );

if ( '' === $wpis_energy['label'] && '' === $wpis_energy['value'] && '' === $wpis_energy['heating'] ) {
	return;
}

// Échelle PEB et couleurs associées.
$wpis_scale  = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G' );
$wpis_colors = array(
	'A' => '#1f8a3b',
	'B' => '#5aa72a',
	'C' => '#a7c20f',
	'D' => '#f2d40c',
	'E' => '#f3a712',
	'F' => '#e8631a',
	'G' => '#d4262a',
);
$wpis_active = strtoupper( substr( (string) $wpis_energy['label'], 0, 1 ) );
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-energy-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Performance énergétique', 'hello-immosync' ); ?></p>
	<h2 id="wpis-energy-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Énergie & PEB', 'hello-immosync' ); ?></h2>

	<div class="mt-8 grid gap-10 lg:grid-cols-2">
		<?php if ( '' !== $wpis_active ) : ?>
			<div>
				<div class="flex flex-col gap-1.5">
					<?php foreach ( $wpis_scale as $wpis_letter ) : ?>
						<?php $wpis_is_active = ( $wpis_letter === $wpis_active ); ?>
						<div class="flex items-center gap-3">
							<span class="flex h-8 items-center rounded-[var(--radius-card)] px-4 font-body text-sm font-semibold text-white transition-all"
								style="background-color: <?php echo esc_attr( trim( $wpis_colors[ $wpis_letter ] ) ); ?>; width: <?php echo esc_attr( ( 40 + array_search( $wpis_letter, $wpis_scale, true ) * 8 ) ); ?>%;">
								<?php echo esc_html( $wpis_letter ); ?>
							</span>
							<?php if ( $wpis_is_active ) : ?>
								<span class="font-body text-sm font-medium text-ink">
									&#9664; <?php echo esc_html( $wpis_energy['label'] ); ?>
									<?php if ( '' !== $wpis_energy['value'] ) : ?>
										<span class="text-stone">(<?php echo esc_html( $wpis_energy['value'] ); ?> kWh/m²·an)</span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<dl class="space-y-1">
			<?php if ( '' !== $wpis_energy['label'] ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Classe énergétique', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_energy['label'] ); ?></dd>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $wpis_energy['value'] ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Consommation', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_energy['value'] ); ?> kWh/m²·an</dd>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $wpis_energy['heating'] ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Type de chauffage', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_energy['heating'] ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>
	</div>
</section>
