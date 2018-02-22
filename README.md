# **README**

### **Install**

- run "composer install" at project folder
- copy sample configuration fom template (run command "cp config/__main.php config/main.php" in the project root)

### **Config**

##### _SMTP params:_
##### _SMTP sample in /vagrant/sample_main.php_
   
- "host" - Specify main and backup SMTP servers
- "port" - TCP port to connect to
- "username" - SMTP username
- "password" - SMTP password
- "wordwrap" - word wrap parameter
- "smtpsecure" - secure type 

##### _Sender params:_
- "email" - addresser e-mail
- "name" - addresser name
   	
##### _Recipient params:_
   	
- "email" - addressee e-mail
   	  	
### **About**

- public - folder with index.php
- config - folder for main config
- vendor - folder with installed by composer vendor code
- composer.json, composer.lock - composer instructions
##
