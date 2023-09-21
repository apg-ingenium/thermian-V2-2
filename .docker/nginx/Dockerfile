ARG NGINX_VER
FROM nginx:${NGINX_VER} as development
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80 443


ARG NGINX_VER
FROM nginx:${NGINX_VER} as production
COPY .docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY Thermian/Web/webroot /app/Thermian/Web/webroot
EXPOSE 80 443
