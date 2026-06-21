# Installation & build

## Prérequis

- WordPress **6.5+** (testé jusqu'à 7.0), PHP **8.1+**
- Plugin **ImmoSync / WPIS** activé (fournit le CPT `wpis_estates`)
- **Advanced Custom Fields** (ACF Pro recommandé) — pour éditer le contenu et accéder au menu
  *Réglages du thème*. Optionnel : sans ACF, le thème dégrade gracieusement vers ses valeurs
  par défaut.
- Node.js **18+** (uniquement pour modifier les styles)

## Installer

```bash
cd wp-content/themes
git clone https://github.com/ArnaudKeyen/immosync-wp-theme.git hello-immosync
cd hello-immosync
npm install        # dépendances de build (TailwindCSS)
```

Le dossier **doit** s'appeler `hello-immosync` (c'est le `Template` attendu par les thèmes
enfants et le text domain).

## Compiler le CSS

```bash
npm run dev     # watch pendant le développement
npm run build   # build minifié pour la production
```

> Le CSS compilé `assets/css/main.css` est versionné : aucune compilation n'est requise pour
> simplement déployer le thème. Ne lancez un build que si vous modifiez `assets/css/src/main.css`
> ou les classes Tailwind des templates.

## Activer

`Apparence → Thèmes`. En production, activez de préférence un **thème enfant** (ex. La Maison
Claire) plutôt que le parent directement.
