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

// Label PEB nettoyé (les flux agences sont sales : « ap », « C+ »…). On retombe
// sur la valeur brute si la normalisation ne reconnaît rien, pour ne rien perdre.
$wpis_epc_label = wpis_epc_label_display( $wpis_energy['label'] );
$wpis_label_txt = '' !== $wpis_epc_label ? $wpis_epc_label : $wpis_energy['label'];

// Visuel PEB officiel de la région du site (Wallonie par défaut). '' si la
// région n'a pas de visuel par classe (Flandre/Bruxelles : rendu dédié à venir).
$wpis_epc_badge = wpis_epc_badge( $wpis_energy['label'], 'h-auto w-full max-w-[120px]' );

// Repli : échelle PEB dessinée en CSS, utilisée quand aucun visuel officiel
// n'est disponible pour la région/la classe.
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
$wpis_active = '' !== $wpis_epc_label ? $wpis_epc_label[0] : '';
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-energy-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Performance énergétique', 'hello-immosync' ); ?></p>
	<h2 id="wpis-energy-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Énergie & PEB', 'hello-immosync' ); ?></h2>

	<div class="mt-8 grid gap-10 lg:grid-cols-2">
		<?php if ( '' !== $wpis_epc_badge ) : ?>
			<div class="flex items-start">
				<?php echo $wpis_epc_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup déjà échappé dans wpis_epc_badge(). ?>
			</div>
		<?php elseif ( '' !== $wpis_active ) : ?>
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
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_label_txt ); ?></dd>
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
			<?php $wpis_epc_total = wpis_get_field( 'wpis_energy_epcTotal', $wpis_pid, '' ); ?>
			<?php if ( '' !== $wpis_epc_total ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Consommation totale', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_epc_total ); ?> kWh/an</dd>
				</div>
			<?php endif; ?>
			<?php $wpis_gaz_emit = wpis_get_field( 'wpis_energy_gazEmit', $wpis_pid, '' ); ?>
			<?php if ( '' !== $wpis_gaz_emit ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Émissions de CO₂', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_gaz_emit ); ?> kg CO₂/m²·an</dd>
				</div>
			<?php endif; ?>
			<?php $wpis_solar = wpis_get_field( 'wpis_energy_solarElectricity', $wpis_pid, '' ); ?>
			<?php if ( '' !== $wpis_solar ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Panneaux solaires', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( wpis_is_true( 'wpis_energy_solarElectricity', $wpis_pid ) ? __( 'Oui', 'hello-immosync' ) : $wpis_solar ); ?></dd>
				</div>
			<?php endif; ?>
			<?php $wpis_elec = wpis_get_field( 'wpis_energy_electricityConformity', $wpis_pid, '' ); ?>
			<?php if ( '' !== $wpis_elec ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Conformité électrique', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( wpis_is_true( 'wpis_energy_electricityConformity', $wpis_pid ) ? __( 'Conforme', 'hello-immosync' ) : $wpis_elec ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>
	</div>
</section>
