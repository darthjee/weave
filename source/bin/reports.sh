#!/bin/bash
set -e

# Pasta do código
CODE_DIR="."

case "$1" in
  complexity)
    poetry run radon cc $CODE_DIR -s -a
    ;;
  maintainability)
    poetry run radon mi $CODE_DIR
    ;;
  ci)
    poetry run xenon $CODE_DIR --max-absolute B --max-modules A --max-average A
    ;;
  lizard)
    poetry run lizard $CODE_DIR -html -o lizard_report.html
    echo "Lizard HTML report generated: lizard_report.html"
    ;;
  *)
    echo "Usage: $0 {complexity|maintainability|ci|lizard}"
    exit 1
    ;;
esac

