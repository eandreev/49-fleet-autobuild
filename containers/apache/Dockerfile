FROM ubuntu:14.04

# install apache
RUN apt-get update
RUN apt-get install apache2 -y

# modify index.html
RUN echo "<h1>Running from Docker on CoreOS "`date '+%Y-%m-%d %H:%M:%S'`"</h1>" > /var/www/html/index.html

# create the build date file
RUN date > /built-at.txt

EXPOSE 80

CMD ["/bin/bash"]
