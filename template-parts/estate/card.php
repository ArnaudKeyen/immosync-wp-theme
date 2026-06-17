<?php
/**
 * Carte de bien (listing, biens mis en avant, similaires).
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['post_id' => int].
 */

defined("ABSPATH") || exit();

$wpis_pid = !empty($args["post_id"]) ? (int) $args["post_id"] : get_the_ID();
$wpis_sold = wpis_is_sold($wpis_pid);
$wpis_link = get_permalink($wpis_pid);
?>
<article class="wpis-card group">
	<a href="<?php echo esc_url(
     $wpis_link,
 ); ?>" class="wpis-card-media block" aria-label="<?php echo esc_attr(
    wpis_get_title($wpis_pid),
); ?>">
		<?php if (has_post_thumbnail($wpis_pid)): ?>
			<?php echo get_the_post_thumbnail($wpis_pid, "wpis-card", [
       "class" => "wpis-card-img" . ($wpis_sold ? " opacity-90" : ""),
       "loading" => "lazy",
   ]); ?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      else: ?>
			<span class="flex h-full w-full items-center justify-center text-mist"><?php echo wpis_icon(
       "location",
       "w-8 h-8",
   );
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      ?></span>
		<?php endif; ?>

		<div class="absolute left-4 top-4 flex flex-wrap gap-2">
			<?php echo wpis_estate_badges($wpis_pid);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
		</div>

		<?php if ($wpis_sold): ?>
			<div class="absolute inset-0 bg-ink/10"></div>
		<?php endif; ?>
	</a>

	<div class="flex flex-1 flex-col px-5 py-5">
		<p class="wpis-eyebrow mb-2 flex items-center gap-2 text-stone">
			<?php
   $wpis_cat = wpis_get_category($wpis_pid);
   $wpis_loc = wpis_get_location($wpis_pid);
   echo esc_html(implode("  ·  ", array_filter([$wpis_cat, $wpis_loc])));
   ?>
		</p>

		<h3 class="font-display text-2xl leading-snug text-ink">
			<a href="<?php echo esc_url(
       $wpis_link,
   ); ?>" class="transition-colors hover:text-brand"><?php echo esc_html(
    wpis_get_title($wpis_pid),
); ?></a>
		</h3>

		<?php $wpis_excerpt = wpis_get_excerpt($wpis_pid, 18); ?>
		<?php if ("" !== $wpis_excerpt): ?>
			<p class="mt-2 text-sm leading-relaxed text-stone"><?php echo esc_html(
       $wpis_excerpt,
   ); ?></p>
		<?php endif; ?>

		<?php $wpis_features = wpis_get_estate_features($wpis_pid, true); ?>
		<?php if ($wpis_features): ?>
			<ul class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-line pt-4 text-sm text-charcoal">
				<?php foreach ($wpis_features as $wpis_feature): ?>
					<li class="flex items-center gap-1.5">
						<span class="text-brand"><?php echo wpis_icon($wpis_feature["icon"], "w-4 h-4");
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?></span>
						<span><?php echo esc_html($wpis_feature["value"]); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="mt-5 flex items-center justify-between">
			<p class="font-display text-xl text-ink"><?php echo esc_html(
       wpis_get_price($wpis_pid),
   ); ?></p>
			<a href="<?php echo esc_url($wpis_link); ?>" class="wpis-btn-ghost text-xs">
				<?php esc_html_e("Découvrir", "hello-immosync"); ?>
				<?php echo wpis_icon("arrow", "w-4 h-4");
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
			</a>
		</div>
	</div>
</article>
