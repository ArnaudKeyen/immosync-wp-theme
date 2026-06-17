<?php
/**
 * PEB / EPC — visuels officiels par région.
 *
 * Chaque région belge impose sa propre représentation réglementaire du certificat :
 *  - Wallonie : étiquette SVG par classe (A++ … G).
 *  - Flandre / Bruxelles : jauge linéaire (fond + curseur), logique d'affichage
 *    différente — à brancher via un rendu dédié le moment venu.
 *
 * Les visuels sont fournis par le plugin wp-immo-sync (dist/img/peb/), source
 * unique partagée par toutes ses régions. Le site cible une seule région (ici la
 * Wallonie) : on centralise le choix de région et la résolution du visuel pour
 * pouvoir basculer sans toucher aux templates.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Région PEB du site. Détermine le jeu de visuels et les règles applicables.
 *
 * Valeur par défaut : « wallonia ». Surchargeable de deux façons :
 *   - constante (wp-config.php ou functions.php enfant) :
 *       define( 'WPIS_EPC_REGION', 'flanders' );
 *   - filtre :
 *       add_filter( 'wpis_epc_region', fn() => 'flanders' );
 *
 * @return string Identifiant de région ('wallonia' | 'flanders' | 'brussels').
 */
function wpis_epc_region() {
	$region = defined( 'WPIS_EPC_REGION' ) ? WPIS_EPC_REGION : 'wallonia';
	return (string) apply_filters( 'wpis_epc_region', $region );
}

/**
 * Normalise un label PEB saisi de façon hétérogène vers un token canonique.
 *
 * Les flux agences sont sales : on rencontre « ap », « Ap », « AP », « a+ »,
 * « A+ » pour A+, « app » pour A++, ou encore « C+ » / « D+ » qui n'existent pas.
 * On ramène tout vers : a++ | a+ | a | b | c | d | e | f | g (ou '' si illisible).
 *
 * Règles :
 *  - insensible à la casse, espaces et séparateurs ignorés ;
 *  - « plus » et « p » en suffixe de classe valent « + » (aplus, ap → a+) ;
 *  - le « + » n'est réglementaire que pour la classe A ; ailleurs il est rejeté
 *    (C+ → c, D+ → d) ;
 *  - toute saisie hors A–G renvoie '' (repli sur l'échelle générique).
 *
 * @param string $label Label brut tel que stocké.
 * @return string Token canonique, ou '' si non reconnu.
 */
function wpis_epc_normalize( $label ) {
	// Minuscule, sans espaces ni séparateurs courants.
	$value = preg_replace( '/[\s._\/\-]/', '', strtolower( (string) $label ) );

	if ( '' === $value ) {
		return '';
	}

	// Notations alternatives du « + » : « plus » (aplus, aplusplus) puis « p » (ap, app).
	$value = str_replace( 'plus', '+', $value );
	$value = preg_replace_callback(
		'/^([a-g])(p+)$/',
		static function ( $matches ) {
			return $matches[1] . str_repeat( '+', strlen( $matches[2] ) );
		},
		$value
	);

	// Forme attendue : une lettre A–G suivie éventuellement de « + ».
	if ( ! preg_match( '/^([a-g])(\+*)$/', $value, $matches ) ) {
		return '';
	}

	$letter = $matches[1];

	// Le « + » n'existe réglementairement que pour la classe A (A+, A++).
	// Ailleurs (C+, D+…) c'est une saisie erronée : on garde la classe de base.
	if ( 'a' !== $letter ) {
		return $letter;
	}

	$plus = strlen( $matches[2] );
	if ( $plus >= 2 ) {
		return 'a++';
	}

	return 1 === $plus ? 'a+' : 'a';
}

/**
 * Label PEB canonique en majuscules, prêt pour l'affichage (A++, A+, A … G).
 *
 * @param string $label Label brut.
 * @return string Label normalisé en majuscules, ou '' si non reconnu.
 */
function wpis_epc_label_display( $label ) {
	$token = wpis_epc_normalize( $label );
	return '' === $token ? '' : strtoupper( $token );
}

/**
 * Source des visuels PEB (plugin wp-immo-sync). Filtrable si le dossier change.
 *
 * @return array{dir:string,url:string} Chemin disque et URL de base du plugin.
 */
function wpis_epc_source() {
	return apply_filters(
		'wpis_epc_source',
		array(
			'dir' => WP_PLUGIN_DIR . '/wp-immo-sync',
			'url' => plugins_url( '', 'wp-immo-sync/wp-immo-sync.php' ),
		)
	);
}

/**
 * Gabarits de chemin (relatifs à la source) des visuels PEB par classe, par région.
 *
 * Le « %s » reçoit le token de classe (a++, a+, a, b … g). Une région absente de
 * ce tableau n'a pas de visuel « une image par classe » : Flandre et Bruxelles
 * utilisent une jauge linéaire (fond + curseur) et demandent un rendu propre.
 *
 * @return array<string,string>
 */
function wpis_epc_image_patterns() {
	return apply_filters(
		'wpis_epc_image_patterns',
		array(
			'wallonia' => 'dist/img/peb/wal/peb_%s.svg',
			// 'flanders' => …  (jauge linéaire : rendu dédié, pas un fichier par classe)
			// 'brussels' => …  (idem)
		)
	);
}

/**
 * URL du visuel PEB officiel pour un label donné, selon la région active.
 *
 * @param string      $label  Label PEB brut (ex. « A++ », « ap », « C+ »).
 * @param string|null $region Région forcée (défaut : région du site).
 * @return string URL absolue du visuel, ou '' si aucun visuel disponible.
 */
function wpis_epc_image_url( $label, $region = null ) {
	$region   = $region ? $region : wpis_epc_region();
	$token    = wpis_epc_normalize( $label );
	$patterns = wpis_epc_image_patterns();

	if ( '' === $token || empty( $patterns[ $region ] ) ) {
		return '';
	}

	$relative = sprintf( $patterns[ $region ], $token );
	$source   = wpis_epc_source();
	$path     = trailingslashit( $source['dir'] ) . $relative;

	if ( ! file_exists( $path ) ) {
		return '';
	}

	return trailingslashit( $source['url'] ) . $relative;
}

/**
 * Markup <img> de l'étiquette PEB officielle (déjà échappé), ou '' si indisponible.
 *
 * Visuel purement graphique : pertinent dans toutes les langues. La taille
 * d'affichage est pilotée par les classes ($classes) — viser ~80–120 px de large.
 *
 * @param string $label   Label PEB brut.
 * @param string $classes Classes CSS appliquées à l'image.
 * @return string Markup <img>, ou '' si aucun visuel disponible.
 */
function wpis_epc_badge( $label, $classes = 'h-auto w-full max-w-[120px]' ) {
	$url = wpis_epc_image_url( $label );

	if ( '' === $url ) {
		return '';
	}

	$display = wpis_epc_label_display( $label );
	$alt     = '' !== $display
		/* translators: %s: classe énergétique PEB. */
		? sprintf( __( 'Étiquette PEB — classe %s', 'hello-immosync' ), $display )
		: __( 'Étiquette PEB', 'hello-immosync' );

	// Ratio intrinsèque des SVG (≈128×38) : sert de réservation anti-CLS.
	return sprintf(
		'<img src="%s" alt="%s" width="340" height="100" loading="lazy" decoding="async" class="%s" />',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $classes )
	);
}
