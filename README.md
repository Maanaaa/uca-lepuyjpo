# Symfony & Next.js Docker Boilerplate

Ce projet est un monorepo fullstack utilisant **Symfony 7** (API Platform) pour le backend et **Next.js 15** pour le frontend, le tout orchestré par Docker avec **FrankenPHP**.

## 🚀 Installation Rapide

### 1. Cloner le projet
\```bash
git clone [https://github.com/Maanaaa/symfony-next-boilerplate.git](https://github.com/Maanaaa/symfony-next-boilerplate.git)
cd symfony-next-boilerplate
\```

### 2. Configurer les variables d'environnement
\```bash
cp .env.example .env
cp app/api/.env.example app/api/.env
cp app/client/.env.example app/client/.env
\```

### 3. Lancer le projet
\```bash
cd docker
docker-compose up -d --build
\```
*Note : Au premier lancement, Docker installera automatiquement les dépendances (Composer & NPM). Cela peut prendre 1 à 2 minutes. Suivez l'avancée avec `docker logs -f symfo-next-core-api`.*

### 4. Initialiser la base de données
Une fois que les containers sont prêts :
\```bash
docker exec -it symfo-next-core-api php bin/console doctrine:migrations:migrate --no-interaction
\```

---

## 🌍 Accès aux services

* **Frontend (Next.js)** : [http://localhost:3000](http://localhost:3000)
* **Backend API (Swagger)** : [http://localhost:8000/api](http://localhost:8000/api)
* **Base de données** : `localhost:3306`

---

## 🛠 Commandes Utiles

| Action | Commande |
| :--- | :--- |
| Voir les logs (Installation/Erreurs) | `docker-compose logs -f` |
| Créer une entité | `docker exec -it symfo-next-core-api php bin/console make:entity` |
| Créer une migration | `docker exec -it symfo-next-core-api php bin/console make:migration` |