

git clone [path]
../composer.phar install

add apache to developer

chown -R apache:developer tmp
chown -R apache:developer logs

assign 

$ umask 002            # allow group write; everyone must do this
$ chgrp developer .            # set directory group to G
$ chmod g+s .          # files created in directory will be in group G

setfacl -dm u::rw,g::rw,o::r tmp


useradd -a -G apache dev7modop


[dev7modop@fotokemweb cache]$ sudo usermod -a -G apache dev7modop
[dev7modop@fotokemweb cache]$ sudo id dev7modop
uid=505(dev7modop) gid=506(dev7modop) groups=506(dev7modop),10(wheel),48(apache),500(developer)


Develoment Receipe

git clone https://github.com/guanchor/cake3-adminlte23.git fmt_v6
../composer.phar install
../composer.phar require dereuromark/cakephp-tinyauth:dev-master 
../composer.phar require dereuromark/cakephp-tools:dev-master


$ bin/cake migrations migrate

bin/cake bake all [model_names] -f -t AdminLTEBakeOverride


etc:=
dereuromark/cakephp-tools suggests installing yangqi/htmldom (For HtmlDom usage)
symfony/console suggests installing symfony/event-dispatcher ()
symfony/console suggests installing symfony/process ()
symfony/var-dumper suggests installing ext-symfony_debug ()
psy/psysh suggests installing ext-pdo-sqlite (The doc command requires SQLite to work.)
cakephp/debug_kit suggests installing ext-sqlite (DebugKit needs to store panel data in a database. SQLite is simple and easy to use.)

in centos system

given httpd process is run with user apache and group apache

set umask for httpd process to 002
echo "umask 002" >> /etc/sysconfig/httpd
service httpd restart

change sftp account default group to apache
usermod -g primarygroupname username
usermod -g apache saifuddin

set umask for user saifuddin
nano ~/.bashrc

add

umask 002 
