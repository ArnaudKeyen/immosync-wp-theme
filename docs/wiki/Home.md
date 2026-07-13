# Hello ImmoSync — Wiki

Thème **parent** WordPress pour agences immobilières (plugin **ImmoSync / WPIS**).

Cette source du Wiki vit dans le dépôt sous `docs/wiki/` et est miroitée vers l'onglet **Wiki**
de GitHub. Éditez les fichiers `docs/wiki/*.md` via une Pull Request, puis synchronisez le Wiki
(voir `docs/wiki/README` dans le dépôt si présent).

## Sommaire

- **[[Installation]]** — cloner, installer, compiler, activer.
- **[[Architecture]]** — parent/enfant, arborescence, includes, helpers, flux de données WPIS.
- **[[Fiche-bien]]** — en-tête modulable (variantes), sections réordonnables, galerie, PEB/EPC.
- **[[Gestion-de-contenu]]** — modèle hybride WP natif + ACF, page d'accueil, réglages, SEO, IPI.
- **[[Bientot-en-ligne]]** — mode pré-lancement : écran d'attente public, config ACF, filtres.
- **[[Workflow-Git-PR]]** — règle « 1 modification = 1 PR », branches, commits, releases.
- **[[Theme-enfant]]** — créer un thème enfant et personnaliser la direction artistique.

## En bref

- Listing des biens (`archive-wpis_estates.php`) + recherche / filtres **server-side**
  (`WP_Query`), sans dépendance JS lourde.
- **Fiche bien immersive** (`single-wpis_estates.php`) entièrement **modulaire** : en-tête à
  variantes (`stacked` / `split`), sections **réordonnables depuis l'admin** (présentation,
  équipements, surfaces, énergie, finances, art de vivre), galerie + lightbox.
- **PEB/EPC** : visuels officiels par région (Wallonie par défaut, repli CSS ailleurs).
- **Page d'accueil** pilotée par ACF (hero, find-your-place, art de vivre, off-market, agence,
  CTA vendeur) + biens d'exception automatiques.
- Templates additionnels : **Services** (hiérarchie de pages), **Estimation**, blog.
- Composants premium **TailwindCSS** (CSS compilé versionné — fonctionnel sans build).
- **SEO** : données structurées JSON-LD `Residence` (désactivables si un plugin SEO gère le schéma).
- **Mentions légales IPI/BIV** (Belgique) en sous-footer conditionnel.
- Pensé pour l'extension par **thème enfant** : aucune logique métier à dupliquer, nombreux
  filtres exposés.

## Historique

Voir le **[CHANGELOG](https://github.com/ArnaudKeyen/immosync-wp-theme/blob/main/CHANGELOG.md)**
à la racine du dépôt pour le détail des versions.
