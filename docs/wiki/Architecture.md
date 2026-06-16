# Architecture

## Modèle parent / enfant

```
hello-immosync (parent)        → logique métier, templates, composants, build Tailwind
        ▲ Template:
        │
lamaisonclaire (enfant)        → direction artistique uniquement (tokens, polices)
```

Le **parent** ne contient aucune spécificité client. Toute personnalisation visuelle se fait
dans un **thème enfant**, versionné dans son **propre dépôt** (séparation du générique et du
sur-mesure, cycles de release indépendants).

## Amorçage (`functions.php`)

Les includes sont chargés dans un ordre précis :

1. `inc/setup.php` — supports, menus, tailles d'images (`wpis-card`, `wpis-hero`, `wpis-gallery`…)
2. `inc/enqueue.php` — CSS Tailwind compilé, polices, JS
3. `inc/helpers.php` — helpers de formatage génériques
4. `inc/immosync-fields.php` — accesseurs des champs WPIS
5. `inc/template-tags.php` — helpers de rendu (cartes, badges, barre de recherche)
6. `inc/search.php` — recherche/filtres server-side (WP_Query)
7. `inc/structured-data.php` — JSON-LD (SEO)

## Templates

| Fichier                      | Rôle |
|------------------------------|------|
| `front-page.php`             | Page d'accueil (sections `template-parts/home/`) |
| `archive-wpis_estates.php`   | Listing des biens + recherche |
| `single-wpis_estates.php`    | Fiche bien (sections `template-parts/estate/`) |
| `index.php`                  | Fallback générique |

## Points d'extension

- Filtre **`wpis_fonts_url`** : permet à un thème enfant de remplacer les polices Google Fonts.
- Les `template-parts/` sont surchargeables par un thème enfant (même chemin relatif).
- `theme.json` définit palette, typographie et espacements (réglages globaux WP).

## Données

Les biens proviennent du CPT **`wpis_estates`** (plugin ImmoSync). Les champs sont lus via les
accesseurs de `inc/immosync-fields.php` — n'accédez jamais aux `post_meta` bruts dans les
templates.
