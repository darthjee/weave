# weave

[![Build Status](https://circleci.com/gh/darthjee/weave.svg?style=shield)](https://circleci.com/gh/darthjee/weave)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/35480a5e82e74ff7a0186697b3f61a4b)](https://app.codacy.com/gh/darthjee/weave/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

**Current Version:** [0.1.8](https://github.com/darthjee/weave/releases/tag/0.1.8)  
**Next Release:** [0.1.9](https://github.com/darthjee/weave/compare/0.1.8...master)

---

## About

An app to display curriculum vitae as a D&D character sheet, where:
- **Skills** represent technologies and programming languages
- **Inventory** showcases projects and work experience
- **Data** will be fetched from GitHub repositories (planned feature)

---

## Tech Stack

- **Backend:** Django 5.2 + Django REST Framework + MySQL
- **Frontend:** React 19 + Vite + React Query + Bootstrap 5
- **Infrastructure:** Docker + Docker Compose

---

## Getting Started

### Prerequisites

- Docker
- Docker Compose

### Running with Docker

1. Clone the repository:
```bash
git clone https://github.com/darthjee/weave.git
cd weave
```

2. Set up environment variables:
```bash
cp .env.example .env
# Edit .env with your configuration
```

3. Start the services:
```bash
make dev-up
```

4. Access the application:
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:3030
- **PhpMyAdmin:** http://localhost:3050

### Running Tests

**Backend:**
```bash
docker-compose run weave_tests poetry run pytest
```

**Frontend:**
```bash
docker-compose run weave_fe npm test
```

---

## Project Structure

```
weave/
├── backend/              # Django application
│   ├── curriculum/       # Curriculum app (models, views, serializers)
│   ├── weave/           # Project settings
│   └── manage.py
├── frontend/            # React application
│   ├── assets/          # JS, CSS, images
│   ├── spec/            # Tests
│   └── package.json
└── docker-compose.yml   # Docker services configuration
```

---

## Environment Variables

### Backend (.env)
```env
WEAVE_MYSQL_NAME=weave
WEAVE_MYSQL_USER=root
WEAVE_MYSQL_PASSWORD=weave
WEAVE_MYSQL_HOST=mysql
WEAVE_MYSQL_PORT=3306
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://127.0.0.1:3000"
```

### Frontend (.env)
```env
VITE_WEAVE_API_URL=http://localhost:3030
```

---

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

MIT