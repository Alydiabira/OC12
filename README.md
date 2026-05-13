Auto‑évaluation du projet — GreenGoodies (Symfony)
1. Développement du back‑end Symfony
Fonctionnalités attendues
Site fonctionnel (aucune erreur au chargement)
Liste des produits affichée sur la page d’accueil
Inscription utilisateur
Connexion utilisateur
Panier fonctionnel : ajout, consultation, validation
Historique des commandes
Activation/désactivation de l’accès API depuis le profil
Composants Symfony utilisés
Symfony 6+
Forms + Validators
Request pour la récupération des données
Security pour l’authentification
Fixtures pour les données produits
Router Symfony
Composer pour les dépendances
Code commenté
Optimisation Green Code (images optimisées, assets minifiés)
2. Base de données & Doctrine
Structure conforme
Doctrine ORM
Tables :
utilisateurs
produits
commandes
Pas de données dupliquées
3. Vues Twig & affichage
Conformité
Vues générées avec Twig
Héritage de templates (base.html.twig)
Formulaires affichés via form_*
Respect des maquettes
4. API Symfony (JWT)
Fonctionnalités API
Authentification JWT via /api/login
Récupération des produits via /api/products
Routes conformes au document technique
Accès API conditionné à l’activation dans le profil utilisateur
Serializer + groupes
Fonctionnalités principales
Côté utilisateur
Inscription / Connexion
Consultation des produits
Panier + validation
Historique des commandes
Activation API
API sécurisée
/api/login
/api/products
Accès conditionné
Technique
Symfony 6+
Doctrine ORM
Fixtures
Serializer (Groupes)
JWT (LexikJWTAuthenticationBundle)
Assets optimisés
Installation
1. Cloner le projet
bash
git clone https://github.com/votre-repo/greengoodies.git
cd greengoodies
2. Installer les dépendances
bash
composer install
3. Configurer l’environnement
bash
cp .env .env.local
Modifier la ligne :
Code
DATABASE_URL="mysql://root:password@127.0.0.1:3306/greengoodies?serverVersion=8.0"
4. Préparer la base de données
bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load -n
5. Lancer le serveur
bash
symfony server:start -d
Accès :
Code
https://127.0.0.1:8001/
Tests Web (cURL)
Page d’accueil
bash
curl -k https://127.0.0.1:8001/
Inscription
bash
curl -k -X POST https://127.0.0.1:8001/register \
 -H "Content-Type: application/x-www-form-urlencoded" \
 -d "registration_form[lastname]=Test&registration_form[firstname]=User&registration_form[email]=test1@gmail.com&registration_form[password]=password&registration_form[confirmPassword]=password&registration_form[acceptCgu]=1"
Connexion
bash
curl -k -c cookies.txt -b cookies.txt -X POST https://127.0.0.1:8001/login \
 -H "Content-Type: application/x-www-form-urlencoded" \
 -d "_username=test1@gmail.com&_password=password"
Ajouter au panier
bash
curl -k -c cookies.txt -b cookies.txt https://127.0.0.1:8001/panier/ajouter/1
Afficher le panier
bash
curl -k -c cookies.txt -b cookies.txt https://127.0.0.1:8001/panier
Valider le panier
bash
curl -k -c cookies.txt -b cookies.txt https://127.0.0.1:8001/panier/valider
Historique commandes
bash
curl -k -c cookies.txt -b cookies.txt https://127.0.0.1:8001/mon-compte
Toggle API
bash
curl -k -c cookies.txt -b cookies.txt -X POST https://127.0.0.1:8001/mon-compte/api/toggle
Tests API (cURL)
Login API
bash
curl -k -X POST https://127.0.0.1:8001/api/login \
 -H "Content-Type: application/json" \
 -d '{"username":"test1@gmail.com","password":"password"}'
Mauvais mot de passe
bash
curl -k -X POST https://127.0.0.1:8001/api/login \
 -H "Content-Type: application/json" \
 -d '{"username":"test1@gmail.com","password":"wrong"}'
Accès API désactivé (403 attendu)
bash
curl -k -X POST https://127.0.0.1:8001/api/login \
 -H "Content-Type: application/json" \
 -d '{"username":"test1@gmail.com","password":"password"}'
Récupérer les produits avec JWT
bash
TOKEN=$(curl -sk -X POST https://127.0.0.1:8001/api/login \
 -H "Content-Type: application/json" \
 -d '{"username":"test1@gmail.com","password":"password"}' | jq -r .token)

curl -k https://127.0.0.1:8001/api/products \
 -H "Authorization: Bearer $TOKEN"
Token invalide
bash
curl -k https://127.0.0.1:8001/api/products \
 -H "Authorization: Bearer INVALID"
Vérifications techniques
Valider le schéma Doctrine
bash
php bin/console doctrine:schema:validate
Recharger les fixtures
bash
php bin/console doctrine:fixtures:load -n
Vérifier les assets
bash
ls -lh public/assets
Vérifier les images optimisées
bash
ls -lh public/uploads
Auteur
Développé par Aly Diabira dans le cadre du parcours OpenClassrooms — Projet Symfony.
