<?php
/**
 * Carte d'article (index du blog, archives, articles liés). À appeler dans la boucle.
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['heading' => 'h2'|'h3'].
 */

defined( 'ABSPATH' ) || exit;

$wpis_heading = ( ! empty( $args['heading'] ) && 'h3' === $args['heading'] ) ? 'h3' : 'h2';
$wpis_cats    = get_the_category();
?>
<article <?php post_class( 'wpis-post-card group flex flex-col' ); ?>>
	<a href="<?php the_permalink(); ?>" class="mb-5 block aspect-[4/3] overflow-hidden rounded-[var(--radius-card)] bg-surface-alt" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'wpis-card', array( 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105' ) ); ?>
		<?php endif; ?>
	</a>
	<p class="wpis-post-meta">
		<?php if ( $wpis_cats ) : ?>
			<span><?php echo esc_html( $wpis_cats[0]->name ); ?></span>
			<span aria-hidden="true">·</span>
		<?php endif; ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
	</p>
	<<?php echo $wpis_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — h2|h3. ?> class="mt-2 font-display text-2xl leading-snug text-text-strong">
		<a href="<?php the_permalink(); ?>" class="transition-colors hover:text-brand-strong"><?php the_title(); ?></a>
	</<?php echo $wpis_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mt-3 text-sm leading-relaxed text-text-secondary"><?php the_excerpt(); ?></div>
	<a href="<?php the_permalink(); ?>" class="wpis-btn-ghost mt-5 text-xs" aria-hidden="true" tabindex="-1">
		<?php esc_html_e( 'Lire l’article', 'hello-immosync' ); ?>
		<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
</article>
