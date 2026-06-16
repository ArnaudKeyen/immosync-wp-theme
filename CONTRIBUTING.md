# Contribuer à Hello ImmoSync

## Règle d'or : **1 modification = 1 Pull Request**

Aucun commit n'est poussé directement sur `main`. **Chaque modification** (correctif, nouvelle
fonctionnalité, refonte d'un composant, doc…) passe par une **branche dédiée** puis une **Pull
Request** revue et fusionnée. `main` reste à tout moment déployable.

Un sujet cohérent par PR : ne mélangez pas un correctif de bug et une nouvelle section dans la
même branche.

## Modèle de branches

| Branche                | Rôle |
|------------------------|------|
| `main`                 | Branche de production, protégée, toujours déployable. |
| `feat/<sujet>`         | Nouvelle fonctionnalité. |
| `fix/<sujet>`          | Correction de bug. |
| `docs/<sujet>`         | Documentation (README, Wiki, commentaires). |
| `refactor/<sujet>`     | Refonte sans changement de comportement. |
| `chore/<sujet>`        | Build, dépendances, outillage. |

## Cycle de travail

```bash
# 1. Partir d'un main à jour
git switch main
git pull --ff-only

# 2. Créer une branche dédiée à la modification
git switch -c feat/fiche-bien-galerie-plein-ecran

# 3. Coder. Si vous touchez au CSS/JS, recompiler :
npm run build

# 4. Commit avec un message conventionnel (voir plus bas)
git add -A
git commit -m "feat(estate): galerie plein écran sur la fiche bien"

# 5. Pousser la branche
git push -u origin feat/fiche-bien-galerie-plein-ecran

# 6. Ouvrir la Pull Request
gh pr create --base main --fill

# 7. Après revue + checks verts, fusionner en squash et supprimer la branche
gh pr merge --squash --delete-branch
```

## Convention de commits (Conventional Commits)

`type(scope): description courte à l'impératif`

- **Types** : `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `chore`, `build`.
- **Scope** (optionnel) : zone touchée — `estate`, `home`, `search`, `seo`, `build`, `i18n`…
- Exemples :
  - `feat(search): filtre par fourchette de prix server-side`
  - `fix(seo): corrige le JSON-LD des biens sans prix`
  - `docs: documente le filtre wpis_fonts_url`

## Avant d'ouvrir la PR — checklist

- [ ] La modification a un **périmètre unique** (pas de changements hors sujet).
- [ ] `npm run build` exécuté si CSS/JS modifié, et `assets/css/main.css` commité.
- [ ] Pas de code PHP générant d'avertissement (sorties échappées, données nettoyées).
- [ ] Chaînes traduisibles avec le text domain `hello-immosync`.
- [ ] Documentation à jour (README / Wiki) si le comportement public change.

## Versioning (SemVer)

La version suit [SemVer](https://semver.org/) et doit rester **synchronisée** entre :

1. `style.css` → en-tête `Version:`
2. `package.json` → `version`
3. `functions.php` → constante `HELLO_IMMOSYNC_VERSION`

| Type de changement                         | Incrément |
|--------------------------------------------|-----------|
| Correctif rétro-compatible                 | `PATCH` (0.1.0 → 0.1.1) |
| Fonctionnalité rétro-compatible            | `MINOR` (0.1.0 → 0.2.0) |
| Rupture pour les thèmes enfants            | `MAJOR` (0.1.0 → 1.0.0) |

Une release est taguée sur `main` après fusion :

```bash
git switch main && git pull --ff-only
git tag -a v0.2.0 -m "Hello ImmoSync 0.2.0"
git push origin v0.2.0
gh release create v0.2.0 --generate-notes
```

## Ce qui est (ou non) versionné

- **Versionné** : tout le code PHP/JS, la source Tailwind, **le CSS compilé** `assets/css/main.css`
  (déploiement par copie sans build), `package-lock.json`.
- **Ignoré** : `node_modules/`, fichiers OS/éditeur, logs (voir `.gitignore`).
