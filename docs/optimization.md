# Optimization

Acorn discovers commands, service providers and listeners by scanning the
filesystem and reflecting each candidate class on every boot. For a small app
this is negligible; for a package-rich app it adds up.

## Caching discovery

Run `acorn:optimize` to scan once and cache the discovered class names to a
manifest. Subsequent boots load the listed classes directly and skip the scan.

```bash
php acorn acorn:optimize
```

```
Cached 12 command(s), 3 provider(s) and 4 listener(s) to /path/to/app/.acorn/manifest.json
```

When the manifest is present it is used automatically — there is nothing else
to configure. A missing or malformed manifest falls back to a live scan, so a
stale cache never breaks the app.

## Clearing the cache

After adding or removing a command, provider or listener, clear the manifest
(or re-run `acorn:optimize`) so discovery reflects the change:

```bash
php acorn acorn:clear
```

## When to use it

- **Development:** leave the manifest cleared so new classes are picked up
  automatically.
- **Production / CI build step:** run `acorn:optimize` as part of your
  deploy/build so the shipped app boots without scanning.

The manifest lives at `.acorn/manifest.json` by default (override with the
`framework.cache.manifest` config key). It is a generated cache and should be
git-ignored.
