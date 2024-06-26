<?php
/**
 * Template page for ebox in-app help page.
 *
 * @package ebox_Settings_Page_Help
 *
 * @var array<string, array<string, string>> $categories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="wrap ebox-support">
	<div class="logo">
		<img
			src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/support/atfusion-logo.svg' ); ?>"
			alt="ebox"
		>
	</div>

	<div class="hero">
		<h1><?php esc_html_e( 'Support', 'ebox' ); ?></h1>
		<p class="tagline"><?php esc_html_e( 'We\'re here to help you succeed.', 'ebox' ); ?></h2>
	</div>

	<div class="search box">
		<h2><?php esc_html_e( 'Got a question?', 'ebox' ); ?></h2>
		<div class="fields">
			<div class="form-wrapper">
				<form
					method="POST"
					name="search"
					id="search-form"
				>
					<input
						type="text"
						name="keyword"
						placeholder="<?php esc_html_e( 'Search Our Knowledge Base', 'ebox' ); ?>"
					>
					<button
						type="submit"
						class="submit-button"
					>
						<span class="dashicons dashicons-search submit"></span>
					</button>
				</form>
			</div>
		</div>
	</div>

	<div class="answers box">
		<div class="headline-wrapper">
			<div class="headline">
				<h2><?php esc_html_e( 'Find The Answers', 'ebox' ); ?></h2>
				<p class="description">
					<?php
					esc_html_e(
						'We\'ve categorized our documentation 
                    to help your most pressing questions.',
						'ebox'
					)
					?>
				</p>
			</div>
			<div class="buttons">
				<p><?php esc_html_e( 'Can\'t find what you\'re looking for?', 'ebox' ); ?></p>
				<a
					class="button create-ticket"
					href="https://account.ebox.com/?tab=support"
					target="_blank"
					rel="noreferrer noopener"
				>
					<?php esc_html_e( 'Create A Support Ticket', 'ebox' ); ?>
				</a>
			</div>
		</div>

		<div class="grid">
			<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound?>
			<?php foreach ( $categories as $category_id => $category ) : ?>
			<div
				class="item"
				id="item-<?php echo esc_attr( $category['id'] ); ?>"
				data-id="<?php echo esc_attr( $category['id'] ); ?>"
			>
				<div class="label-wrapper">
					<span class="icon">
                        <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
						<img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/support/' . $category['icon'] . '.png' ); ?>" alt="" >
					</span>
					<h3><?php echo esc_html( $category['label'] ); ?></h3>
					<span class="icon icon-external dashicons dashicons-external"></span>
				</div>
				<?php if ( ! empty( $category['description'] ) ) : ?>
					<p class="description"><?php echo esc_html( $category['description'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
