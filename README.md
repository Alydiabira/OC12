# 🌱 EcoGarden & Co — API REST Symfony

EcoGarden & Co est une API REST développée avec Symfony permettant :
- la gestion d’utilisateurs,
- la récupération de conseils de jardinage,
- la récupération de données météo via une API publique,
- la gestion avancée des conseils pour les administrateurs.

---

## 🚀 Technologies utilisées
- Symfony 6
- PHP 8+
- Doctrine ORM
- JWT Authentication (LexikJWT)
- OpenWeatherMap API
- MySQL / MariaDB

---

## 📦 Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


👤 Auteur
Développé par Aly dans le cadre du projet OpenClassrooms.