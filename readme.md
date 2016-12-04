# LoveLetter
Projet L3 WEB
Groupe : Guillaume PETIT - Simon HAJEK

Frameworks utilisés : Laravel (Slim pour les routes ELOQUENT pour l'accès bdd)
et les facades proposées par défaut dans Laravel

BDD :
DB_CONNECTION=mysql
DB_HOST=172.18.0.3 //Attention voir en dessous pour la config
DB_PORT=3306
DB_DATABASE=love_letter
DB_USERNAME=root
DB_PASSWORD=root

Tout est configuré avec Docker (laradock)

Marche à suivre pour setup le projet : 

Aller dans le dossier laradock.

faire : 

docker-compose up -d apache2 mysql

Récupérer l'ip du container mysql faire :

docker inspect -f '{{.Name}} - {{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $(docker ps -aq)

copier l'ip du container mysql et la coller dans le .env à la racine du projet
et remplacer DB_HOST par l'ip.

Se mettre à la racine du projet et faire :

php artisan migrate:refresh --seed

Aller sur le local host et s'incrire et ENJOY !




