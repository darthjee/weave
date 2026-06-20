# Fix collectstatic failure in upload_admin_assets CI job

## Context

The `upload_admin_assets` job in `.circleci/config.yml` runs `poetry install` to set up the Python virtualenv, but the next step calls `python manage.py collectstatic --noinput` directly instead of through `poetry run`. Since `poetry install` does not activate its virtualenv for subsequent shell steps, the bare `python` interpreter does not have Django (or any other dependency) on its path, and the job fails with:

```
ModuleNotFoundError: No module named 'django'
```

Every other CI job that runs Python commands (`pytest`, `checks`) correctly prefixes them with `poetry run`; `upload_admin_assets` was missed.

## What needs to be done

- In `.circleci/config.yml`, update the `upload_admin_assets` job's "Collect static files" step to run `poetry run python manage.py collectstatic --noinput` instead of `python manage.py collectstatic --noinput`.

## Acceptance criteria

- [ ] The `upload_admin_assets` job's "Collect static files" step runs `poetry run python manage.py collectstatic --noinput`.
- [ ] No other step in `.circleci/config.yml` that depends on the poetry virtualenv is missing the `poetry run` prefix.
