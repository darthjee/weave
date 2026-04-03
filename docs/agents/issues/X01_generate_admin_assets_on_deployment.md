# Issue: Generate Admin Assets on Deployment

## Description

Django admin static files are currently committed to the repository under `proxy/static/assets/admin/` and uploaded to the server as part of the deployment process. These files should not be version-controlled — they are generated artifacts that Django can produce at deployment time via the `collectstatic` management command.

## Problem

- Django admin static assets (`proxy/static/assets/admin/`) are committed to the repository.
- The deployment process uploads these committed files to the server instead of generating them fresh.
- Committed generated files can become stale, cause unnecessary diff noise, and bloat the repository.

## Expected Behavior

- `proxy/static/assets/admin/` is excluded from version control (added to `.gitignore`).
- During deployment, `python manage.py collectstatic` is run to generate the admin assets into the correct location.
- The server always has up-to-date admin assets that match the deployed Django version.

## Solution

- Add `proxy/static/assets/admin/` to `.gitignore`.
- Remove the committed admin asset files from the repository.
- Add a dedicated deployment step that:
  1. Runs `python manage.py collectstatic` to generate the admin assets.
  2. Uploads the generated assets to the server.
- This step must run **in parallel** with the existing build-and-release step, not sequentially, to avoid increasing total deployment time.
- Ensure the `collectstatic` output is directed to the path that Tent serves statically.

## Benefits

- Keeps generated files out of version control.
- Ensures admin assets always match the deployed Django version.
- Reduces repository size and eliminates noisy diffs when Django or its dependencies update their admin assets.
- Parallel execution keeps deployment time unchanged.
