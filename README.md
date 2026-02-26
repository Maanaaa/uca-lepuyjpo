# 🚀 Fullstack Starter Kit: Symfony (API Platform) & Next.js

Ce boilerplate est un environnement de développement utilisant **Docker**, **FrankenPHP** et **MariaDB**.

---

## 🏗️ Architecture de la Stack
- **API** : Symfony 7 + API Platform (Port 8080)
- **Front** : Next.js (Port 3000)
- **Database** : MariaDB (Port 3306)
- **Admin DB** : phpMyAdmin (Port 8081)

---

## 🛠️ Pré-requis
Avant de commencer, assure-toi d'avoir installé sur ta machine :
1. **Docker Desktop**
2. **Git**

---

## 📥 Installation Étape par Étape

### 1. Cloner le projet
git clone (https://github.com/Maanaaa/symfony-next-boilerplate.git)
cd symfony-next-boilerplate

### 2. Configurer les variables d'environnement
Copie le fichier d'exemple (les valeurs par défaut sont déjà configurées pour fonctionner immédiatement) :
cp .env.example .env

### 3. Lancer Docker
Cette commande construit les images et démarre tous les services en arrière-plan :
docker compose up -d --build

### 4. Installer les dépendances
Les dossiers vendor/ et node_modules/ sont ignorés par Git. Tu dois les installer à l'intérieur des containers :

# Pour le Back-end (Symfony)
docker compose exec api composer install

# Pour le Front-end (Next.js)
docker compose exec client npm install

### 5. Initialiser la Base de Données
# Créer la DB
docker compose exec api php bin/console doctrine:database:create --if-not-exists

# Appliquer les migrations
docker compose exec api php bin/console doctrine:migrations:migrate --no-interaction

---

## 🖥️ Accès aux services

| Service | URL | Note |
| :--- | :--- | :--- |
| **Front-end** | http://localhost:3000 | Next.js (Hot Reload activé) |
| **API Docs** | http://localhost:8080/api | Swagger / API Platform |
| **phpMyAdmin** | http://localhost:8081 | Login: root / mdp du .env |

---

## 🛠️ Commandes de Développement

### Créer une nouvelle Entité (API Platform)
docker compose exec api php bin/console make:entity --api-resource

### Mettre à jour la Base de Données
docker compose exec api php bin/console make:migration
docker compose exec api php bin/console doctrine:migrations:migrate

### Gestion Docker au quotidien
- **Arrêter le projet** : docker compose stop
- **Relancer le projet** : docker compose start
- **Voir les logs** : docker compose logs -f
- **Vider le cache Symfony** : docker compose exec api php bin/console cache:clear

---

## ⚠️ Notes de configuration
- **CORS** : Déjà configuré pour autoriser localhost:3000.
- **Database** : Si tu as une erreur de connexion au premier lancement, attends 10 secondes que MariaDB finisse son initialisation et relance la commande.

---
