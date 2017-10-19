#!/bin/bash

EXPECTED_ARGS=5
E_BADARGS=100

if [ $# -lt ${EXPECTED_ARGS} ]
then
  echo "Usage:"
  echo "  `basename $0` KEYFILE SERVER_IP CLIENT_NAME GIT_USERNAME GIT_PASSWORD"
  echo ""
  echo "This script is used to setup a new magento instance."
  exit ${E_BADARGS}
fi

KEYFILE="$1"
SERVER_IP="$2"
CLIENT_NAME="$3"
GIT_USERNAME="$4"
GIT_PASSWORD="$5"

REPOS_NAME="magento_$CLIENT_NAME"
WORKING_DIR="/var/www/$REPOS_NAME"
CONFIG_SCRIPT_NAME="config.sh"
SETUP_SCRIPT_NAME="setupInstance.sh"
CONFIG_SCRIPT_PATH="./$CONFIG_SCRIPT_NAME"
SETUP_SCRIPT_PATH="./$SETUP_SCRIPT_NAME"

SERVER_USER="ubuntu"

read -p "Continue with setup of $CLIENT_NAME (y/n)? " -n 1 -r
echo    # move to a new line
if [[ ${REPLY} =~ ^[Yy]$ ]]
then
    echo ""
    echo "========= Uploading scripts to $SERVER_IP:$WORKING_DIR... "
    ssh -t -i ${KEYFILE} "${SERVER_USER}@${SERVER_IP}" "sudo mkdir -p ${WORKING_DIR}"
    ssh -t -i ${KEYFILE} "${SERVER_USER}@${SERVER_IP}" "sudo chown ${SERVER_USER}:${SERVER_USER} ${WORKING_DIR}"
    scp -i ${KEYFILE} ${CONFIG_SCRIPT_PATH} "${SERVER_USER}@${SERVER_IP}:${WORKING_DIR}"
    scp -i ${KEYFILE} ${SETUP_SCRIPT_PATH} "${SERVER_USER}@${SERVER_IP}:${WORKING_DIR}"

    echo ""
    echo "========= Running the setup script... "
    ssh -t -i ${KEYFILE} "${SERVER_USER}@${SERVER_IP}" \
    "export CLIENT_NAME=$CLIENT_NAME && \
    export REPOS_NAME=$REPOS_NAME && \
    export SERVER_USER=$SERVER_USER && \
    export GIT_USERNAME=$GIT_USERNAME && \
    export GIT_PASSWORD=$GIT_PASSWORD && \
    source ${WORKING_DIR}/$CONFIG_SCRIPT_NAME && \
    sh ${WORKING_DIR}/$SETUP_SCRIPT_NAME"
fi
