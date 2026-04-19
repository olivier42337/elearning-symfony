# 🎓 E-Learning Symfony

Une plateforme d’apprentissage en ligne développée avec **Symfony 7**, **Doctrine ORM**, **Twig** et **Bootstrap 5**.

Ce projet permet à des **instructeurs** de créer et gérer leurs cours, et à des **étudiants** de s’inscrire, suivre des leçons et répondre à des quiz.

---

## ✨ Fonctionnalités

- 🔐 Authentification sécurisée (inscription / connexion / déconnexion)
- 👨‍🎓 Gestion des rôles : **Étudiant** et **Instructeur**
- 📚 Création, modification et suppression de cours
- 📖 Gestion des leçons associées aux cours
- 📝 Inscription aux cours
- ❓ Système de quiz avec correction automatique
- 👤 Gestion du profil utilisateur
- 🏠 Landing page professionnelle
- 📋 Tableau de bord étudiant
- 🧑‍🏫 Tableau de bord instructeur

---

## 🛠️ Technologies utilisées

- **PHP 8.2**
- **Symfony 7**
- **Doctrine ORM**
- **MySQL**
- **Twig**
- **Bootstrap 5.3**

---

## 👥 Rôles utilisateur

### Étudiant
- s’inscrire et se connecter
- voir les cours disponibles
- s’inscrire à un cours
- consulter les leçons
- passer les quiz

### Instructeur
- créer ses cours
- modifier ses cours
- supprimer ses cours
- gérer les leçons
- consulter les inscriptions liées à ses cours

---

## ⚙️ Installation locale

### Prérequis

- PHP 8.2+
- Composer
- MySQL / MariaDB
- Symfony CLI
- XAMPP (si environnement local Windows)

### 1. Cloner le projet

```bash
git clone https://github.com/olivier42337/elearning-symfony.git
cd elearning-symfony
### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer l'environnement
Copie le fichier `.env` et crée un `.env.local` :
```bash
cp .env .env.local
```
Modifie la ligne `DATABASE_URL` :
### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

### 5. Lancer le serveur
```bash
symfony server:start
```

Ouvre **http://localhost:8000** 🚀