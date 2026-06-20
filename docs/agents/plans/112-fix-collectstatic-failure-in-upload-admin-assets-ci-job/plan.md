# Plan: Fix collectstatic failure in upload_admin_assets CI job

Issue: [112-fix-collectstatic-failure-in-upload-admin-assets-ci-job.md](../../issues/112-fix-collectstatic-failure-in-upload-admin-assets-ci-job.md)

## Overview

One-line fix to `.circleci/config.yml`: prefix the `collectstatic` call in the `upload_admin_assets` job with `poetry run`, matching every other job that runs Python commands after `poetry install`.

## Context

`poetry install` does not put the project's virtualenv on `PATH` for later steps in the same CircleCI job — each `run` step needs `poetry run` to use it. `upload_admin_assets` calls `python manage.py collectstatic --noinput` without that prefix, so Django isn't importable and the job fails with `ModuleNotFoundError: No module named 'django'`.

## Implementation Steps

### Step 1 — Add the missing `poetry run` prefix

In `.circleci/config.yml`, in the `upload_admin_assets` job, change:

```yaml
      - run:
          name: Collect static files
          command: python manage.py collectstatic --noinput
```

to:

```yaml
      - run:
          name: Collect static files
          command: poetry run python manage.py collectstatic --noinput
```

## Files to Change

- `.circleci/config.yml` — add `poetry run` prefix to the `collectstatic` command in `upload_admin_assets`.

## CI Checks

- No local equivalent for this CircleCI-only job; correctness is verified by reading the job definition and comparing it to the other jobs (`pytest`, `checks`) that already use `poetry run` after `poetry install`.

## Notes

- This is a single-line infra fix; no specialist agent split is needed.
