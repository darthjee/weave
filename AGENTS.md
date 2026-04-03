# Project Instructions

Weave is a user-agnostic CV generation tool. It exposes structured profile data and enriches it by reading GitHub to gather information about projects, commits, and language proficiency. Anyone can use it to track and present their own developer profile.

## Stack

- **Backend**: Python, Django (main folder)
- **Frontend**: ReactJS (frontend folder)
- **Proxy**: [Tent](https://github.com/darthjee/tent) — a PHP application (external project) that serves as a reverse proxy and cache layer for both the frontend and backend

## Architecture Overview

- The backend (Django) is the primary application and lives at the root of the repo.
- The frontend (ReactJS) lives in the `frontend/` folder.
- Tent acts as the entry point, routing and caching requests to both the backend API and the frontend assets.

## Conventions

- The project is user-agnostic: avoid hardcoding any specific user identity in logic or configuration.
- GitHub integration is a core feature — changes to data-fetching logic should preserve the ability to work with any GitHub user.

## Documentation

All project documentation lives under [`docs/agents/`](docs/agents/):

| File | Contents |
|------|----------|
| [Folder Structure](docs/agents/folder-structure.md) | Top-level directory layout and the role of each folder. |
| [Architecture](docs/agents/architecture.md) | Source layout, modules, code style, and implementation guidelines. |
| [Flow](docs/agents/flow.md) | Main runtime flow of the application. |
| [Plans](docs/agents/plans/) | Implementation plans for ongoing or upcoming features. |
| [Issues](docs/agents/issues/) | Detailed specs for open issues. |
| [Contributing](docs/agents/contributing.md) | Commit guidelines, PR standards, code organization, and refactoring rules. |

### Issues (`docs/agents/issues/`)

Each file documents an issue in detail. Naming convention:

```
docs/agents/issues/<issue_id>_<issue_name>.md
```

Example: `docs/agents/issues/5_release_docker_image.md` for issue #5.

### Plans (`docs/agents/plans/`)

Each plan is a directory named after the issue ID and topic, containing one or more related files:

```
docs/agents/plans/<issue_id>_<topic>/<related_files>.md
```

Example: `docs/agents/plans/12_add-auth/plan.md` for issue #12.
