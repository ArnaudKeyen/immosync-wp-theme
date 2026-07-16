# Sync — post-update des biens (`wpis-post-update.php`)

Fichier optionnel à la **racine du thème**, appelé par le plugin **ImmoSync / WPIS** après la
synchronisation de chaque bien. S'il définit `wpis_post_update( $postId )`, le plugin l'inclut
(depuis le répertoire du **thème actif**, `get_stylesheet_directory()`) et l'exécute pour chaque
fiche importée ou mise à jour.

Objectif : **normaliser côté serveur** ce que la saisie manuelle ne garantit pas, et pré-calculer
quelques métas d'affichage — sans dupliquer les accesseurs de [[Architecture]]
(`inc/immosync-fields.php`).

> Motivation : les biens sont poussés par un flux externe et souvent retouchés à la main. Les
> titres deviennent incohérents (structure variable, « VENDU »/« OPTION » collés dans le titre
> alors que le statut est déjà géré à part). On force donc des valeurs propres au moment du sync.

## Ce que le hook force à chaque sync

| Sortie | Détail |
|---|---|
| `post_title` | Reconstruit **« Catégorie transaction à Ville »** (ex. *Maison à vendre à Uccle*). Repart de zéro → purge les « VENDU »/« OPTION » saisis à la main. Segments manquants ignorés (pas de « à » orphelin ni double espace). |
| `post_name` (slug) | `sanitize_title()` de `transaction-catégorie-ville` (accents/espaces gérés), suffixé par l'**ID Immosync** pour l'unicité. |
| `post_content` | Meilleure description disponible : `long` → `base` → `short`. |
| `wpis_status_label` | Libellé de statut normalisé (voir table). |
| `wpis_status_order` | Entier de tri (disponible en haut, indisponible en bas). |
| `wpis_custom_addressZipCity` | Ex. « `1180 - Uccle` ». |
| `wpis_custom_addressComplete` | Adresse française complète (`format_address_fr()`). |
| `wpis_custom_address_gps` | Coordonnées « `longitude;latitude` ». |

## Table des statuts

| Libellé source | Libellé stocké | `wpis_status_order` |
|---|---|---|
| Nouveau | Nouveau | 20 |
| À vendre / À louer / Actif | *(inchangé)* | 19 |
| Option | Option | 15 |
| Option Location | **Option** | 12 |
| Autre | **Offre en cours** | 11 |
| Sous compromis | Sous compromis | 10 |
| Vendu / Loué | *(inchangé)* | 5 |
| *(inconnu)* | *(inchangé)* | 14 *(défaut médian)* |

Pour afficher les biens groupés « disponibles d'abord, indisponibles en dernier », ordonner une
requête d'archive sur `wpis_status_order` (DESC) — c'est la raison d'être de cette méta.

## Intégration & prérequis

- Le plugin charge le fichier depuis le **thème actif** (`get_stylesheet_directory()`). Avec un
  **thème enfant** actif, y placer le fichier — ou un shim
  `require get_template_directory() . '/wpis-post-update.php';` — pour que le hook s'exécute.
- Le hook doit s'exécuter **après** la persistance des métas de la synchro pour lire des valeurs
  fraîches ; il écrit ensuite titre/slug/contenu + ses métas en un seul `wp_update_post()`.
- Logique **générique** (toutes agences) → reste dans le **parent** ; ne rien y coder de spécifique
  à un client (voir [[Theme-enfant]]).

## Fichier compagnon : `wpis-settings.json`

À la racine du thème également, lu par le plugin (`SettingsManager`). Il fixe les slugs des CPT et
taxonomies WPIS (`bien`, `projet`, `agent`, `ville`, `type-de-bien`, `transaction`, `equipement`).

## Personnaliser

Copier `wpis-post-update.php` dans le thème enfant pour ajuster le format du titre, la table des
statuts ou les métas calculées. Réutiliser les helpers existants plutôt que de relire les
`post_meta` bruts dans les templates (voir [[Architecture]] → *Données*).
