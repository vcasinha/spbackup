# spBackup

## Overview

**spBackup** a script to control your stored procedures under git

This very basic and yet limited script allows dump and automatic source control for the lazy people.

It was inspired on a quite old version of a similar task, where I needed to keep track of router configurations that were changed by many different people.

So, resuming, it will dump all stored procedures into folders, create git repositories of these git folders and commit each time it is executed.

### Settings

The settings.ini file contains a very basic configuration where we can set the script up.

	;Where you want your databases dump
	backup_location=/var/lib/backup/
	;Username and password to access the database
	db_username = sa
	db_password = sql
	;As many databases as you want (on the same server or using the same account)
	dsn[database1]="dblib:host=hostname;dbname=database"
	dsn[database2]="dblib:host=hostname;dbname=database"
	dsn[database3]="dblib:host=hostname;dbname=database"
	dsn[databasen]="dblib:host=hostname;dbname=database"
	
### How to

You should start everything once with the following command:

	#php spBackup.php dump

This will dump and initialize the repositories

Then you can set it up on crontab

	59 23 * * * /usr/bin/php /opt/spBackup/spBackup.php
	
### Limitations

It is very limited, but exists and works!