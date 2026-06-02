FROM node:20-alpine

WORKDIR /app

# ─── Instalar dependencias primero (capa cacheada) ───────────────────────────
COPY package*.json ./
RUN npm install

# ─── Copiar fuentes ───────────────────────────────────────────────────────────
COPY . .

EXPOSE 5173

# Hot reload con polling (necesario en Docker en Windows/WSL)
CMD ["npm", "run", "dev"]
