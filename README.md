# HintHub

This system should provide the University IUBH with Quality Management capabilites, so that Students can easily report mistakes in for example Module Scripts.

It uses Symfony 5.3+ and EasyAdmin 3.x.  Symfony uses Doctrine 2, the Twig Templating Engine and other various packages (upgradeable with composer, npm and yarn).

![Screenshot_Login](screenshots/1.png?raw=true=250x250)

## Installation

For the Installation, please look at 

https://github.com/HintHub/hh-prodstack

## Upgrades

### Downloading new Pushed Files (upgrading the Software itself)

```bash
git fetch
git pull
#git reset -head HARD 
```




### Packages

For upgrades you need to run the following commands

**jump into the php-fpm container**

`sudo docker exec -it testprojekt_php-fpm_1 "bash"`

**and run the upgrades (composer, npm, yarn)**

`cd /var/www/ && composer upgrade && npm upgrade && yarn upgrade`



Also, a cache:clear `php /var/www/bin/console cache:clear` inside the php-fpm container is recommended

Optionally you can add a Cronjob (crontab -e) for the automation of escalateFehler (escalaiting long unedited Fehlermeldungen)

add to `/etc/crontab` (every 5 min means /5)

```bash
# /etc/crontab
*/5 * * * * docker exec -it <stackName>_php-fpm_1 bash -c "php /var/www/bin/console escalate" 
```

add sudo before the command if necessary

### DB Changes (Entities/Schema)

If you upgrade the DB schema, please regenerate the database:

use the following commands:

```bash
#!/bin/bash
sudo docker exec -it <stackName>_php-fpm_1 php /var/www/bin/console doctrine:database:drop --force -n
sudo docker exec -it <stackName>_php-fpm_1 php /var/www/bin/console doctrine:database:create -n
sudo docker exec -it <stackName>_php-fpm_1 php /var/www/bin/console doctrine:migrations:migrate -n
sudo docker exec -it <stackName>_php-fpm_1 php /var/www/bin/console doctrine:fixtures:load -n

```

## Helper Scripts

### Production

In Production you should use the Helper Scripts of the HH-Prod-Stack.

### Dev-VM

Inside the Dev VM it's recommended to use the prebuild Helper Scripts

```bash
/bin/restartdockercontainer
/usr/bin/rebuilddatabase
/usr/bin/clearsymfonycache
/usr/bin/jumpinphpfpm
/usr/bin/jumpinmysqlshell
```

The names are descriptive therefore any further explaination is not available.



***HintHub Dev Team 01.02.2022***
