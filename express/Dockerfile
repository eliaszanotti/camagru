FROM	node:23-alpine

WORKDIR /usr/src/app

COPY	package.json .
RUN		npm install
RUN		npm run build:css
COPY	. .

EXPOSE	3000

CMD		["npm", "start"]