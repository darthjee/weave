#!/bin/bash

PLATFORM=${PLATFORM:-linux/amd64}
BASE_VERSION=${BASE_VERSION:-0.0.4}

function dockerfile_path() {
  local image=$1
  echo "dockerfiles/${image}-base/Dockerfile"
}

function skip_if_unchanged() {
  local image=$1

  local prev_tag
  prev_tag=$(git tag --sort=-creatordate | awk 'NR==2{print; exit}')

  if [ -z "$prev_tag" ]; then
    echo "No previous tag found, proceeding with release of ${image}-base."
    return 0
  fi

  if git diff --quiet "$prev_tag"..HEAD -- "dockerfiles/${image}-base/"; then
    echo "No changes in dockerfiles/${image}-base/ since ${prev_tag}, skipping."
    exit 0
  fi
}

function build() {
  local image=$1
  local arch=$2

  local platform tag_suffix
  if [ -n "$arch" ]; then
    platform="linux/$arch"
    tag_suffix="-$arch"
  else
    platform="$PLATFORM"
    tag_suffix=""
  fi

  local base_image="$DOCKER_ID_USER/${image}-base"
  local latest_tag="${base_image}:latest${tag_suffix}"
  local cached_tag="${base_image}:cached${tag_suffix}"
  local version_tag="${base_image}:${BASE_VERSION}${tag_suffix}"

  docker tag "$latest_tag" "$cached_tag" 2>/dev/null || true
  docker rmi "$latest_tag" 2>/dev/null || true
  docker build --platform "$platform" \
    -f "$(dockerfile_path "$image")" . \
    -t "$latest_tag"
  docker tag "$latest_tag" "$version_tag"
  if docker images | grep -q "$cached_tag"; then
    docker rmi "$cached_tag"
  fi
}

function push() {
  local image=$1
  local arch=$2
  local tag_suffix
  [ -n "$arch" ] && tag_suffix="-$arch" || tag_suffix=""

  if [ -z "$CIRCLE_TAG" ]; then
    echo "Not a tag build, skipping release of ${image}-base."
    exit 0
  fi

  skip_if_unchanged "$image"

  echo "$DOCKER_HUB_PASSWORD" | docker login -u "$DOCKER_HUB_USERNAME" --password-stdin

  build "$image" "$arch"

  local base_image="$DOCKER_ID_USER/${image}-base"
  docker push "${base_image}:latest${tag_suffix}"
  docker push "${base_image}:${BASE_VERSION}${tag_suffix}"
}

ACTION=$1
IMAGE_NAME=$2
ARCH=${3:-}

case $ACTION in
  "build") build "$IMAGE_NAME" "$ARCH" ;;
  "push")  push "$IMAGE_NAME" "$ARCH" ;;
  *)
    echo "Usage: $0 <action> <image_name> [arch]"
    echo "Actions: build, push"
    exit 1
    ;;
esac
