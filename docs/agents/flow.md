# Flow

## Web Request Flow

All requests enter through the Tent proxy (`weave_proxy`, port 3000). Tent inspects the URL and dispatches accordingly.

### API Requests (`GET /api/*`)

```
Client
  └─▶ Tent (port 3000)
        └─▶ [cache miss] Django backend (weave_app:8080)
              └─▶ MySQL
              ◀─┘ query result
        ◀─┘ JSON response
        │   [2xx → cached to disk for subsequent requests]
        ◀─┘ cached JSON response (on cache hit)
  ◀─┘ JSON
```

Tent applies file-based caching to all 2xx API responses. Subsequent identical requests are served from cache without hitting Django.

### Frontend Requests — Development mode (`FRONTEND_DEV_MODE=true`)

```
Client
  └─▶ Tent (port 3000)
        ├─▶ [/, /assets/js/*, /assets/css/*, /@vite/*, /node_modules/*, /@react-refresh]
        │     └─▶ Vite dev server (weave_fe:8080)
        │           └─▶ React SPA (with HMR)
        │   ◀─┘
        └─▶ [/assets/images/*]
              └─▶ static files in /var/www/html/static (proxy/static/)
```

Vite handles hot module replacement (HMR) directly. Image assets are still served statically by Tent.

### Frontend Requests — Static mode (`FRONTEND_DEV_MODE=false` or unset)

```
Client
  └─▶ Tent (port 3000)
        └─▶ [/, /index.html, /assets/*]
              └─▶ static files in /var/www/html/static
                    ├── proxy/static/          (committed assets: images, etc.)
                    └── docker_volumes/static/ (Vite build output: JS, CSS, index.html)
```

The Vite build writes to `docker_volumes/static/`, which is mounted directly into Tent's static root — no copy step needed.

### In-browser API calls (React → backend)

Once the React SPA is loaded, it uses React Query to fetch data:

```
React SPA (in browser)
  └─▶ fetch /api/...
        └─▶ Tent (port 3000)  ← same flow as API requests above
```

All API calls from the frontend go through Tent, so they benefit from caching automatically.
