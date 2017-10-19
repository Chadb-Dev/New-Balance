#!/bin/bash

# Disable line below for debugging
#set -exu

EXPECTED_ARGS=5
E_BADARGS=100

if [ $# -lt ${EXPECTED_ARGS} ]
then
  echo "Usage:"
  echo "  `basename $0` KEYFILE SERVER_IP CLIENT_NAME SERVER_USER TARGET[eg. origin/master]"
  echo ""
  echo "This script is used to do a 'git checkout' request on the live server which updates project with latest changes from repos."
  exit ${E_BADARGS}
fi

KEYFILE="$1"
SERVER_IP="$2"
CLIENT_NAME="$3"
SERVER_USER="$4"
TARGET="$5"

WORKING_DIR="/var/www/magento_$CLIENT_NAME"

read -p "Continue updating project $CLIENT_NAME (y/n)? " -n 1 -r
echo    # move to a new line
if [[ ${REPLY} =~ ^[Yy]$ ]]
then
    echo ""
    echo "========= Updating production server working copy... "
    ssh -t -i ${KEYFILE} "${SERVER_USER}@${SERVER_IP}" "cd ${WORKING_DIR}; git fetch --all; git checkout --force ${TARGET}"
    
fi
