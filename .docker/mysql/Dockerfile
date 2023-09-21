ARG MYSQL_VER
FROM mysql:${MYSQL_VER}
COPY .docker/mysql/mysql.cnf /etc/mysql/conf.d/mysql.cnf
COPY .docker/mysql/schema.sql /docker-entrypoint-initdb.d
EXPOSE 3306