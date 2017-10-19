# Magento default theme (uses Porto latest)

[Porto theme](http://newsmartwave.net)

This repos contains scripts to setup and deploy new magento instances on EC2

## Setup new EC2 Instance
1. Clone project onto your local system
```
git clone github.com/xyz.git
```
2. Navigate into the *scripts* folder make a copy of **config.sh.sample** and rename it to **config.sh**
```
cd scripts
cp config.sh.sample config.sh
```
3. Change the variables values in the config.sh file to the appropriate values
4. Run the remote setup script to setup the Amazon EC2 instance
```
sh remoteSetupInstance.sh KEYFILE SERVER_IP CLIENT_NAME GIT_USERNAME GIT_PASSWORD
```
  * *KEYFILE* - Location of private key file
  * *SERVER_IP* - The ip of the EC2 instance or the domain name (e.g. mydomain.com)
  * *CLIENT_NAME* - The name of the project/client. Usually whatever is followed after **magento_** in the repos name. So if repos name is *magento_gomedia* then CLIENT_NAME would be *gomedia*
  * *GIT_USERNAME* - This is your github.com username (not stored and simply used during the cloning process)
  * *GIT_PASSWORD* - This is your github.com password (not stored and simply used during the cloning process)

**Once the script finishes you should immediately be able to access the site.**

TODO document instructions on creating new IAM user and setting permissions

EC2 server environment:-
- Ubuntu 14.04
- MySQL 5.5
- Php 5.5
- Apache2

## Add user to server
1. Login to server
2. Create the new user with appropriate permissions (*username* refers to the actual username for the user e.g. john)
```
sudo adduser username
sudo usermod -aG sudo username
sudo su - username
mkdir .ssh
chmod 700 .ssh
```
3. Add your username as a sudo user so you can run sudo commands without having to type in your password
```
echo 'username  ALL=(ALL:ALL) ALL' >> /etc/sudoers
```
4. Create the authorised keys file (if it doesn't already exist) with appropriate permissions
```
touch .ssh/authorized_keys
chmod 600 .ssh/authorized_keys
```
5. On your local machine generate public key from the private key file
```
ssh-keygen -y -f ~/privatekey.pem ~/.ssh/id_rsa.pub
```
6. Copy contents of public key file and login to server again
7. Add public key to .ssh/authorized_keys on the server and save
8. Add friendly name so you can easily see what server you are on:
  * Edit .profile file
```
vi ~/.profile
```
  * Add the following to .profile and save (Change *ClientName* to the name of the client)
```
PS1="
<\[^[[0;32m\]Magento ClientName[^[[0m\]:\[^[[0;37m\]\u\[^[[0m\]> \j [\$(date +%d-%m\" \"%H:%M)] \w
! "
```

## Setup Dev environment
1. clone repose
```
git clone github.com/xyz.git
```
2. Navigate into the *scripts* folder make a copy of **config.sh.sample** and rename it to **config.sh**
```
cp scripts/client.dev.sh.sample scripts/client.dev.sh
```

## Deploy changes to production environment

Do the following after committing and pushing your changes to Git.

1. Navigate into the *scripts* folder and run the remote deployment script
```
sh remoteDeploy.sh KEYFILE SERVER_IP CLIENT_NAME TARGET
```

  * *KEYFILE* - Location of private key file
  * *SERVER_IP* - The ip of the EC2 instance or the domain name (e.g. mydomain.com)
  * *CLIENT_NAME* - The name of the project/client. Usually whatever is followed after **magento_** in the repos name. So if repos name is *magento_gomedia* then CLIENT_NAME would be *gomedia*
  * *TARGET* - Either a tag name (v1.2.1) or a remote branch name (origin/master), but could also be a commit hash or anything else Git recognizes as a revision.



Disable webhook
Empty mail queue
change contacts


## Go live script
TODO

