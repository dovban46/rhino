<?php
/**
 * Legal page helpers (Privacy Policy, Terms of Use).
 *
 * @package RHINO
 */

/**
 * Site / company name for legal copy.
 *
 * @return string
 */
function rhino_legal_site_name() {
	return get_bloginfo( 'name', 'display' );
}

/**
 * Primary contact email for legal pages.
 *
 * @return string
 */
function rhino_legal_contact_email() {
	$email = '';

	if ( function_exists( 'rhino_footer_contact_text' ) ) {
		$email = trim( (string) rhino_footer_contact_text( 'footer_email' ) );
	}

	if ( '' === $email && function_exists( 'rhino_get_header_field' ) ) {
		$header_email = rhino_get_header_field( 'header_email' );

		if ( is_string( $header_email ) ) {
			$email = trim( $header_email );
		} elseif ( is_array( $header_email ) && ! empty( $header_email['title'] ) ) {
			$email = trim( (string) $header_email['title'] );
		}
	}

	if ( '' === $email ) {
		$email = get_option( 'admin_email', '' );
	}

	return sanitize_email( $email );
}

/**
 * Formatted last updated date for a page.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function rhino_legal_last_updated( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	return get_the_modified_date( get_option( 'date_format' ), $post_id );
}

/**
 * Allowed HTML for legal default content.
 *
 * @return array
 */
function rhino_legal_allowed_html() {
	return array(
		'h2'     => array( 'id' => true ),
		'h3'     => array(),
		'p'      => array(),
		'ul'     => array(),
		'ol'     => array(),
		'li'     => array(),
		'strong' => array(),
		'em'     => array(),
		'a'      => array(
			'href'   => true,
			'rel'    => true,
			'target' => true,
		),
		'br'     => array(),
	);
}

/**
 * Default Privacy Policy HTML.
 *
 * @return string
 */
function rhino_legal_default_privacy_policy_html() {
	$name  = rhino_legal_site_name();
	$site  = esc_url( home_url( '/' ) );
	$email = rhino_legal_contact_email();
	$mail  = $email ? sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) ) : esc_html__( 'the contact email listed on our website', 'rhino' );

	$html = sprintf(
		/* translators: %s: site / company name */
		'<p>' . esc_html__( 'This Privacy Policy describes how %s ("we," "us," or "our") collects, uses, and protects information when you visit %s or contact us about our remodeling and home improvement services.', 'rhino' ) . '</p>',
		esc_html( $name ),
		'<a href="' . $site . '">' . esc_html( wp_parse_url( $site, PHP_URL_HOST ) ?: $site ) . '</a>'
	);

	$html .= '<h2 id="information-we-collect">' . esc_html__( 'Information We Collect', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'We may collect information that you voluntarily provide, including:', 'rhino' ) . '</p>';
	$html .= '<ul>';
	$html .= '<li>' . esc_html__( 'Name, phone number, email address, and property address', 'rhino' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Project details, photos, and messages submitted through contact or estimate forms', 'rhino' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Reviews or testimonials you choose to share', 'rhino' ) . '</li>';
	$html .= '</ul>';
	$html .= '<p>' . esc_html__( 'We may also automatically collect limited technical data such as IP address, browser type, device information, and pages visited to improve site performance and security.', 'rhino' ) . '</p>';

	$html .= '<h2 id="how-we-use-information">' . esc_html__( 'How We Use Your Information', 'rhino' ) . '</h2>';
	$html .= '<ul>';
	$html .= '<li>' . esc_html__( 'Respond to inquiries and provide estimates or service information', 'rhino' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Schedule consultations and deliver remodeling services you request', 'rhino' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Improve our website, marketing, and customer experience', 'rhino' ) . '</li>';
	$html .= '<li>' . esc_html__( 'Comply with legal obligations and protect against fraud or abuse', 'rhino' ) . '</li>';
	$html .= '</ul>';

	$html .= '<h2 id="sharing">' . esc_html__( 'How We Share Information', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'We do not sell your personal information. We may share data with trusted service providers (such as hosting, analytics, email, or scheduling tools) only as needed to operate our business, and only under appropriate confidentiality obligations.', 'rhino' ) . '</p>';

	$html .= '<h2 id="cookies">' . esc_html__( 'Cookies & Analytics', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Our website may use cookies and similar technologies to remember preferences and understand how visitors use the site. You can adjust cookie settings in your browser; disabling cookies may affect some features.', 'rhino' ) . '</p>';

	$html .= '<h2 id="security">' . esc_html__( 'Data Security', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'We use reasonable administrative, technical, and physical safeguards to protect your information. No method of transmission over the Internet is completely secure, and we cannot guarantee absolute security.', 'rhino' ) . '</p>';

	$html .= '<h2 id="your-rights">' . esc_html__( 'Your Choices & Rights', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Depending on your location, you may have rights to access, correct, delete, or restrict certain uses of your personal information. To make a request, contact us using the details below.', 'rhino' ) . '</p>';

	$html .= '<h2 id="children">' . esc_html__( "Children's Privacy", 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Our services are directed to adults. We do not knowingly collect personal information from children under 13.', 'rhino' ) . '</p>';

	$html .= '<h2 id="changes">' . esc_html__( 'Changes to This Policy', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'We may update this Privacy Policy from time to time. The "Last updated" date at the top of this page reflects the latest revision. Continued use of the site after changes constitutes acceptance of the updated policy.', 'rhino' ) . '</p>';

	$html .= '<h2 id="contact">' . esc_html__( 'Contact Us', 'rhino' ) . '</h2>';
	$html .= '<p>' . sprintf(
		/* translators: 1: company name, 2: email link or fallback text */
		esc_html__( 'If you have questions about this Privacy Policy or our data practices, contact %1$s at %2$s.', 'rhino' ),
		esc_html( $name ),
		$mail
	) . '</p>';

	return $html;
}

/**
 * Default Terms of Use HTML.
 *
 * @return string
 */
function rhino_legal_default_terms_of_use_html() {
	$name  = rhino_legal_site_name();
	$site  = esc_url( home_url( '/' ) );
	$email = rhino_legal_contact_email();
	$mail  = $email ? sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) ) : esc_html__( 'the contact email listed on our website', 'rhino' );

	$html = sprintf(
		/* translators: %s: site / company name */
		'<p>' . esc_html__( 'These Terms of Use govern your access to and use of the website operated by %s. By using this site, you agree to these terms. If you do not agree, please do not use the website.', 'rhino' ) . '</p>',
		esc_html( $name )
	);

	$html .= '<h2 id="use-of-site">' . esc_html__( 'Use of the Website', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'You may use this website only for lawful purposes and in a manner that does not infringe the rights of others or restrict their use of the site. You agree not to attempt unauthorized access, introduce malware, scrape content without permission, or misuse forms or contact tools.', 'rhino' ) . '</p>';

	$html .= '<h2 id="services">' . esc_html__( 'Services, Estimates & Content', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Information on this site—including project photos, descriptions, pricing references, and timelines—is provided for general informational purposes. It does not constitute a binding offer. Final scope, pricing, and schedules are confirmed only in a written agreement between you and our team.', 'rhino' ) . '</p>';

	$html .= '<h2 id="intellectual-property">' . esc_html__( 'Intellectual Property', 'rhino' ) . '</h2>';
	$html .= '<p>' . sprintf(
		/* translators: %s: company name */
		esc_html__( 'All content on %s—including text, graphics, logos, photographs, and layout—is owned by us or our licensors and protected by applicable copyright and trademark laws. You may not copy, reproduce, or distribute site content without prior written permission.', 'rhino' ),
		'<a href="' . $site . '">' . esc_html( $name ) . '</a>'
	) . '</p>';

	$html .= '<h2 id="user-submissions">' . esc_html__( 'Submissions & Reviews', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'If you submit a message, review, image, or other material through our forms, you grant us a non-exclusive right to use that content to respond to you, deliver services, and promote our work (for example, on the website or in marketing), unless we agree otherwise in writing.', 'rhino' ) . '</p>';

	$html .= '<h2 id="third-party">' . esc_html__( 'Third-Party Links', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'Our site may link to third-party websites or tools. We are not responsible for their content, policies, or practices. Your use of third-party sites is at your own risk.', 'rhino' ) . '</p>';

	$html .= '<h2 id="disclaimer">' . esc_html__( 'Disclaimer', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'The website and its content are provided "as is" without warranties of any kind, express or implied, including merchantability, fitness for a particular purpose, or non-infringement.', 'rhino' ) . '</p>';

	$html .= '<h2 id="liability">' . esc_html__( 'Limitation of Liability', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'To the fullest extent permitted by law, we shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the website, even if we have been advised of the possibility of such damages.', 'rhino' ) . '</p>';

	$html .= '<h2 id="indemnity">' . esc_html__( 'Indemnification', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'You agree to indemnify and hold us harmless from claims, damages, and expenses arising from your violation of these Terms or misuse of the website.', 'rhino' ) . '</p>';

	$html .= '<h2 id="governing-law">' . esc_html__( 'Governing Law', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'These Terms are governed by the laws of the state in which we primarily operate, without regard to conflict-of-law principles. Any dispute shall be brought in the courts located in that jurisdiction, unless otherwise required by applicable law.', 'rhino' ) . '</p>';

	$html .= '<h2 id="changes">' . esc_html__( 'Changes to These Terms', 'rhino' ) . '</h2>';
	$html .= '<p>' . esc_html__( 'We may revise these Terms of Use at any time by posting an updated version on this page. Your continued use of the website after changes become effective constitutes acceptance of the revised terms.', 'rhino' ) . '</p>';

	$html .= '<h2 id="contact">' . esc_html__( 'Contact', 'rhino' ) . '</h2>';
	$html .= '<p>' . sprintf(
		/* translators: 1: company name, 2: email link or fallback text */
		esc_html__( 'Questions about these Terms of Use may be directed to %1$s at %2$s.', 'rhino' ),
		esc_html( $name ),
		$mail
	) . '</p>';

	return $html;
}

/**
 * Default legal HTML by document type.
 *
 * @param string $type privacy|terms.
 * @return string
 */
function rhino_legal_default_content_html( $type ) {
	if ( 'terms' === $type ) {
		return rhino_legal_default_terms_of_use_html();
	}

	return rhino_legal_default_privacy_policy_html();
}

/**
 * Render legal page body content (editor content or defaults).
 *
 * @param string $type privacy|terms.
 */
function rhino_legal_page_content( $type ) {
	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		echo wp_kses( rhino_legal_default_content_html( $type ), rhino_legal_allowed_html() );
		return;
	}

	$raw = $post->post_content;
	$raw = is_string( $raw ) ? trim( $raw ) : '';

	if ( '' !== $raw ) {
		the_content();
		return;
	}

	echo wp_kses( rhino_legal_default_content_html( $type ), rhino_legal_allowed_html() );
}

/**
 * Render shared legal page layout.
 *
 * @param array $args {
 *     @type string $type          privacy|terms.
 *     @type string $eyebrow       Small label above title.
 *     @type string $default_title Fallback H1 if page has no title.
 * }
 */
function rhino_render_legal_page( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'type'          => 'privacy',
			'eyebrow'       => '',
			'default_title' => '',
		)
	);

	$type          = 'terms' === $args['type'] ? 'terms' : 'privacy';
	$modifier      = 'privacy' === $type ? 'privacy' : 'terms';
	$default_title = $args['default_title'] ?: ( 'terms' === $type ? __( 'Terms of Use', 'rhino' ) : __( 'Privacy Policy', 'rhino' ) );
	$eyebrow       = $args['eyebrow'] ?: ( 'terms' === $type ? __( 'Legal', 'rhino' ) : __( 'Legal', 'rhino' ) );

	if ( ! have_posts() ) {
		return;
	}

	while ( have_posts() ) :
		the_post();

		$page_title = get_the_title();
		if ( ! is_string( $page_title ) || '' === trim( $page_title ) ) {
			$page_title = $default_title;
		}

		$updated = rhino_legal_last_updated();
		?>
		<main id="primary" class="legal-page legal-page--<?php echo esc_attr( $modifier ); ?>">
			<div class="legal-page__inner">
				<header class="legal-page__header">
					<?php if ( $eyebrow ) : ?>
						<div class="legal-page__eyebrow">
							<span class="legal-page__eyebrow-line" aria-hidden="true"></span>
							<span class="legal-page__eyebrow-text"><?php echo esc_html( $eyebrow ); ?></span>
						</div>
					<?php endif; ?>

					<h1 class="legal-page__title"><?php echo esc_html( $page_title ); ?></h1>

					<?php if ( $updated ) : ?>
						<p class="legal-page__updated">
							<?php
							printf(
								/* translators: %s: formatted date */
								esc_html__( 'Last updated: %s', 'rhino' ),
								esc_html( $updated )
							);
							?>
						</p>
					<?php endif; ?>
				</header>

				<div class="legal-page__content">
					<?php rhino_legal_page_content( $type ); ?>
				</div>
			</div>
		</main>
		<?php
	endwhile;
}

/**
 * Body classes for legal page templates.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function rhino_legal_page_body_class( $classes ) {
	if ( is_page_template( 'page-privacy-policy.php' ) ) {
		$classes[] = 'rhino-legal-page';
		$classes[] = 'rhino-legal-page--privacy';
	}

	if ( is_page_template( 'page-terms-of-use.php' ) ) {
		$classes[] = 'rhino-legal-page';
		$classes[] = 'rhino-legal-page--terms';
	}

	return $classes;
}
add_filter( 'body_class', 'rhino_legal_page_body_class' );
