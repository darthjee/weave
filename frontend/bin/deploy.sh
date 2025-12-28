#!/bin/bash

function run_build() {
    npm run build
    rsync -r assets/images/ dist/assets/images/
    rm -f $(find dist/assets/images/ -iname "*.pbm")
}

ACTION=$1

case $ACTION in
  "build")
    run_build
    ;;
  "upload")
    run_upload
    ;;
esac