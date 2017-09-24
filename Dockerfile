FROM docker.io/sroze/cp-demo-magento1:ec5c5b69cb9353e67b8e0ff5423393395367cf38

COPY . /app/module
COPY tools/docker/usr/ /usr/
COPY tools/docker/etc/ /etc/

ARG GITHUB_TOKEN=

RUN bash /app/module/tools/docker/usr/local/share/container/plan.sh
