# Contributing

## Commit Guidelines

- **Atomic and Unitary:** Each commit must represent a single logical change.  
  *Example:*  
  - Good: `Add PersonSerializer with nested skills field`  
  - Bad: `Add PersonSerializer and refactor URL routing`
- **No Unrelated Changes:** Do not mix unrelated changes in the same commit.
- **Separate Refactoring:** Whenever possible, separate refactoring commits from new feature or bugfix commits.

## Pull Requests

- **Descriptive Summary:** Every PR must include a clear and descriptive summary of its purpose and changes.
- **PR Description Files:** If a description cannot be provided directly in the PR, generate a file with the PR description (e.g., `docs/agents/issues/<pr_number>_description.md`), but do not commit this file.

## Definition of Done for PRs

A PR is considered complete when:

- The stated objective has been achieved.
- All tests are passing.
- Linting passes without errors.
- Code coverage is as high as reasonably possible.
- Code is not overly complex:
  - Classes and methods should have clear, focused responsibilities.
  - If a class or method is taking on too many responsibilities, refactor to simplify.
  - Methods should be small and do exactly one thing. If a method is growing, extract parts into private helper methods or separate classes.
  - *Example (Python):*
    ```python
    # Good: each method does one thing
    class PersonView:
        def get_person(self, request): ...
        def serialize(self, person): ...

    # Bad: method does too much
    class PersonView:
        def handle(self, request):
            person = self.get_person(request)
            data = self.serialize(person)
            self.log(person)
            self.send_metrics(person)
            return data
    ```
  - This requirement applies primarily to source code. For tests, refactor only if there is excessive duplication.

## Code Organization

### Backend (Python/Django)

#### File Responsibility

Every source file must define and export one class or a focused group of related classes. Files must not execute logic at import time or perform side effects on import.

The only exceptions are Django entrypoints:

| Application | Entrypoint |
|-------------|-----------|
| Django app | `backend/manage.py` |
| WSGI server | `backend/weave/wsgi.py` |
| ASGI server | `backend/weave/asgi.py` |

#### File Naming: `snake_case` for Python files

Files must use `snake_case` naming, matching the class or module name.

*Examples:*
- `person_serializer.py` for `class PersonSerializer`
- `person_retrieve_view.py` for `class PersonRetrieveView`

Spec files mirror the source path and name:
- `curriculum/serializers/person_serializer.py` → `curriculum/tests/serializers/person_serializer_test.py`

### Frontend (JavaScript/React)

#### File Responsibility

Every source file must define and export one component or a focused group of related utilities. Files must not execute logic at module level.

#### File Naming

- React components: `PascalCase.jsx` matching the component name.
- Utility modules: `camelCase.js`.
- Spec files mirror the source name: `PersonCard.jsx` → `PersonCard_spec.js`.

## Dependency Injection

Classes and components must receive their dependencies as arguments or props. Neither backend classes nor frontend components should reach out to load configuration, read environment variables, or fetch data on their own — that is the responsibility of the entry point or a dedicated data-fetching layer (React Query on the frontend).

*Example (Python):*
```python
# Good: view receives serializer class as a dependency
class PersonRetrieveView(RetrieveAPIView):
    serializer_class = PersonSerializer

# Bad: view instantiates its own dependencies internally
class PersonRetrieveView(APIView):
    def get(self, request):
        serializer = PersonSerializer(Person.objects.get(...))
        ...
```

## Running Tests

**Backend:**
```bash
docker-compose run weave_tests poetry run pytest
```

**Frontend:**
```bash
docker-compose run weave_fe npm test
```

## Linting

**Backend** (ruff, max line length 88):
```bash
docker-compose run weave_tests poetry run ruff check .
```

**Frontend** (ESLint):
```bash
docker-compose run weave_fe npm run lint
```

## Refactoring Guidelines

When refactoring, aim to:

- **Reduce Code Duplication:**  
  *Example (Python):* Move repeated fixture setup in tests to a factory function or pytest fixture.
  ```python
  # Good
  @pytest.fixture
  def person():
      return Person(name="Ada", role="Engineer")

  # Bad
  def test_something():
      person = Person(name="Ada", role="Engineer")  # repeated in every test
  ```

- **Keep Classes Focused:** If a class handles more than one concern, split it.

- **Keep Methods Small:** Extract logic into private helpers when a method grows beyond a single clear purpose.
