# Architecture

## Modèle parent / enfant

```
hello-immosync (parent)        → logique métier, templates, composants, build Tailwind
        ▲ Template:
        │
mon-agence (enfant)            → direction artistique uniquement (tokens, polices)
```

Le **parent** ne contient aucune spécificité client. Toute personnalisation visuelle se fait
dans un **thème enfant**, versionné dans son **propre dépôt** (séparation du générique et du
sur-mesure, cycles de release indépendants). Voir **[[Theme-enfant]]**.

## Amorçage (`functions.php`)

Les includes sont chargés dans un **ordre précis** (chaque couche s'appuie sur la précédente) :

1. `inc/setup.php` — supports, menus, tailles d'images (`wpis-card`, `wpis-hero`, `wpis-gallery`…)
2. `inc/enqueue.php` — CSS Tailwind compilé, polices (filtre `wpis_fonts_url`), JS
3. `inc/helpers.php` — helpers de formatage génériques (`wpis_format_number/area/price/distance`, `wpis_icon`)
4. `inc/immosync-fields.php` — **accesseurs des champs WPIS** (ne jamais lire les `post_meta` bruts)
5. `inc/epc.php` — PEB/EPC : normalisation des classes + visuels officiels par région
6. `inc/template-tags.php` — helpers de rendu (cartes, badges, barre de recherche)
7. `inc/search.php` — recherche / filtres server-side (`WP_Query`)
8. `inc/structured-data.php` — JSON-LD `Residence` (SEO)
9. `inc/acf-content.php` — helpers ACF, page d'options (menu **Réglages du thème**), champs
10. `inc/estate-sections.php` — registre + ordre des sections de la fiche bien
11. `inc/estate-hero.php` — variantes d'en-tête de la fiche bien

> Si ACF est absent, le thème **dégrade gracieusement** : chaque accesseur retombe sur des
> valeurs par défaut traduisibles.

## Templates

| Fichier                      | Rôle |
|------------------------------|------|
| `front-page.php`             | Page d'accueil — sections `template-parts/home/` |
| `archive-wpis_estates.php`   | Listing des biens + recherche |
| `single-wpis_estates.php`    | Fiche bien — en-tête + sections `template-parts/estate/` |
| `template-services.php`      | *Template : Services* — hero + grille des pages enfants |
| `template-estimation.php`    | Page d'estimation (formulaire WPIS + arguments + étapes) |
| `home.php`                   | Blog (hero page « Blog » + liste d'articles) |
| `page.php`                   | Page standard (hero + contenu Gutenberg natif) |
| `index.php`                  | Fallback générique |

`template-parts/` : `estate/` (composants fiche bien — voir **[[Fiche-bien]]**), `home/`
(sections de l'accueil), `global/` (`page-hero`, `search-bar`).

## Points d'extension (thème enfant)

| Besoin | Mécanisme |
|---|---|
| Polices | filtre **`wpis_fonts_url`** |
| Palette / typo / espacements | `theme.json` (réglages globaux WP) |
| Surcharger un composant | copier le fichier `template-parts/` au même chemin relatif |
| Réordonner / ajouter une section de fiche | filtres `wpis_estate_section_registry`, `wpis_estate_sections` |
| Ajouter une variante d'en-tête | filtre `wpis_estate_hero_registry` |
| Région PEB / source des visuels | constante `WPIS_EPC_REGION`, filtres `wpis_epc_region`, `wpis_epc_image_patterns` |
| Réglages ACF supplémentaires | sous-page d'options accrochée à `parent_slug => 'wpis-theme-settings'` |

> **Privilégiez un hook exposé par le parent** à la recopie d'un template entier : la divergence
> est plus difficile à maintenir lors des montées de version du parent.

## Données

Les biens proviennent du CPT **`wpis_estates`** (plugin ImmoSync, meta préfixe `wpis_`). Les
champs sont **toujours** lus via les accesseurs de `inc/immosync-fields.php`
(`wpis_get_price()`, `wpis_get_location()`, `wpis_get_estate_features()`, `wpis_get_gallery()`,
`wpis_get_energy()`, `wpis_get_agent()`…) — **jamais** via `get_post_meta()` brut dans les
templates. Cela isole les templates des évolutions du schéma du plugin.
