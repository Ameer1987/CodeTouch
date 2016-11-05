# CodeTouch
Interview test

Steps to test the project:

1- Import the database to your localhost.

2- Download the project from github using git clone or download as zip then extract the file.

3- Open terminal and browse to the project folder "Test".

4- Run the following commands 
    -> curl http://getcomposer.org/installer | php

    -> ./composer.phar install --prefer-dist
    (at the end of this step you will need only to specify the db name, user and pass)

    -> bin/console server:run

(Don't close the terminal)

5- Open any browser and go to this url "localhost:8000", 

6- Create users as you need and proceed with testing.
