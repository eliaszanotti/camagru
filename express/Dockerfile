FROM node:23-alpine

WORKDIR /usr/src/app

RUN npm install -g pnpm
COPY package.json ./
RUN pnpm install
COPY . .

EXPOSE 3000

CMD ["pnpm", "start"]
# CMD ["tail", "-f", "/dev/null"]