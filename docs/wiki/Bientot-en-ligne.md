# Bientôt en ligne (Coming soon)

Mode **pré-lancement** : tant qu'il est activé, tout le front public est remplacé par un écran
d'attente plein écran. Pratique pour publier un site (DNS, indexation, partages) avant que le
contenu soit prêt, sans exposer un site en chantier.

- **L'équipe voit le vrai site.** Tout utilisateur connecté disposant du droit `edit_posts`
  (administrateur, éditeur…) navigue normalement — il peut donc préparer le site en conditions
  réelles pendant que le public voit l'écran d'attente.
- **Le public voit l'écran d'attente**, servi avec un statut `HTTP 503 Service Unavailable`
  (+ `Retry-After`) et une balise `noindex, nofollow` : les moteurs ne l'indexent pas et
  reviendront après le lancement.
- **Désactivé par défaut** (opt-in) : un site neuf sous le thème n'affiche jamais cet écran tant
  qu'on ne l'a pas explicitement activé.

## Activer / configurer

Menu **Réglages du thème → Bientôt en ligne** (sous-page d'options `wpis-coming-soon`).

| Champ (ACF) | Rôle |
|---|---|
| `wpis_cs_enabled` | Interrupteur principal. **Décocher le jour du lancement** pour rouvrir le site. |
| `wpis_cs_background` | Image de fond (repli : visuel du thème enfant, sinon dégradé). |
| `wpis_cs_logo` | Logo (version claire pour fond sombre ; repli : visuel enfant, sinon le nom du site). |
| `wpis_cs_eyebrow` | Surtitre (défaut : « Bientôt en ligne »). |
| `wpis_cs_heading` | Titre texte si aucun logo. |
| `wpis_cs_text` | Message d'attente. |
| `wpis_cs_contact_name` | Nom du contact affiché. |
| `wpis_cs_phone` | Téléphone (lien `tel:` automatique). |
| `wpis_cs_email` | E-mail (lien `mailto:` ; défaut : e-mail admin du site). |
| `wpis_cs_cta_url` | Lien du bouton. **Le bouton est masqué si vide.** |
| `wpis_cs_cta_label` | Libellé du bouton (défaut : « Découvrir nos biens »). |

L'écran est **autonome** (HTML + CSS inline, polices via le filtre `wpis_fonts_url`) : il ne dépend
pas du CSS Tailwind compilé et s'affiche même très tôt dans le cycle WordPress
(`template_redirect`). Il reprend les tokens de couleur/typo (`--color-ink`, `--color-brand`,
`--font-display`…) s'ils existent, donc il hérite de la direction artistique du thème enfant.

## Étendre depuis un thème enfant

Le parent reste agnostique : aucun visuel ni texte client n'est codé en dur. Un thème enfant
fournit ses **visuels par défaut** via deux filtres (utilisés seulement si l'option ACF
correspondante est vide) :

```php
// functions.php du thème enfant
add_filter( 'wpis_coming_soon_default_background_url', function ( $url ) {
    $rel = 'assets/img/coming-soon-bg.jpg';
    return file_exists( get_stylesheet_directory() . '/' . $rel )
        ? get_stylesheet_directory_uri() . '/' . $rel
        : $url;
} );

add_filter( 'wpis_coming_soon_default_logo_url', function ( $url ) {
    $rel = 'assets/img/logo-negatif.png';
    return file_exists( get_stylesheet_directory() . '/' . $rel )
        ? get_stylesheet_directory_uri() . '/' . $rel
        : $url;
} );
```

Autres points d'extension :

- `wpis_coming_soon_active` (bool) — forcer/inhiber l'affichage par du code (ex. whitelist d'IP,
  fenêtre horaire) sans passer par l'option.

## Comportement & garde-fous

- Ne s'active jamais pour l'admin, l'AJAX, le cron, l'API REST, WP-CLI, `robots.txt` ou la favicon
  → connexion, back-office et intégrations restent accessibles.
- Le rendu part en `exit` après l'écran : aucun template de thème n'est chargé pour le visiteur
  public, ce qui rend la page très légère.

## Cycle de lancement type

1. Activer l'interrupteur, renseigner visuels + contact (+ éventuellement le lien du bouton).
2. Publier / mettre en ligne : le public voit l'écran, l'équipe finalise le site connectée.
3. Le jour J : **décocher `wpis_cs_enabled`** → le site complet s'ouvre au public, l'indexation
  reprend (le `503`/`noindex` disparaît).
