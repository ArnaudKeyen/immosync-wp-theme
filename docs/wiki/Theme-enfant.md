# Créer un thème enfant

Personnalisez l'apparence **sans jamais modifier le parent**. Un thème enfant vit dans son
**propre dépôt Git**.

## Squelette minimal

```
mon-agence/
├── style.css
├── functions.php
└── assets/css/style.css
```

`style.css` — l'en-tête `Template` doit pointer vers le dossier du parent :

```css
/*
Theme Name: Mon Agence
Template: hello-immosync
Version: 0.1.0
*/
```

`functions.php` — charger les overrides **après** le CSS Tailwind du parent (handle
`hello-immosync`) :

```php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'mon-agence',
        get_stylesheet_directory_uri() . '/assets/css/style.css',
        array( 'hello-immosync' ), // dépendance = parent
        filemtime( get_stylesheet_directory() . '/assets/css/style.css' )
    );
}, 20 );
```

## Surcharger la palette

Les tokens de couleur sont nommés **par fonction**, pas par teinte : un enfant peut donc passer
d'une palette chaude à une palette froide sans qu'aucun nom ne devienne mensonger. Redéfinir les
variables dans `assets/css/style.css` suffit — les utilitaires Tailwind du parent (`bg-surface`,
`text-text-strong`, `border-border`…) suivent automatiquement.

| Token | Rôle |
|---|---|
| `--color-text-strong` | Titres, header, aplats sombres, boutons pleins |
| `--color-text` | Texte courant / body |
| `--color-text-secondary` | Texte secondaire |
| `--color-text-muted` | Tertiaire, méta, placeholders |
| `--color-border` | Bordures fines |
| `--color-surface-alt` | Fond alterné |
| `--color-surface` | Fond principal (et texte sur fond sombre) |
| `--color-brand` | Accent de marque |
| `--color-brand-strong` | État survol / actif de la marque |

```css
:root {
    --color-text-strong: #0e1f40;
    --color-surface: #edeef5;
    --color-brand: #ecc747;
}
```

Penser aussi aux slugs de la palette de `theme.json`, qui doivent reprendre les mêmes noms pour que
l'éditeur de blocs reste cohérent avec le front.

> Ces noms datent de la **0.4.0**. Avant, ils portaient un nom de couleur (`ink`, `cream`,
> `charcoal`…) — voir la table de migration dans le `CHANGELOG.md`.

## Surcharger les polices

```php
add_filter( 'wpis_fonts_url', function () {
    return 'https://fonts.googleapis.com/css2?family=Fraunces:...&family=Inter:...&display=swap';
} );
```

## Surcharger un composant

Copiez un fichier de `template-parts/` du parent vers le même chemin relatif dans l'enfant ;
WordPress charge prioritairement la version de l'enfant.

## Étendre la fiche bien par filtres (préféré)

Plutôt que de recopier un template, utilisez les filtres exposés par le parent pour réordonner
les sections, ajouter une variante d'en-tête ou changer la région PEB :

```php
add_filter( 'wpis_estate_section_registry', /* ajouter/retirer une section */ );
add_filter( 'wpis_estate_hero_registry',    /* ajouter une variante d'en-tête */ );
add_filter( 'wpis_epc_region', fn() => 'brussels' );
```

Détail complet : **[[Fiche-bien]]**.

## Implémentation de référence

Un thème enfant de référence ne contient que des tokens CSS et le filtre des polices, et reste
versionné dans un dépôt distinct du parent. Le parent, lui, ne nomme jamais un client en
particulier.
