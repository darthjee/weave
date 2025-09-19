#!/bin/bash
set -e

# Pasta do código
CODE_DIR="."
EXCLUDE="manage.py"

case "$1" in
  complexity)
    poetry run radon cc $CODE_DIR -s -a --exclude $EXCLUDE
    ;;
  maintainability)
    poetry run radon mi $CODE_DIR --exclude $EXCLUDE
    ;;
  ci)
    poetry run xenon $CODE_DIR --max-absolute B --max-modules A --max-average A --exclude $EXCLUDE
    ;;
  lizard)
    poetry run lizard -x $EXCLUDE --html -o lizard_report.html $CODE_DIR
    echo "Lizard HTML report generated: lizard_report.html"
    ;;
  *)
    echo "Usage: $0 {complexity|maintainability|ci|lizard}"
    exit 1
    ;;
esac

