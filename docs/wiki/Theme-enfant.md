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

## Surcharger les polices

```php
add_filter( 'wpis_fonts_url', function () {
    return 'https://fonts.googleapis.com/css2?family=Fraunces:...&family=Inter:...&display=swap';
} );
```

## Surcharger un composant

Copiez un fichier de `template-parts/` du parent vers le même chemin relatif dans l'enfant ;
WordPress charge prioritairement la version de l'enfant.

## Exemple de référence

Le thème **La Maison Claire** (`lamaisonclaire`) est l'implémentation de référence : il ne
contient que des tokens CSS et le filtre des polices. Il est versionné dans un dépôt distinct
du parent.
