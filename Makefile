.PHONY: build-base push-base build push dev

PROJECT?=weave
IMAGE?=$(PROJECT)
BASE_VERSION?=0.0.4
PUSH_IMAGE=$(DOCKER_ID_USER)/$(PROJECT)
DOCKER_FILE=dockerfiles/$(PROJECT)/Dockerfile

all:
	@echo "Usage:"
	@echo "  make build\n    Build docker image for $(PROJECT)"
	@echo "  make build-base\n    Build base docker image for $(PROJECT)"
	@echo "  make push-base\n    Pushes base docker image for $(PROJECT) to dockerhub"

build-base:
	BASE_VERSION=$(BASE_VERSION) scripts/image.sh build $(PROJECT)

push-base:
	BASE_VERSION=$(BASE_VERSION) scripts/image.sh push $(PROJECT)

setup:
	docker-compose run --rm $(PROJECT)_fe yarn install
	docker-compose run --rm $(PROJECT)_app bin/configure_database.sh all

build:
	docker build -f $(DOCKER_FILE) . -t $(IMAGE) -t $(PUSH_IMAGE) -t $(PUSH_IMAGE):$(BASE_VERSION)

push:
	make build
	docker push $(PUSH_IMAGE)
	docker push $(PUSH_IMAGE):$(BASE_VERSION)

tests:
	docker-compose run $(PROJECT)_tests /bin/bash

dev:
	docker-compose run $(PROJECT)_app /bin/bash

dev-fe:
	docker-compose run $(PROJECT)_fe /bin/bash

dev-up:
	docker-compose up $(PROJECT)_proxy

production-up: .env.prod
	docker-compose up $(PROJECT)_prod_app

production: .env.prod
	docker-compose run $(PROJECT)_prod_app /bin/bash

.env.prod:
	cp .env.prod.test .env.prod
