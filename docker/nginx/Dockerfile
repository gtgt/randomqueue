FROM nginx:alpine
ARG MAX_EXECUTION_TIME
ARG SERVER_NAME
ARG WWW_ROOT_DIR

# Install nginx
#RUN apt-get update && apt-get install -y nginx
RUN apk add --no-cache shadow bash

# Configure Nginx
ADD nginx.conf /etc/nginx/
RUN sed "/fastcgi_read_timeout 60s;/c\  fastcgi_read_timeout ${MAX_EXECUTION_TIME}s;" -i /etc/nginx/nginx.conf
ADD randomqueue.conf /etc/nginx/sites-available/
RUN sed "/server_name host.dev;/c\  server_name ${SERVER_NAME};" -i /etc/nginx/sites-available/randomqueue.conf
RUN sed "/root \/var\/www\/public;/c\  root \/var\/www\/${WWW_ROOT_DIR};" -i /etc/nginx/sites-available/randomqueue.conf
RUN echo "upstream php-upstream { server php-fpm:9000; }" > /etc/nginx/conf.d/upstream.conf
RUN usermod -u 1000 nginx

# Configure the virtual host
RUN mkdir -p /etc/nginx/sites-available
RUN mkdir -p /etc/nginx/sites-enabled
RUN ln -s /etc/nginx/sites-available/randomqueue.conf /etc/nginx/sites-enabled/randomqueue
RUN rm /etc/nginx/conf.d/default.conf

# Run Nginx
CMD ["nginx"]

# Expose ports
EXPOSE 80
EXPOSE 443
