# Fiche bien (single-wpis_estates)

La fiche bien (`single-wpis_estates.php`) est **entièrement modulaire** : un **en-tête à
variantes** suivi de **sections réordonnables depuis l'admin**. Tout est réglable sans toucher
au code, et extensible par filtres côté thème enfant.

```
single-wpis_estates.php
├── wpis_render_estate_hero()      → en-tête (variante choisie)
└── wpis_render_estate_sections()  → sections, dans l'ordre réglé
```

Réglages dans **Réglages du thème → Fiches de biens** (page d'options `wpis-theme-estate`).

## En-tête (hero) — `inc/estate-hero.php`

| Variante | Template | Description |
|---|---|---|
| `stacked` *(défaut)* | `template-parts/estate/hero-stacked.php` | Titre, localisation, prix en colonne, puis galerie mosaïque. |
| `split` | `template-parts/estate/hero-split.php` | Grande photo plein cadre (4/3, cliquable → lightbox), titre/prix/badges incrustés, 2–3 vignettes médias superposées (photo / vidéo / visite virtuelle). |

**Fonctions** : `wpis_get_estate_hero_registry()`, `wpis_get_estate_hero_variant()`,
`wpis_render_estate_hero()`. La variante est lue depuis l'option ACF `estate_hero_variant`
(repli : `stacked`).

Les vignettes de la variante `split` sont calculées automatiquement par
`wpis_get_hero_media_slots( $post_id )` (slot 1 = vidéo ou 2ᵉ photo ; slot 2 = visite virtuelle
ou 3ᵉ photo ; slot 3 = 4ᵉ photo sur grand écran). `wpis_media_embed_html( $url, $type )` génère
l'iframe (YouTube / Vimeo / oEmbed / visite 360°).

**Étendre (thème enfant)** :

```php
// Ajouter une variante d'en-tête
add_filter( 'wpis_estate_hero_registry', function ( $variants ) {
    $variants['immersive'] = array(
        'label'    => 'Immersif',
        'template' => 'template-parts/estate/hero-immersive', // dans le thème enfant
    );
    return $variants;
} );

// Forcer une variante quel que soit le réglage admin
add_filter( 'wpis_estate_hero_variant', fn() => 'split' );

// Personnaliser les vignettes médias
add_filter( 'wpis_hero_media_slots', function ( $slots, $post_id ) { /* … */ return $slots; }, 10, 2 );
```

## Sections — `inc/estate-sections.php`

Registre canonique (ordre par défaut) :

| Clé | Label | Template |
|---|---|---|
| `overview`  | Présentation & caractéristiques | `template-parts/estate/details.php` |
| `amenities` | Équipements & commodités | `template-parts/estate/amenities.php` |
| `areas`     | Surfaces & pièces | `template-parts/estate/areas.php` |
| `energy`    | Énergie & PEB | `template-parts/estate/energy.php` |
| `finance`   | Conditions financières | `template-parts/estate/finance.php` |
| `lifestyle` | Art de vivre & quartier | `template-parts/estate/local-lifestyle.php` |

**Fonctions** : `wpis_get_estate_section_registry()`, `wpis_get_estate_sections()` (résout
l'ordre/visibilité depuis le répéteur ACF `estate_sections`), `wpis_render_estate_sections()`.

Dans l'admin, le répéteur **« Ordre des sections »** est triable par glisser-déposer, chaque
ligne possède un interrupteur `enabled`. Au premier accès, `wpis_acf_seed_estate_sections()`
pré-remplit toutes les sections du registre, activées.

**Étendre (thème enfant)** :

```php
// Ajouter / retirer une section du registre
add_filter( 'wpis_estate_section_registry', function ( $registry ) {
    $registry['video'] = array(
        'label'    => 'Vidéo de présentation',
        'template' => 'template-parts/estate/video', // dans le thème enfant
    );
    unset( $registry['finance'] ); // exemple : masquer les finances
    return $registry;
} );

// Modifier l'ordre / la visibilité finals (après lecture des réglages)
add_filter( 'wpis_estate_sections', function ( $sections ) { /* … */ return $sections; } );

// Court-circuiter le rendu d'une section précise
add_filter( 'wpis_render_estate_section', function ( $render, $key ) {
    if ( 'energy' === $key && /* condition */ false ) { return false; }
    return $render;
}, 10, 2 );
```

## Composants `template-parts/estate/`

| Fichier | Rôle |
|---|---|
| `hero-split.php` | En-tête grande photo + vignettes médias superposées. |
| `hero-stacked.php` | En-tête titre/prix en colonne + galerie (défaut). |
| `hero-links.php` | Accès secondaires (vidéo, visite virtuelle, modèle 3D, rendez-vous). |
| `gallery.php` | Mosaïque photos (1 grande + 4 vignettes si ≥ 5) avec overlay « +N ». |
| `gallery-modal.php` | Lightbox plein écran (clavier + gestes tactiles). |
| `details.php` | Description longue, caractéristiques clés, infos techniques. |
| `amenities.php` | Liste équipements / commodités (generic + custom fusionnés). |
| `areas.php` | Détail de toutes les surfaces. |
| `energy.php` | PEB/EPC : badge officiel ou échelle CSS de repli, classe + valeur, chauffage. |
| `finance.php` | Prix, disponibilité, charges, rentes, cadastre, précompte, parkings. |
| `local-lifestyle.php` | Proximités (transports, écoles, commerces… avec distances). |
| `contact-agent.php` | Carte agent (photo, contact, agence) — colonne latérale sticky. |
| `similar.php` | Biens similaires (même ville/catégorie). |
| `card.php` | Carte de bien réutilisée par les listings et « biens d'exception ». |

## PEB / EPC — `inc/epc.php`

Affiche le certificat énergétique avec le **visuel officiel par région**.

- **Région** : `wpis_epc_region()` → `wallonia` *(défaut)* | `flanders` | `brussels`.
  Surcharge via constante **`WPIS_EPC_REGION`** ou filtre **`wpis_epc_region`**.
- **Visuels** : Wallonie = SVG par classe (`wp-immo-sync/dist/img/peb/wal/peb_*.svg`).
  Flandre/Bruxelles : repli sur une échelle CSS dessinée par `energy.php`.
- **Normalisation** : `wpis_epc_normalize( $label )` ramène une saisie libre (« ap », « C+ »…)
  au token canonique (`a++`, `a+`, `a` … `g`) ; `wpis_epc_label_display()` le formate en
  majuscules. Le `+` réglementaire n'est valable que pour A.
- **Rendu** : `wpis_epc_badge( $label, $classes )` renvoie un `<img>` échappé (ratio réservé
  pour éviter le CLS) ; `wpis_epc_image_url( $label, $region )` donne l'URL ou `''`.
- **Filtres** : `wpis_epc_region`, `wpis_epc_image_patterns`, `wpis_epc_source`.

## Données du bien

Tous les composants lisent les données via les **accesseurs** de `inc/immosync-fields.php`,
jamais via `get_post_meta()` brut — voir **[[Architecture]]**. Principaux :
`wpis_get_price()`, `wpis_is_price_hidden()`, `wpis_get_location()`, `wpis_get_estate_features()`,
`wpis_get_area_breakdown()`, `wpis_get_amenities()`, `wpis_get_proximities()`, `wpis_get_gallery()`,
`wpis_get_links()`, `wpis_get_energy()`, `wpis_get_agent()`, `wpis_get_agency()`,
`wpis_get_finance_details()`, `wpis_is_sold()`.
