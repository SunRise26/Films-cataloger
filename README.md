# Webbylab test project
![alt text](https://github.com/SunRise26/webbylab-test/blob/assets/example-1.png?raw=true)
## Installation:
1. Build and run docker containers<br/>
RUN (cd docker && docker-compose up --build)<br/>
!!! MySql need some time to initialize on first launch (takes about 0-3 minutes). Please, wait for the following message:
[Server] /usr/sbin/mysqld: ready for connections. Version: '8.0.22'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL.

2. Generate composer autoload files<br/>
RUN (cd docker && docker-compose exec php composer install)

3. Copy .env settings<br/>
RUN (cd webbylab-test && cp .env.example .env)

4. Setup db tables<br/>
RUN (cd docker && docker-compose exec php php scripts/db-setup.php)

## Design:
Project built as MVC system to separate work with database, frontend templates and requests.

* 'src/boostrap.php' - prepare project environment, routes and autoload
* 'src/routes.php' - routes registrar
* 'src/core/' - contain parent MVC classes, router and database connector.
* 'src/models/' - models
* 'src/views/' - views
* 'src/controllers/' - controllers
* 'src/templates/' - view php templates
* 'scripts/' - dir for php scripts (for manual or cron usage)
* 'resources/' - (js, sass, css)
* 'public/' - public directory

## Import file text example:
___
Title: Gladiator<br/>
Release Year: 2000<br/>
Format: Blu-Ray<br/>
Stars: Russell Crowe, Joaquin Phoenix, Connie Nielson

Title: Star Wars<br/>
Release Year: 1977<br/>
Format: Blu-Ray<br/>
Stars: Harrison Ford, Mark Hamill, Carrie Fisher, Alec Guinness, James Earl Jones

Title: Raiders of the Lost Ark<br/>
Release Year: 1981<br/>
Format: DVD<br/>
Stars: Harrison Ford, Karen Allen
___

## Possible improvements:
1. Handle mysql "SELECT" limits, currently used defaults. (4096 columns per table). For example, use page listing for films list.
2. Check for vulnerabilities. Possibly, I've missed smth in a hurry. (e.g. maybe somewhere missed sql injections)
3. Move part of functions from model and controllers to some kind of "helpers";
4. Improve searching (e.g. generating search map, using search libraries ...)
5. To handle huge import files would be better to save them and process using cron schedule.
