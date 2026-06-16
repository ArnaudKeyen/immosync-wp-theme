# Hello ImmoSync — Wiki

Thème **parent** WordPress pour agences immobilières (plugin **ImmoSync / WPIS**).

Cette source du Wiki vit dans le dépôt sous `docs/wiki/` et est miroitée vers l'onglet **Wiki**
de GitHub. Éditez les fichiers `docs/wiki/*.md` via une Pull Request, puis synchronisez le Wiki
(voir `docs/wiki/README` dans le dépôt si présent).

## Sommaire

- **[[Installation]]** — cloner, installer, compiler, activer.
- **[[Architecture]]** — parent/enfant, arborescence, helpers, flux de données WPIS.
- **[[Workflow-Git-PR]]** — règle « 1 modification = 1 PR », branches, commits, releases.
- **[[Theme-enfant]]** — créer un thème enfant et personnaliser la direction artistique.

## En bref

- Listing des biens (`archive-wpis_estates.php`) + fiche bien immersive (`single-wpis_estates.php`).
- Recherche / filtres **server-side** (WP_Query), pas de dépendance JS lourde.
- Composants premium **TailwindCSS** (CSS compilé versionné).
- **SEO** : données structurées JSON-LD intégrées.
- Pensé pour l'extension par **thème enfant** (aucune logique métier à dupliquer).
