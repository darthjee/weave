#!/bin/bash
SSH_KEY_FILE_PATH=~/ssh_key

function run_build() {
    npm run build
    rsync -r assets/images/ dist/assets/images/
    rm -f $(find dist/assets/images/ -iname "*.pbm")
}

function run_generate_ssh_key_file() {
    echo "$SSH_PRIVATE_KEY" | sed -e "s/\\\n/\n/g" > $SSH_KEY_FILE_PATH
    chmod 600 $SSH_KEY_FILE_PATH
}

function run_upload() {
    SSH_COMMAND="ssh -i $SSH_KEY_FILE_PATH -p $SSH_PORT -o StrictHostKeyChecking=no"
    rsync -avz --delete -e "$SSH_COMMAND" dist/ $SSH_USER@$SSH_HOST:$SSH_REMOTE_DIR
}

ACTION=$1

case $ACTION in
  "build")
    run_build
    ;;
  "generate_key_file")
    run_generate_ssh_key_file
    ;;
  "upload")
    run_upload
    ;;
esac
