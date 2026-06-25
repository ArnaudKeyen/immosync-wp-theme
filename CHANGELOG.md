# Changelog

Toutes les évolutions notables de **Hello ImmoSync** (thème parent) sont consignées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le thème respecte
le [versionnage sémantique](https://semver.org/lang/fr/) :

| Changement | Incrément |
|---|---|
| Correctif rétro-compatible | PATCH |
| Fonctionnalité rétro-compatible | MINOR |
| Rupture pour les thèmes enfants | MAJOR |

## [Non publié]

## [0.2.0] — 2026-06-21

Premier gros palier fonctionnel après l'amorçage : page d'accueil, fiche bien modulable,
système de contenu ACF et données structurées.

### Ajouté

- **Fiche bien modulable** (`inc/estate-sections.php`) : registre de sections réordonnables
  depuis l'admin (présentation, équipements, surfaces, énergie, finances, art de vivre).
  Filtres `wpis_estate_section_registry`, `wpis_estate_sections`, `wpis_render_estate_section`.
- **Variantes d'en-tête de fiche** (`inc/estate-hero.php`) : `stacked` (défaut) et `split`
  (grande photo plein cadre + vignettes médias superposées, lightbox, vidéo/visite virtuelle).
  Filtres `wpis_estate_hero_registry`, `wpis_estate_hero_variant`, `wpis_hero_media_slots`.
- **Galerie + lightbox** (`template-parts/estate/gallery.php`, `gallery-modal.php`) : mosaïque
  responsive, overlay « +N », navigation clavier et gestes tactiles.
- **PEB/EPC** (`inc/epc.php`) : visuels officiels par région (Wallonie par défaut), normalisation
  des classes énergétiques, repli en échelle CSS. Filtres `wpis_epc_region`,
  `wpis_epc_image_patterns`, `wpis_epc_source` ; constante `WPIS_EPC_REGION`.
- **Système de contenu ACF** (`inc/acf-content.php`) : menu **Réglages du thème**
  (`wpis-theme-settings`), réglages généraux (contact, réseaux, footer, image de repli),
  hero de page, sections de la page d'accueil. Helpers `wpis_page_field()`, `wpis_home_field()`,
  `wpis_theme_option()`.
- **Accesseurs de champs WPIS** (`inc/immosync-fields.php`) : couche unique d'accès aux données
  des biens (prix, localisation, caractéristiques, surfaces, équipements, proximités, galerie,
  liens, énergie, agent/agence, finances) — les templates ne lisent plus de `post_meta` brut.
- **Page d'accueil** (`front-page.php` + `template-parts/home/`) : hero, find-your-place,
  art de vivre, off-market, l'agence, CTA vendeur, biens d'exception automatiques.
- **Hero de page transverse** (`template-parts/global/page-hero.php`) : image mise en avant +
  titre/sur-titre ACF, avec replis gracieux.
- **Template Services** (`template-services.php`) : hiérarchie de pages enfants en grille.
- **Template Estimation** (`template-estimation.php`) : formulaire WPIS + arguments + étapes.
- **Blog** (`home.php`) : hero de la page « Blog » + liste d'articles.
- **Mentions légales IPI/BIV** (Belgique) : sous-footer conditionnel (visible seulement si le
  numéro IPI est renseigné).
- **JSON-LD `Residence`** enrichi sur les fiches biens (`inc/structured-data.php`), désactivable
  via *Réglages du thème* si un plugin SEO gère déjà le schéma.

### Modifié

- `footer.php` / `header.php` : intégration du menu Réglages du thème (coordonnées, réseaux,
  IPI) et harmonisation.
- `template-parts/estate/card.php` : badges (opération / vendu), caractéristiques compactes.
- Documentation : wiki (`docs/wiki/`) actualisé — nouvelle page **Fiche bien**, sommaire et
  architecture mis à jour ; README synchronisé.
- **Copie éditoriale neutralisée** dans les `template-parts/` par défaut (accueil :
  find-your-place, art de vivre, à propos ; fiche bien : quartier & environs) : le parent
  fournit des libellés neutres et factuels, la copie propre à une agence vivant dans le thème
  enfant (surcharge de template-parts) ou dans les champs ACF.

### Supprimé

- `template-parts/home/hero.php` (code mort) : la page d'accueil utilise `global/page-hero.php`.

### Corrigé

- Hero `split` : image en couverture plein cadre et vignettes superposées correctement
  positionnées (responsive, 3ᵉ vignette sur grand écran).

## [0.1.0] — 2026-06-16

### Ajouté

- Amorçage du thème parent **Hello ImmoSync** : structure parent/enfant, `theme.json`,
  build TailwindCSS (CSS compilé versionné).
- Listing des biens (`archive-wpis_estates.php`) et recherche / filtres **server-side**
  (`inc/search.php`, `WP_Query`).
- Fiche bien initiale (`single-wpis_estates.php`) et composants de base.
- Helpers de formatage (`inc/helpers.php`) et tailles d'images (`wpis-card`, `wpis-hero`,
  `wpis-gallery`).
- Point d'extension polices via le filtre `wpis_fonts_url`.

[Non publié]: https://github.com/ArnaudKeyen/immosync-wp-theme/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/ArnaudKeyen/immosync-wp-theme/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/ArnaudKeyen/immosync-wp-theme/releases/tag/v0.1.0
