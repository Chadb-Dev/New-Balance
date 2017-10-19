#!/bin/bash

EXPECTED_ARGS=1
E_BADARGS=100

if [ $# -lt ${EXPECTED_ARGS} ]
then
  echo "NOTICE:"
  echo "  Make sure that you have copied 'scripts/config.dev.sh.sample' and created the shell script for this client prior to running this script!"
  echo "  So if the client name is 'company_x' then the copied filename should be 'company_x.dev.sh'"
  echo ""
  echo "DESCRIPTION:"
  echo "  This script is used to setup a new magento project."
  echo ""
  echo "USAGE:"
  echo "  `basename $0` CLIENT_NAME"
  exit ${E_BADARGS}
fi

# Set (x)trace to print commands executed for debugging
# Set (e)xit on error
# Set no(u)nset to exit on undefined variable
set -exu
# If any command in a pipeline fails, that return code will be used as the
# return code of the whole pipeline.
bash -c 'set -o pipefail'

#CLIENT_NAME="$1"
#GIT_USERNAME="$2"
#GIT_PASSWORD="$3"
#WORKING_DIR="$4"

#REPOS_NAME="magento_$CLIENT_NAME"
#WORKING_DIR="/mnt/hgfs/workspace/$REPOS_NAME"
#CONFIG_SCRIPT_NAME="$CLIENT_NAME.sh"
#SETUP_SCRIPT_NAME="setupRepos.sh"
#CONFIG_SCRIPT_PATH="./config/$CONFIG_SCRIPT_NAME"
#SETUP_SCRIPT_PATH="./$SETUP_SCRIPT_NAME"

# ..............................................................................
# Get client config variables
if ! [ -f "config.dev.sh" ]; then
    echo ""
	echo "ERROR: No 'config.dev.sh' found! Copy config.dev.sh.sample and rename to config.dev.sh. Then update its variables appropriately."
	exit
fi
source config.dev.sh

# ..............................................................................
# Generic ubuntu server instance setup

# We don't want MySQL to prompt for a password,
# so we pass through the env variable DEBIAN_FRONTEND.
# See comments this link below:
#   https://snowulf.com/2008/12/04/truly-non-interactive-unattended-apt-get-install/
#   http://stackoverflow.com/a/8633575/639133
# The following setting apply to apt-get:
#   -y => assume "yes" to everything
#   -q => do it quietly
export DEBIAN_FRONTEND=noninteractive

# ..............................................................................
# Magento specific setup

# Activate apache ssl module
sudo a2enmod ssl

sudo chown ${SERVER_USER}:www-data ${REPOS_WORKING_DIR}

# Setup virtualhost.............................................................

sudo touch ${VHOST_FILE}
sudo chmod o+w ${VHOST_FILE}

echo " \
<VirtualHost *:80>
    ServerName $CLIENT_NAME.dev
    DocumentRoot $REPOS_WORKING_DIR/www

    # Prevent use of Magento Connect
	RedirectMatch permanent ^/downloader/(.*)$ http://$CLIENT_NAME.dev
    RedirectMatch permanent ^/index.php/downloader/(.*)$ http://$CLIENT_NAME.dev

    <Directory $REPOS_WORKING_DIR/www/>
        Options Indexes FollowSymLinks
        Options -MultiViews
        AllowOverride All
    </Directory>
</VirtualHost>

<VirtualHost *:443>

	ServerName $CLIENT_NAME.dev
	DocumentRoot $REPOS_WORKING_DIR/www

    # Prevent use of Magento Connect
    RedirectMatch permanent ^/downloader/(.*)$ http://$CLIENT_NAME.dev
    RedirectMatch permanent ^/index.php/downloader/(.*)$ http://$CLIENT_NAME.dev
    RedirectMatch permanent ^/s2sadmin/extension_custom/(.*)$ http://$CLIENT_NAME.dev
    RedirectMatch permanent ^/index.php/s2sadmin/extension_custom/(.*)$ http://$CLIENT_NAME.dev

	SSLEngine on
	SSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem
	SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key

	BrowserMatch ".*MSIE.*" \
	nokeepalive ssl-unclean-shutdown \
	downgrade-1.0 force-response-1.0

	AllowEncodedSlashes on

	<Directory $REPOS_WORKING_DIR/www>
		Options Indexes FollowSymLinks
		Options -MultiViews
		AllowOverride All
	</Directory>

</VirtualHost>" \
>> ${VHOST_FILE}

sudo chmod o-w ${VHOST_FILE}

sudo a2ensite "$REPOS_NAME.conf"

# Create a MySQL Database and User..............................................

mysql -u root --password=${MYSQL_ROOT_PASSWORD} \
    -e "CREATE DATABASE $MYSQL_MAGENTO_DB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"
mysql -u root --password=${MYSQL_ROOT_PASSWORD} \
    -e "GRANT ALL ON $MYSQL_MAGENTO_DB.* TO '$MYSQL_MAGENTO_USER'@'localhost';"
mysql -u root --password=${MYSQL_ROOT_PASSWORD} \
    -e "FLUSH PRIVILEGES;"

# Download and Set Up Magento Files.............................................

cd ${REPOS_WORKING_DIR}

#sudo wget ${MAGENTO_DOWNLOAD_URL}

# We need strip-components=1 to remove "magento" from the tar file path
sudo tar xzvf ${MAGENTO_DOWNLOAD_FILE} --strip-components=1 -C "$REPOS_WORKING_DIR/www"

# Set privileges as per Magento docs
# We set the privileges as per Magento docs, but on the group.
# This is so that the server user still has access to the files.

# Directories have rx (4+1=5) and files have r (4) for user and group.
# Upper case X applies x to directories but not files.
# http://devdocs.magento.com/guides/m1x/install/installer-privileges_after.html
sudo find ${REPOS_WORKING_DIR} -type f -exec chmod 740 {} \;
sudo find ${REPOS_WORKING_DIR} -type d -exec chmod 750 {} \;
sudo find ${REPOS_WORKING_DIR}/www/var/ -type f -exec chmod 660 {} \;
sudo find ${REPOS_WORKING_DIR}/www/media/ -type f -exec chmod 660 {} \;
sudo find ${REPOS_WORKING_DIR}/www/skin/frontend/smartwave/porto/css/configed/ -type f -exec chmod 660 {} \;
sudo find ${REPOS_WORKING_DIR}/www/var/ -type d -exec chmod 770 {} \;
sudo find ${REPOS_WORKING_DIR}/www/media/ -type d -exec chmod 770 {} \;
sudo find ${REPOS_WORKING_DIR}/www/skin/frontend/smartwave/porto/css/configed/ -type d -exec chmod 770 {} \;
sudo chmod 770 ${REPOS_WORKING_DIR}/www/includes
sudo chmod 660 ${REPOS_WORKING_DIR}/www/includes/config.php
if [ -d "${REPOS_WORKING_DIR}/www/sitemap" ]; then
	sudo chmod 770 ${REPOS_WORKING_DIR}/www/sitemap
fi
if [ -f "${REPOS_WORKING_DIR}/scripts/site_maintenance.sh" ]; then
	sudo chmod 750 ${REPOS_WORKING_DIR}/scripts/site_maintenance.sh
fi

# Install database from repos...................................................

cd ${REPOS_WORKING_DIR}

# TODO Download live DB

# Extract and import the dump file

sudo gunzip "$MAGENTO_DUMP_PREFIX.sql.gz"

mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "source $MAGENTO_DUMP_PREFIX.sql"

# Change email addresses

mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_RECIPIENT' where path = 'contacts/email/recipient_email';"

mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_TRANS' where path = 'trans_email/ident_general/email';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_TRANS' where path = 'trans_email/ident_sales/email';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_TRANS' where path = 'trans_email/ident_support/email';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_TRANS' where path = 'trans_email/ident_custom1/email';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_EMAIL_TRANS' where path = 'trans_email/ident_custom2/email';"

mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SMTP_EMAIL' where path = 'smtppro/general/smtp_username';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SMTP_PASS' where path = 'smtppro/general/smtp_password';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SMTP_HOST' where path = 'smtppro/general/smtp_host';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SMTP_PORT' where path = 'smtppro/general/smtp_port';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SMTP_SSL' where path = 'smtppro/general/smtp_ssl';"

# SES details
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SES_ACCESS_KEY' where path = 'smtppro/general/ses_access_key';"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = '$MAGENTO_SES_PRIVATE_KEY' where path = 'smtppro/general/ses_private_key';"

# Change base URLs

mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = 'http://$MAGENTO_BASE_DOMAIN/' where path = 'web/unsecure/base_url'"
mysql -u ${MYSQL_MAGENTO_USER} --password=${MYSQL_MAGENTO_PASSWORD} --database=${MYSQL_MAGENTO_DB} \
    -e "update core_config_data set value = 'https://$MAGENTO_BASE_DOMAIN/' where path = 'web/secure/base_url'"

# TODO Disable webhook, Empty mail queue, Change contacts

# Flush sessions and cache

rm -rf ${REPOS_WORKING_DIR}/www/var/cache/*

# Add required lines to crontab.................................................

CRON_MODE="always"
CRON_LINE="* * * * * ! test -e $REPOS_WORKING_DIR/www/maintenance.flag && /bin/bash $REPOS_WORKING_DIR/www/scheduler_cron.sh --mode $CRON_MODE"
(sudo crontab -u www-data -l ; echo "$CRON_LINE" ) | sudo crontab -u www-data -

CRON_MODE="default"
CRON_LINE="* * * * * ! test -e $REPOS_WORKING_DIR/www/maintenance.flag && /bin/bash $REPOS_WORKING_DIR/www/scheduler_cron.sh --mode $CRON_MODE"
(sudo crontab -u www-data -l ; echo "$CRON_LINE" ) | sudo crontab -u www-data -

# Add cron to re-index products at 3am every morning (commented out by default)
CRON_LINE="#0 3 * * * php $REPOS_WORKING_DIR/www/shell/indexer.php --reindex catalog_category_product"
(sudo crontab -u www-data -l ; echo "$CRON_LINE" ) | sudo crontab -u www-data -

# Add check to start mysql if it is not running
CRON_LINE="* * * * * $REPOS_WORKING_DIR/scripts/site_maintenance.sh"
echo "$CRON_LINE" | sudo crontab -

# Restart apache................................................................

sudo service apache2 restart

cd ${REPOS_WORKING_DIR}/www

# Run magento patches ..........................................................
# A user defined variable in bash cannot start with a digit
if [ "version-${MAGENTO_VERSION}" = "version-1.9.1.0" ]
then
	# Make patch scripts executable
	sudo chmod +x PATCH_SUPEE-*.sh

	sudo sh ./PATCH_SUPEE-5344_CE_1.8.0.0_v1-2015-02-10-08-10-38.sh
	sudo sh ./PATCH_SUPEE-5994_CE_1.6.0.0_v1-2015-05-15-04-34-46.sh
	sudo sh ./PATCH_SUPEE-6237_EE_1.14.2.0_v1-2015-06-18-05-24-23.sh
	sudo sh ./PATCH_SUPEE-6285_CE_1.9.1.1_v2-2015-07-08-08-07-43.sh
	sudo sh ./PATCH_SUPEE-6482_CE_1.9.2.0_v1-2015-08-03-06-51-10.sh
	sudo sh ./PATCH_SUPEE-6788_CE_1.9.1.0_v1-2015-10-27-09-06-11.sh
	sudo sh ./PATCH_SUPEE-7616_CE_1.9.2.2-CE_1.8.0.0_v1-2016-01-20-03-08-56.sh
	sudo sh ./PATCH_SUPEE-7405_CE_1.9.1.1_v1-2016-01-20-04-42-03.sh
	sudo sh ./PATCH_SUPEE-8788_CE_1.9.1.0_v1-2016-10-11-06-57-54.sh
fi

# Run patches for version 1.9.2.2...............................................
if [ "version-${MAGENTO_VERSION}" = "version-1.9.2.2" ]
then
	# Make patch scripts executable
	sudo chmod +x PATCH_SUPEE-*.sh

	sudo sh ./PATCH_SUPEE-7616_CE_1.9.2.2-CE_1.8.0.0_v1-2016-01-20-03-08-56.sh
	sudo sh ./PATCH_SUPEE-7405_CE_1.9.2.2_v1-2016-01-20-04-35-33.sh
	sudo sh ./PATCH_SUPEE-7405_CE_1.9.2.2_v1.1-2016-02-23-07-44-38.sh
	sudo sh ./PATCH_SUPEE-8788_CE_1.9.2.2_v1-2016-10-11-07-01-37.sh
fi

# Run patches for version 1.9.2.4...............................................
if [ "version-${MAGENTO_VERSION}" = "version-1.9.2.4" ]
then
	# Make patch scripts executable
	sudo chmod +x PATCH_SUPEE-*.sh

	sudo sh ./PATCH_SUPEE-8788_CE_1.9.2.4_v1-2016-10-11-07-03-46.sh
fi