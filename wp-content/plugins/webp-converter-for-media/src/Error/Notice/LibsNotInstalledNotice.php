<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class LibsNotInstalledNotice implements NoticeInterface {

	const ERROR_KEY = 'libs_not_installed';

	/**
	 * {@inheritdoc}
	 */
	public function get_key(): string {
		return self::ERROR_KEY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_message(): array {
		$message = [
			sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				__( 'GD or Imagick library is not installed on your server.', 'webp-converter-for-media' ) . ' ' . __( 'This means that you cannot convert images to the WebP format on your server, because it does not meet the plugin requirements described in %1$sthe plugin FAQ%2$s. This issue is not dependent on the plugin.', 'webp-converter-for-media' ),
				'<a href="https://url.mattplugins.com/converter-error-libs-not-installed-faq" target="_blank">',
				'</a>'
			),
		];

		if ( function_exists( 'curl_init' ) ) {
			$message[] = sprintf(
			/* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: open anchor tag, %4$s: close anchor tag */
				__( '%1$sHowever, we have a solution for you!%2$s You can activate %3$sthe PRO version%4$s of the plugin that allows you to convert images using a remote server. This will allow you to convert images without any problems and speed up your website now.', 'webp-converter-for-media' ),
				'<strong class="webpcContent__tip">',
				'</strong>',
				'<a href="https://url.mattplugins.com/converter-error-libs-not-installed-upgrade" target="_blank">',
				'</a>'
			);
		}

		return $message;
	}
}
