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

### Ajouté

- **Deux modules facturables supplémentaires** : `immersive_media` conditionne désormais toutes
  les sorties vidéo / visite 360° ; `map_voyager` transforme la carte OpenStreetMap sans clé en
  carte Leaflet interactive avec le fond clair CARTO Voyager. Quand ils sont coupés, aucun asset
  associé n'est chargé et la carte conserve son iframe OSM légère.

- **Gabarits d'articles** (rubrique Actualité) : `single.php` (hero avec catégorie et date,
  contenu complet `.wpis-prose`, étiquettes, partage si le module `share` est actif, encart
  « Estimation gratuite », « À lire aussi » dans la même catégorie) et `archive.php` (catégories,
  étiquettes…). Jusqu'ici un article retombait sur `index.php`, qui n'affichait que l'extrait sans
  titre H1. Carte d'article commune `template-parts/post/card.php` (catégorie, date, extrait,
  « Lire l'article ») et grille `template-parts/post/grid.php`, utilisées par `home.php`,
  `index.php` et `archive.php`.
- `page-hero` accepte des sur-titre / sous-titre par défaut (`eyebrow`, `subtitle`), utilisés quand
  les champs ACF sont vides.
- **Coordonnées de l'agence** dans *Réglages généraux* : adresse, latitude / longitude, horaires
  (répéteur jours / horaires), accès & stationnement. `wpis_get_agency_info()` (filtre
  `wpis_agency_info`) les normalise ; `template-parts/global/agency-info.php` les affiche (lien
  « Itinéraire », blocs masqués s'ils sont vides) et peut émettre le JSON-LD `RealEstateAgent`
  (`wpis_output_agency_jsonld()`, filtre `wpis_agency_jsonld`). L'adresse apparaît aussi dans le
  pied de page.
- **Carte réutilisable** `template-parts/global/map.php` (iframe OpenStreetMap, sans clé ni
  script), désormais utilisée par la section « Quartier » de la fiche.
- **Partage des annonces** (module `share`, `template-parts/estate/share.php`) : boutons
  WhatsApp, Facebook et « Copier le lien » dans l'en-tête de fiche (les deux variantes ; ton
  `inverse` sur image), plus la feuille de partage native sur mobile (`navigator.share`). Simples
  liens de partage — aucun script tiers ni cookie. Réseaux filtrables (`wpis_share_networks`) ;
  retour « Lien copié » annoncé aux lecteurs d'écran (`WPIS.share`). Icônes `whatsapp`,
  `facebook`, `link`, `share` ajoutées à `wpis_icon()`. Les vignettes de partage s'appuient sur
  les balises Open Graph du plugin SEO (Rank Math).
- **Bouton « Demander une visite » en haut de fiche** (`template-parts/estate/visit-cta.php`),
  à côté du prix dans les deux variantes d'en-tête ; il descend jusqu'au formulaire de visite de la
  colonne contact (ancre `#wpis-contact`). Masqué pour un bien vendu / loué ; libellé filtrable
  (`wpis_estate_visit_label`).
- **Encart « Estimation gratuite »** : nouvelle section `estimate_cta` du registre, placée sous la
  présentation (`template-parts/estate/estimate-cta.php`). Un bouton vers la page Estimation —
  jamais de second formulaire sur la fiche. Textes éditables dans *Réglages du thème → Fiches de
  biens* (`estate_estimate_*`). Nouveau bouton clair `.wpis-btn-light` pour les aplats sombres.
- `wpis_get_estimation_url()` (filtre `wpis_estimation_url`) : résout la page Estimation par son
  gabarit, puis par les slugs `services/estimation` / `estimation`. Utilisé par l'en-tête.
- `wpis_array_insert_after()` : insertion ordonnée dans un tableau associatif.

### Modifié

- **Nouvelles sections de fiche à leur place logique** : une section livrée par une mise à jour et
  absente des réglages enregistrés est insérée après sa voisine de registre (plus à la fin).
- **Colonne contact de la fiche non sticky** : plus haute que l'écran, elle rendait le bas du
  formulaire de visite (dont le bouton d'envoi) inatteignable avant la fin de la page.
- **Recherche à facettes dans le parent** (remontée du thème enfant LMC) : le formulaire
  (`template-parts/global/search-bar.php`) passe en onglets « Opération » + champ « Chambres »
  (seuils filtrables via `wpis_search_bedroom_choices`), classes génériques `.wpis-searchform*`.
  `assets/js/search.js` désactive les options qui ne mènent à aucun bien, à partir du dataset
  `wpis_get_estate_facets()` (transient invalidé à la synchro), et relance la recherche à chaque
  changement sur l'archive. Le script n'est chargé que là où le formulaire est affiché.
- **Recherche instantanée** (module `search_ajax`) : route REST `wpis/v1/estates` qui renvoie le
  bloc de résultats rendu par le même template-part que l'archive
  (`template-parts/estate/results.php`) ; grille, compteur, tri et pagination sont remplacés sans
  rechargement, l'URL suit (`history.pushState`) et le retour arrière restaure filtres et
  résultats. Sans JavaScript ou module coupé, le formulaire GET classique fonctionne à l'identique.
- `wpis_build_estate_query_args()` (filtre `wpis_estate_query_args`), `wpis_sanitize_filters()`,
  `wpis_estates_url()` : construction de la requête et des URLs de recherche partagée entre
  l'archive et la route REST.
- **Modules activables** (`inc/modules.php`) : registre filtrable (`wpis_module_registry`) de
  fonctionnalités optionnelles — `share` (partage des annonces) et `search_ajax` (recherche
  instantanée) — testées via `wpis_module_enabled()`. Activation sur une page *Réglages du thème →
  Modules* réservée à la capacité `wpis_manage_modules` (invisible pour l'administrateur de
  l'agence), ou forcée par la constante `WPIS_MODULES` dans `wp-config.php`.
- **Médias immersifs de la fiche** (`template-parts/estate/media.php`) : nouvelle section
  `media` (« Visite 360° & vidéo ») au registre des sections, cible des ancres `#wpis-visit360` /
  `#wpis-video` du bandeau de hero. Les liens passent par `wpis_get_estate_media()`, qui
  interroge plusieurs clés meta (filtre `wpis_estate_media_meta_keys`) — Whise range la visite
  360° dans `links.model` — et `wpis_normalize_media_url()` écarte les valeurs inexploitables.
  Les cartes affichent des pastilles « 360° » / « vidéo » (`.wpis-media-flags`), et une icône
  `view360` rejoint `wpis_icon()`.
- **Textes de la section « derniers biens » éditables** : sur-titre, titre et libellé du lien
  passent par `wpis_home_field()` (`home_featured_eyebrow`, `home_featured_titre`,
  `home_featured_bouton`), dans un nouvel onglet « Les derniers biens » du groupe de champs de la
  page d'accueil. C'était la dernière accroche de la home encore écrite en dur. Les valeurs par
  défaut sont inchangées (« Sélection » / « Biens d'exception » / « Voir tous les biens ») et la
  liste de biens reste automatique.

### Corrigé

- **Surface habitable = `wpis_areas_total`** : `areas.living` est la surface du **séjour** dans
  Whise (« Living area »), pas la surface habitable. La grille de la fiche et les cartes
  (`wpis_get_estate_features()`) ainsi que `floorSize` du JSON-LD lisent désormais
  `wpis_areas_total` ; dans le détail des surfaces (`wpis_get_area_breakdown()`), `living` est
  libellé « Séjour » et `ground` « Terrain (superficie totale) ». Une villa affichait 33 m²
  « habitables » (son séjour) ; une carte sans séjour encodé n'affichait plus aucune surface.

## [0.5.0] — 2026-07-31

### Ajouté

- **Page d'accueil modulable** (`inc/home-sections.php`) : `front-page.php` n'enchaîne plus des
  `get_template_part()` en dur mais parcourt un registre canonique filtrable
  (`wpis_get_home_section_registry()`, rendu par `wpis_render_home_sections()`). L'ordre et la
  visibilité des sept blocs (hero, biens en vedette, localités, cadre de vie, off-market, agence,
  CTA vendeur) se règlent par glisser-déposer dans **Réglages du thème → Page d'accueil** (page
  d'options `wpis-theme-home`). Même mécanique que les sections de fiche de bien, avec en plus une
  clé `args` transmise au template-part (nécessaire au hero). Un bloc absent des réglages
  enregistrés est ajouté en fin de liste, activé.
- **Trois niveaux de disponibilité** via `wpis_get_status_level()` : `available`, `pending`
  (Option, Sous compromis, Offre en cours, Réservé, Vendu sous conditions) et `sold`. Nouveau
  filtre `wpis_pending_statuses` pour la liste intermédiaire, en pendant du `wpis_sold_statuses`
  existant. Nouvelle variante CSS `.wpis-badge-pending` (pastille claire opaque à filet), qui
  s'intercale entre `.wpis-badge-brand` et `.wpis-badge-sold` — les deux classes existantes sont
  inchangées, donc aucune rupture pour les thèmes enfants.

### Corrigé

- **Le badge de carte affiche désormais le statut, plus le type d'opération.** `wpis_estate_badges()`
  n'affichait `wpis_status_label` que pour un bien vendu, et `wpis_purpose_label` sinon : les statuts
  intermédiaires étaient donc invisibles, un bien « Option » ou « Sous compromis » s'affichant
  « À vendre ». Le badge rend maintenant toujours le statut ; l'opération reste portée par le titre
  normalisé (« Appartement à vendre à Uccle »).
- **« À louer » n'est plus considéré comme loué.** Le terme `loue` de `wpis_sold_statuses` était
  cherché en sous-chaîne et matchait `louer` : toute location disponible sortait avec le badge sombre
  « Vendu/Loué », l'image atténuée et un `availability: SoldOut` en JSON-LD. La comparaison se fait
  désormais sur des mots entiers, après passage en minuscules et suppression des accents.
- **`wpis_get_title()` ne renvoie plus le nom interne du logiciel immo en priorité.** L'ordre est
  maintenant `post_title` (normalisé à chaque synchro par `wpis-post-update.php`) → champ éditorial
  `wpis_description_title` → `wpis_name` en dernier recours. Auparavant `wpis_name` gagnait toujours
  et les cartes affichaient des libellés du type « Demo Duplex ».
- **`availability` JSON-LD sur trois niveaux** (`inc/structured-data.php`) : `SoldOut` pour un bien
  vendu, `LimitedAvailability` pour un bien sous compromis ou en option, `InStock` sinon.

### Supprimé

- **Description courte des cartes de biens** (`template-parts/estate/card.php`) : le paragraphe
  d'extrait n'apportait rien entre le titre et la ligne de caractéristiques. `wpis_get_excerpt()`
  reste disponible et sert toujours aux données structurées.

## [0.4.0] — 2026-07-30

### Ajouté

- **Typographie éditoriale de `.wpis-prose`** : titres, listes, liens, citations, filets et tableaux
  sont désormais stylés. Le preflight Tailwind remet `h1`–`h6` à `font-size: inherit` et retire les
  puces : sans ces règles, toute page rédigée dans l'éditeur (mentions légales, article, page libre)
  sortait en bloc de texte indifférencié. Les styles s'appuient sur les tokens, donc un thème enfant
  les re-skinne sans y toucher.

### Modifié — RUPTURE pour les thèmes enfants

- **Les tokens de couleur sont renommés par fonction, plus par couleur.** Un token doit dire à quoi
  il sert (`--color-text-strong`), pas de quelle teinte il est (`--color-ink`) : la palette d'un
  thème enfant peut alors changer complètement sans que le nom des tokens devienne mensonger.
  Les utilitaires Tailwind correspondants (`bg-*`, `text-*`, `border-*`) suivent le même
  renommage. Table de correspondance :

  | Avant | Après | Rôle |
  |---|---|---|
  | `ink` | `text-strong` | Titres, header, aplats sombres, boutons pleins |
  | `charcoal` | `text` | Texte courant / body |
  | `stone` | `text-secondary` | Texte secondaire |
  | `mist` | `text-muted` | Tertiaire, méta, placeholders |
  | `line` | `border` | Bordures fines |
  | `sand` | `surface-alt` | Fond alterné |
  | `cream` | `surface` | Fond principal (et texte sur fond sombre) |
  | `brand-dark` | `brand-strong` | État survol / actif de la marque |
  | `brand` | `brand` | *(inchangé)* |

  **Migration d'un thème enfant** : renommer les utilitaires dans les surcharges de templates
  (`bg-cream` → `bg-surface`, `text-ink` → `text-text-strong`, `border-line` → `border-border`…),
  les variables CSS (`var(--color-cream)` → `var(--color-surface)`) et les slugs de palette de
  `theme.json`. Aucun changement de valeur hexadécimale : le rendu est identique une fois le
  renommage appliqué.

- **Barre de recherche** (`template-parts/global/search-bar.php`) : structure revue pour être
  surchargeable par un thème enfant (variantes `hero` / `inline`, classes de champ isolées).

## [0.3.0] — 2026-07-13

### Ajouté

- **Écran « Bientôt en ligne » (coming soon)** (`inc/coming-soon.php`) : mode pré-lancement qui
  remplace tout le front par une page d'attente plein écran (image de fond, logo, surtitre, texte,
  contact, bouton CTA) tant que le visiteur n'est pas connecté (`edit_posts`). L'équipe continue de
  voir le vrai site ; les visiteurs reçoivent un `HTTP 503` + `noindex`. **Désactivé par défaut**
  (opt-in) via **Réglages du thème → Bientôt en ligne** (sous-page d'options `wpis-coming-soon`,
  champs ACF `wpis_cs_*`). Filtres exposés : `wpis_coming_soon_active`,
  `wpis_coming_soon_default_background_url`, `wpis_coming_soon_default_logo_url` — un thème enfant
  fournit ses visuels par défaut sans coder de contenu dans le parent.

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

[Non publié]: https://github.com/ArnaudKeyen/immosync-wp-theme/compare/v0.5.0...HEAD
[0.5.0]: https://github.com/ArnaudKeyen/immosync-wp-theme/compare/v0.4.0...v0.5.0
[0.2.0]: https://github.com/ArnaudKeyen/immosync-wp-theme/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/ArnaudKeyen/immosync-wp-theme/releases/tag/v0.1.0
