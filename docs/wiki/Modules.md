# Modules activables

Certaines fonctionnalités du thème sont des **modules optionnels**, activés agence par agence selon
l'offre souscrite. Le code est livré avec le thème, mais un module coupé ne produit **aucun**
markup, script ni requête.

| Clé | Module | Effet |
|---|---|---|
| `share` | Partage des annonces | Boutons WhatsApp, Facebook et « Copier le lien » sur les fiches de biens. |
| `search_ajax` | Recherche instantanée | Sur la page des biens, les résultats se mettent à jour sans rechargement. |

## Qui active quoi

L'activation est **réservée à l'intégrateur** : l'administrateur de l'agence ne voit pas la page.

- **Page d'options** : *Réglages du thème → Modules* (`wpis-theme-modules`), visible uniquement
  des comptes portant la capacité **`wpis_manage_modules`**. À attribuer en WP-CLI :

  ```bash
  wp user add-cap <login> wpis_manage_modules
  wp user remove-cap <login> wpis_manage_modules
  ```

  L'agence doit donc disposer de **son propre compte** administrateur, distinct de celui de
  l'intégrateur.

- **Constante** (prioritaire sur la page) dans `wp-config.php`, pour figer l'état côté serveur :

  ```php
  define( 'WPIS_MODULES', 'share,search_ajax' ); // ou array( 'share' ) ; '' = tout coupé
  ```

  Quand la constante est définie, la page l'indique et les cases sont ignorées.

## API

| Fonction / filtre | Rôle |
|---|---|
| `wpis_module_enabled( $key )` | `true` si le module est actif. À tester dans le code du module. |
| `wpis_get_enabled_modules()` | Clés des modules actifs (constante, sinon option `options_wpis_modules`). |
| `wpis_get_module_registry()` | Registre `key => { label, description }`. |
| filtre `wpis_module_registry` | Déclarer un module (thème enfant, extension). |
| filtre `wpis_module_enabled` | Dernier mot sur l'état d'un module. |
| filtre `wpis_modules_capability` | Changer la capacité requise (défaut `wpis_manage_modules`). |

L'état est lu directement dans l'option (`get_option`), sans passer par ACF : désactiver ACF ne
fait pas « réapparaître » un module.
