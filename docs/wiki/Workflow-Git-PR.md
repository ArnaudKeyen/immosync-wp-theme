# Workflow Git — 1 modification = 1 PR

> Référence canonique : [`CONTRIBUTING.md`](https://github.com/ArnaudKeyen/immosync-wp-theme/blob/main/CONTRIBUTING.md) à la racine du dépôt.

## Principe

`main` est **protégée** et toujours déployable. Aucun push direct dessus. Chaque modification
vit sur une **branche dédiée** et est intégrée par une **Pull Request** revue.

## Étapes

```bash
git switch main && git pull --ff-only
git switch -c feat/<sujet>          # feat | fix | docs | refactor | chore
# ... modifications ...
npm run build                       # si CSS/JS touché
git commit -am "feat(scope): description impérative"
git push -u origin feat/<sujet>
gh pr create --base main --fill
gh pr merge --squash --delete-branch
```

## Commits conventionnels

`type(scope): description` — types : `feat`, `fix`, `docs`, `style`, `refactor`, `perf`,
`chore`, `build`. Scopes usuels : `estate`, `home`, `search`, `seo`, `build`, `i18n`.

## Releases (SemVer)

Version synchronisée entre `style.css`, `package.json` et la constante
`HELLO_IMMOSYNC_VERSION`. Après fusion sur `main` :

```bash
git tag -a v0.2.0 -m "Hello ImmoSync 0.2.0"
git push origin v0.2.0
gh release create v0.2.0 --generate-notes
```

| Changement                       | Incrément        |
|----------------------------------|------------------|
| Correctif rétro-compatible       | PATCH            |
| Fonctionnalité rétro-compatible  | MINOR            |
| Rupture pour les thèmes enfants  | MAJOR            |
