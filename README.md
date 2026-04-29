#  EcoGarden & Co — API REST Symfony

EcoGarden & Co est une API REST développée avec Symfony permettant :
- la gestion d’utilisateurs,
- la récupération de conseils de jardinage,
- la récupération de données météo via une API publique,
- la gestion avancée des conseils pour les administrateurs.

---

##  Technologies utilisées
- Symfony 6
- PHP 8+
- Doctrine ORM
- JWT Authentication (LexikJWT)
- OpenWeatherMap API
- MySQL / MariaDB

---

##  Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


 Auteur
Développé par Aly dans le cadre du projet OpenClassrooms.# OC12



#!/bin/bash

BASE_URL="https://127.0.0.1:8001"

echo "=== 1) Création d'un utilisateur ==="
curl -k -X POST $BASE_URL/user \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"1234","ville":"Paris"}'
echo -e "\n"

echo "=== 2) Authentification ==="
TOKEN=$(curl -k -s -X POST $BASE_URL/auth \
    -H "Content-Type: application/json" \
    -d '{"email":"test@test.com","password":"1234"}' | jq -r '.token')

echo "TOKEN UTILISATEUR : $TOKEN"
echo -e "\n"

echo "=== 3) Conseils du mois en cours ==="
curl -k -X GET $BASE_URL/conseil \
    -H "Authorization: Bearer $TOKEN"
echo -e "\n"

echo "=== 4) Conseils du mois 3 ==="
curl -k -X GET $BASE_URL/conseil/3 \
    -H "Authorization: Bearer $TOKEN"
echo -e "\n"

echo "=== 5) Météo de Paris ==="
curl -k -X GET $BASE_URL/meteo/Paris \
    -H "Authorization: Bearer $TOKEN"
echo -e "\n"

echo "=== 6) Météo de l'utilisateur ==="
curl -k -X GET $BASE_URL/meteo \
    -H "Authorization: Bearer $TOKEN"
echo -e "\n"

echo "=== 7) Authentification ADMIN ==="
TOKEN_ADMIN=$(curl -k -s -X POST $BASE_URL/auth \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@test.com","password":"admin"}' | jq -r '.token')

echo "TOKEN ADMIN : $TOKEN_ADMIN"
echo -e "\n"

echo "=== 8) Ajouter un conseil (ADMIN) ==="
curl -k -X POST $BASE_URL/conseil \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN_ADMIN" \
    -d '{"contenu":"Planter les tomates","mois":[3,4,5]}'
echo -e "\n"

echo "=== 9) Modifier un conseil (ADMIN) ==="
curl -k -X PUT $BASE_URL/conseil/1 \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN_ADMIN" \
    -d '{"contenu":"Arroser tôt le matin","mois":[4,5]}'
echo -e "\n"

echo "=== 10) Supprimer un conseil (ADMIN) ==="
curl -k -X DELETE $BASE_URL/conseil/1 \
    -H "Authorization: Bearer $TOKEN_ADMIN"
echo -e "\n"

echo "=== 11) Modifier un utilisateur (ADMIN) ==="
curl -k -X PUT $BASE_URL/user/1 \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN_ADMIN" \
    -d '{"ville":"Lyon"}'
echo -e "\n"

echo "=== 12) Supprimer un utilisateur (ADMIN) ==="
curl -k -X DELETE $BASE_URL/user/1 \
    -H "Authorization: Bearer $TOKEN_ADMIN"
echo -e "\n"

echo "=== 13) Test erreur : sans token ==="
curl -k -X GET $BASE_URL/conseil
echo -e "\n"

echo "=== 14) Test erreur : accès interdit (403) ==="
curl -k -X POST $BASE_URL/conseil \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d '{"contenu":"Test","mois":[1]}'
echo -e "\n"

echo "=== 15) Test erreur : mois invalide ==="
curl -k -X GET $BASE_URL/conseil/99 \
    -H "Authorization: Bearer $TOKEN"
echo -e "\n"

echo "=== FIN DES TESTS ==="

