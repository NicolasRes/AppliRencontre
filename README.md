# Tableau d'avancement
https://docs.google.com/document/d/1u32_x2NByHAUZhRkqmQCCkhgpQBoKV2wL1orQeiDycY/edit?pli=1&tab=t.0#heading=h.6jynaot9cbnq

# Site en ligne
https://projetpso.fr


# Guide d'installation du projet

## Prérequis
- PHP ≥ 8.2
- Composer
- MySQL / MariaDB

## Installation
Dans un terminal :
- git clone https://github.com/MinaroliCorentin/AppliRencontre.git
- cd AppliRencontre

## Fichier .env
- modifier / décommenter la ligne **DATABASE_URL=...** qui correspond à votre BBD, éventuellement ajouter votre lien

## Base de données
- Création : `php bin/console doctrine:database:create`
- Appliquer les migrations pour obtenir les tables : `php bin/console doctrine:migrations:migrate` (impossible en l'état, importer un **dump** est la seule solution)

## Assets (JS/CSS)
`php bin/console importmap:require bootstrap`

# 🔴 Installation de Mercure (temps réel) – Projet Symfony

Ce projet utilise **Mercure** pour gérer le temps réel (chat, notifications, etc.).

⚠️ Mercure **n’est pas installé automatiquement** avec Symfony → chaque développeur doit l’installer localement.

---

# 📦 1. Télécharger Mercure

👉 Télécharger depuis ce git :
[https://mercure.rocks/download](https://github.com/dunglas/mercure/releases)

⚠️ **IMPORTANT : ne PAS télécharger les fichiers `mercure-legacy`**

---

## 🖥️ Choisir le bon fichier selon votre OS

### 🍎 macOS

* Mac M1 / M2 / M3 :

```
mercure_Darwin_arm64.tar.gz
```

* Mac Intel :

```
mercure_Darwin_x86_64.tar.gz
```

---

### 🐧 Linux

```
mercure_Linux_x86_64.tar.gz
```

---

### 🪟 Windows

```
mercure_Windows_x86_64.zip
```

---

# 📂 2. Installation

## macOS / Linux

- Extraire le dossier compressé
- Déplacer le binaire `mercure` ainsi que `dev.Caddyfile` dans le projet dans le dossier `/bin`
- Rendre exécutable si nécessaire (normalement pas besoin) : ```chmod +x bin/mercure```

---

## Windows

- Extraire le zip
- Copier `mercure.exe` et `dev.Caddyfile` dans le dossier `bin/`

---

# ⚙️ 3. Configuration (.env)

Ajouter / modifier dans `.env` :

```env
MERCURE_URL=http://localhost:3000/.well-known/mercure
MERCURE_PUBLIC_URL=http://localhost:3000/.well-known/mercure
MERCURE_JWT_SECRET=dev_secret
```

⚠️ La clé sera la même que celle utilisée pour lancer Mercure
Mettez ce que vous voulez dans JWT_SECRET à la place de `dev_secret`.
Mettre la même clé dans le makefile également.
Remplacez bien `https` par `http`.

---

## ⚙️ Configuration du Caddyfile

On force Mercure à démarrer en HTTP pour éviter les erreurs en local.

### 🔧 Modification à faire

Mercure a besoin de son propre serveur.

Dans le fichier :

```bash
bin/dev.Caddyfile
```

Remplacer :

```caddy
{$SERVER_NAME:localhost}
```

par :

```caddy
{$SERVER_NAME:http://localhost:3000}

```

---

# 🚀 4. Lancer Mercure

## macOS / Linux

Utiliser cette commande :
$ mkdir -p ~/.local/share/caddy

Puis celle-ci, en une seule ligne :
MERCURE_PUBLISHER_JWT_KEY=dev_secret MERCURE_SUBSCRIBER_JWT_KEY=dev_secret ./bin/mercure run --config ./bin/dev.Caddyfile

Attention : sur Linux, il ne faut pas la lancer dans le terminal de VS Code (s'il a été installé avec Snap), mais dans un terminal standard à la racine du projet.

Remplacez bien dev_secret par la clé que vous avez choisi.

---

## Windows (Powershell)

```bash
set MERCURE_PUBLISHER_JWT_KEY=dev_secret
set MERCURE_SUBSCRIBER_JWT_KEY=dev_secret
bin\mercure.exe run --config bin/dev.Caddyfile
```

Remplacez bien dev_secret par la clé que vous avez choisi.

---

# 🧪 5. Vérifier que Mercure fonctionne

Ouvrir dans le navigateur :

```
http://localhost:3000/.well-known/mercure
```

👉 Résultat attendu :

```
Missing "topic" parameter
```

✔️ Si vous voyez ce message → Mercure fonctionne

---

# 🧠 6. Lancer Symfony

```bash
# Symfony
symfony server:start --no-tls -d
```

---

# 🛑 7. Arrêter les programmes (si lancés en arrière plan)

```bash
symfony server:stop
pkill -f mercure
```

---

# ⚙️ 8. Utilisation du Makefile

Pour simplifier le lancement du projet, un **Makefile** est disponible.

---

- `make start` : lance Symfony et Mercure
- `make stop` : arrête Symfony et Mercure
- `make check` : vérifie l'état des deux outils
