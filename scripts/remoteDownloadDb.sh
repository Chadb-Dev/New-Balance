#!/bin/bash

# Disable line below for debugging
#set -exu

EXPECTED_ARGS=4
E_BADARGS=100

if [ $# -lt ${EXPECTED_ARGS} ]
then
  echo "Usage:"
  echo "  `basename $0` KEYFILE SERVER_IP CLIENT_NAME SAVE_PATH"
  echo ""
  echo "This script is used to download live magento database."
  exit ${E_BADARGS}
fi

KEYFILE="$1"
SERVER_IP="$2"
CLIENT_NAME="$3"
SAVE_PATH="$4"
SERVER_USER="ubuntu"

WORKING_DIR="/var/www/magento_$CLIENT_NAME"

read -p "Continue downloading of $CLIENT_NAME database (y/n)? " -n 1 -r
echo    # move to a new line
if [[ ${REPLY} =~ ^[Yy]$ ]]
then
    echo ""
    echo "========= Export database... "
    ssh -t -i ${KEYFILE} "${SERVER_USER}@${SERVER_IP}" "mysqldump  -u magento --password='indo2000' magento | gzip -c | cat > ${WORKING_DIR}/magento.sql.gz"
    echo ""
    echo "========= Download database... "
    scp -i ${KEYFILE} ${SERVER_USER}@${SERVER_IP}:${WORKING_DIR}/magento.sql.gz "${SAVE_PATH}/${CLIENT_NAME}.sql.gz"
    echo ""
    echo "========= Extract database... "
    sudo gunzip "${SAVE_PATH}/${CLIENT_NAME}.sql.gz"
    echo ""
    echo "========= Filename - ${CLIENT_NAME}.sql"
    
fi
