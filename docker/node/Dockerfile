FROM node:latest
WORKDIR src/app

RUN npm ci
RUN npm install -g @angular/cli
COPY ../../client /src
RUN npm install

CMD ng serve --host 0.0.0.0
EXPOSE 4200