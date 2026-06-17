# Gestion de contenu (modèle hybride WP + ACF)

Principe : **le thème (parent) définit la _structure_** (templates + champs, versionnés) ;
**le contenu vit en base, par site**, saisi depuis l'admin. Si un champ est vide ou si ACF est
absent, le thème retombe sur des valeurs par défaut traduisibles (dégradation gracieuse).

## Deux couches à ne pas mélanger

| Couche | Où l'éditer | Quoi |
|---|---|---|
| **Contenu d'une page** | la **page** elle-même | hero (image mise en avant + titre + sous-titre), sections d'accueil, contenu des pages |
| **Réglages transverses** | menu **Réglages du thème** | coordonnées, réseaux, footer, image de repli, SEO + personnalisation du thème enfant |

## Le hero de page (hybride natif + ACF)

`template-parts/global/page-hero.php`, deux variantes (`home`, `default`) :

- **Image de fond** = *image mise en avant* native de la page (taille `wpis-hero`). Replis :
  variante home → dernier bien → image de repli globale → dégradé.
- **Titre (H1)** = champ ACF `Titre du hero` si rempli, sinon le **titre natif** de la page.
- **Sur-titre / sous-titre** = champs ACF.
- Barre de recherche : variante `home` uniquement.

→ Le client gère **image + titre via les outils WordPress natifs**, le reste via 2 champs.

## Page d'accueil

La page « Accueil » (réglée dans *Réglages → Lecture*) porte deux groupes de champs :
**Hero de page** + **Page d'accueil — sections** (onglets : Find your place, Art de vivre,
Off-market, L'agence, CTA vendeur). La section « Biens d'exception » est automatique (plugin).

**Boutons exposés** (pas de retour au code pour les demandes courantes) — ex. *Find your place* :
mode **Auto** (villes les plus fréquentes) ou **Manuel** (le client choisit dans une liste
**alimentée par les villes réelles des biens**). Même logique à généraliser sur les sections à
logique (nombre de biens, sélection manuelle…).

## Pages & services

- Pages standard (`page.php`) : hero + contenu natif (éditeur Gutenberg = liberté de structure / Hn).
- **Services** : **hiérarchie de pages** (pas de CPT). La page « Nos services » utilise le
  *Template : Services* (`template-services.php`) → hero + grille des **pages enfants**. Chaque
  service est une page enfant standard.
- Blog : `home.php` (hero basé sur la page « Blog » + liste d'articles).

## Réglages du thème (parent + enfant)

Menu **Réglages du thème** (icône pinceau) :

- **Réglages généraux** (parent) : téléphone, e-mail, réseaux sociaux, note de footer, image de
  repli du hero, **toggle Schema.org** et **mentions légales IPI** (Belgique).
- **Personnalisation** (thème enfant) : accrochée au même menu via
  `acf_add_options_sub_page(['parent_slug' => 'wpis-theme-settings'])`. Le parent ne dépend jamais
  de l'enfant.

## SEO / données structurées

Le thème émet un JSON-LD `Residence` sur les fiches biens. Réglage **« Données structurées des
biens (Schema.org) »** dans *Réglages généraux* : **à désactiver** si un plugin SEO (RankMath,
Yoast) gère déjà le schéma des biens, pour éviter un doublon. RankMath gère par ailleurs
`<title>`/méta/OG/sitemaps/schéma de site sans conflit.

## Mentions légales IPI (Belgique)

Les champs IPI (*Réglages généraux* : n° IPI, TVA, assureur, police, responsable RGPD, logo) sont
rendus dans un **sous-footer dédié** avec le **logo officiel IPI/BIV** (SVG fourni :
`assets/images/ipi-logo.svg` ; champ « Logo IPI » pour uploader une autre version). Le sous-footer
**n'apparaît que si le numéro IPI est renseigné** (jamais de mentions légales avec des valeurs vides).

## Étendre côté thème enfant

1. **Templates** : copier un fichier au même chemin → l'enfant l'emporte.
2. **Filtres/actions** dans le `functions.php` enfant (préférer un hook exposé par le parent à la
   recopie d'un template entier, pour éviter la divergence).
3. **Champs / réglages** ACF via `acf/init`.
4. **CSS / tokens / polices** (filtre `wpis_fonts_url`).
