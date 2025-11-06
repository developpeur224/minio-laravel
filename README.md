# Guide de Démarrage — Projet Laravel + MinIO + MySQL

Ce document explique comment configurer et exécuter le projet Laravel en environnement local avec **MinIO** (comme serveur S3) et **MySQL** pour la base de données.

---

## 🧩 Prérequis

Avant de commencer, assurez-vous d’avoir installé :

- [PHP ≥ 8.2](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL ≥ 8.0](https://dev.mysql.com/downloads/)
- [MinIO](https://min.io/download) (serveur installé localement, pas dans Docker)
- [Git](https://git-scm.com/)

---

## ⚙️ Installation du projet

```bash
# Cloner le dépôt
git https://github.com/developpeur224/minio-laravel.git
cd mini-laravel

# Installer les dépendances PHP
composer install
```

---

## 🔑 Configuration de l’environnement

Copiez le fichier d’exemple d’environnement :

```bash
cp .env.example .env
```

Puis modifiez les variables suivantes :

### 🔸 Base de données MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_ta_base
DB_USERNAME=root
DB_PASSWORD=ton_mot_de_passe
```

Créez la base de données si elle n’existe pas :

```bash
php artisan migrate 
```

---

### 🔸 Configuration du stockage MinIO (équivalent S3 local)

MinIO joue le rôle d’un **serveur S3 local**, accessible via HTTP.

#### 1️⃣ Lancer le serveur MinIO localement

```bash
minio server ~/minio-data --console-address ":9001" --adress ":9000"
```

- **Port 9000** → accès API S3  
- **Port 9001** → accès à la console web (UI MinIO)

Par défaut :
- Accès : http://127.0.0.1:9001
- Identifiant : `admin`
- Mot de passe : `secret123`

#### 2️⃣ Créer un bucket pour Laravel

Dans la console MinIO :
- Connectez-vous à http://127.0.0.1:9001
- Créez un bucket nommé : `laravel-bucket`
- Laissez les permissions par défaut (ou mettez-le privé si vous générez des URLs signées).

#### 3️⃣ Modifier la configuration S3 dans `.env`

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=admin
AWS_SECRET_ACCESS_KEY=secret123
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=laravel-bucket
AWS_URL=http://127.0.0.1:9000
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
```

> ⚠️ `AWS_USE_PATH_STYLE_ENDPOINT=true` est **obligatoire** pour MinIO afin d’éviter les erreurs `AccessDenied`.

---


---

## 🧠 Lancer le serveur Laravel

```bash
php artisan serve
```

Accédez à votre application :
👉 [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 📁 Stockage et liens symboliques

Créez le lien entre le stockage et le dossier public :

```bash
php artisan storage:link
```

---

## 🧪 Vérification du fonctionnement de MinIO

Pour vérifier que Laravel peut stocker un fichier dans MinIO :

```bash
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'Hello MinIO!');
```

Ensuite, connectez-vous à la console MinIO (port 9001) et vérifiez que `test.txt` apparaît dans votre bucket.

---

---

## 🧩 Notes techniques

- Le projet utilise **MinIO** comme équivalent local à Amazon S3.
- Les fichiers privés peuvent être consultés via des **URLs signées** générées avec :
  ```php
  Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(10));
  ```
- Les fichiers publics peuvent être servis directement via `Storage::disk('s3')->url($path)` si le bucket est public.
- MySQL est utilisé comme base de données principale pour les métadonnées (ex. titres, types, tailles, catégories).

---

## 👨‍💻 Auteur & Support

Projet développé par **Mika Diallo**  
📧 Contact : [developpeur033@gmail.com]  
🗓️ Dernière mise à jour : 06-11-2025

---
