#!/bin/bash

## Description: Check if mysql is running, if not then start it.
if [[ ! "$(service mysql status)" =~ "start/running" ]]
then
    service mysql start
fi