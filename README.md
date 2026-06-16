# Hello ImmoSync

Thème **parent** WordPress pour agences immobilières propulsées par le plugin **ImmoSync (WPIS)**.
Il fournit le listing des biens, la fiche bien immersive, la recherche server-side et une
bibliothèque de composants premium en **TailwindCSS**. Il est conçu pour être étendu par un
**thème enfant** qui porte la direction artistique (couleurs, typographie) sans dupliquer la
logique métier.

> Exemple de thème enfant : **La Maison Claire** (`lamaisonclaire`), versionné dans un dépôt
> séparé.

- **Dépôt** : https://github.com/ArnaudKeyen/immosync-wp-theme
- **Version** : 0.1.0
- **Licence** : GPL-2.0-or-later

## Prérequis

| Composant   | Version minimale |
|-------------|------------------|
| WordPress   | 6.5 (testé jusqu'à 7.0) |
| PHP         | 8.1 |
| Node.js     | 18+ (build TailwindCSS) |
| Plugin      | ImmoSync / WPIS (CPT `wpis_estates`) |

## Installation

```bash
# 1. Cloner DANS le dossier des thèmes WordPress
cd wp-content/themes
git clone https://github.com/ArnaudKeyen/immosync-wp-theme.git hello-immosync

# 2. Installer les dépendances de build
cd hello-immosync
npm install

# 3. (optionnel) Recompiler le CSS Tailwind
npm run build
```

Le CSS compilé (`assets/css/main.css`) est versionné : le thème est donc **fonctionnel
immédiatement après le clone**, sans build. Le build n'est nécessaire que pour modifier les styles.

Activez ensuite **Apparence → Thèmes → Hello ImmoSync** (ou activez plutôt le thème enfant).

## Scripts npm

| Script          | Rôle |
|-----------------|------|
| `npm run dev`   | Compile le CSS en continu (`--watch`) pendant le développement |
| `npm run build` | Compile et minifie le CSS pour la production |

## Structure du thème

```
hello-immosync/
├── functions.php              # Amorçage : charge inc/ dans l'ordre
├── style.css                  # En-tête de thème (métadonnées WP)
├── theme.json                 # Réglages globaux (palette, typo, espacements)
├── index.php / front-page.php # Templates de base + page d'accueil
├── archive-wpis_estates.php   # Listing des biens (archive CPT)
├── single-wpis_estates.php    # Fiche bien
├── header.php / footer.php
├── inc/
│   ├── setup.php              # Supports, menus, tailles d'images
│   ├── enqueue.php            # CSS Tailwind compilé, polices, JS
│   ├── helpers.php            # Helpers de formatage génériques
│   ├── immosync-fields.php    # Accesseurs de champs WPIS
│   ├── template-tags.php      # Helpers de rendu (cartes, badges, recherche)
│   ├── search.php             # Recherche / filtres server-side (WP_Query)
│   └── structured-data.php    # JSON-LD (SEO)
├── template-parts/
│   ├── estate/                # Composants fiche bien (hero, galerie, énergie…)
│   ├── home/                  # Sections de la page d'accueil
│   └── global/                # Composants transverses (barre de recherche)
├── assets/
│   ├── css/src/main.css       # Source Tailwind (édité)
│   ├── css/main.css           # CSS compilé (versionné)
│   └── js/main.js
└── languages/                 # Fichiers de traduction (text domain : hello-immosync)
```

## Étendre via un thème enfant

Ne modifiez **jamais** ce thème parent pour des besoins spécifiques à un client : créez un
thème enfant.

```css
/* style.css du thème enfant */
/*
Theme Name: Mon Agence
Template: hello-immosync
*/
```

Le parent expose des points d'extension, par exemple le filtre `wpis_fonts_url` pour
remplacer les polices :

```php
add_filter( 'wpis_fonts_url', fn() => 'https://fonts.googleapis.com/...' );
```

## Contribution & versioning

Toute modification passe par une **Pull Request** (1 sujet = 1 branche = 1 PR).
Voir **[CONTRIBUTING.md](CONTRIBUTING.md)** pour le détail du workflow Git et des conventions.

La documentation approfondie est dans le **[Wiki](https://github.com/ArnaudKeyen/immosync-wp-theme/wiki)**
(source dans `docs/wiki/`).
