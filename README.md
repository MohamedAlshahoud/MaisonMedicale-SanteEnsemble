# 🏥 Maison Médicale Santé Ensemble - Plateforme de gestion des rendez-vous médicaux 🏥

Bienvenue sur **Maison Médicale Santé Ensemble** ! Ce projet est une plateforme dédiée à la gestion simplifiée des rendez-vous médicaux et au suivi des patients développée avec **Symfony 7.2**. Elle permet aux patients de créer un espace personnel, de consulter les médecins disponibles et de réserver des rendez-vous en ligne.

---

## 🚀 Fonctionnalités

- **Gestion des rôles** :
  - **Patient** : Crée un espace personnel, consulte les médecins, réserve un rendez-vous.
  - **Médecin** : Gère ses créneaux horaires, consulte les rendez-vous des patients.
  - **Administrateur** : Supervise les patients et médecins, gère les données utilisateurs.
  
- **Système de réservation de rendez-vous** : Les patients peuvent consulter les disponibilités des médecins et réserver un créneau horaire.

- **Notifications par email** : Les utilisateurs reçoivent des rappels automatiques pour leurs rendez-vous.

- **Gestion des disponibilités des médecins** : Les médecins peuvent définir leurs horaires de disponibilité.

- **Espace patient** : Chaque patient dispose d'un espace personnel où il peut suivre ses rendez-vous, consulter ses informations médicales.

- **Contactez-nous** : Vous pouvez nous contacter par téléphone ou via le formulaire de contact disponible sur le site.

---

## 🛠️ Technologies utilisées

- **Symfony 7.2** ⚡️
- **JavaScript (JS)** 💻
- **Bootstrap** 🎨
- **CSS personnalisé** 🎨 : Personnalisation des styles pour un rendu visuel unique et adapté aux besoins du projet.
- **Doctrine ORM** 💾
- **Twig** 🧩
- **EasyAdmin** 🛠️

---

## 📦 Installation

### Prérequis

- **PHP** 8.0 ou version supérieure ☕
- **Composer** (gestionnaire de dépendances PHP) 💾
- **MySQL** (pour la base de données) 🗃️

### Étapes pour démarrer le projet

1. Clone ce dépôt sur ton ordinateur :

   ```bash
   git clone https://github.com/Mohamed18995/MaisonMedicale-SanteEnsemble.git
   cd MaisonMedicale-SanteEnsemble

2. Installe les dépendances PHP via Composer :

   ```bash
   composer install
   
3. Crée et configure la base de données :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force

4. Lancer le serveur Symfony :

   ```bash
   symfony server:start
   
5. L'application sera disponible à l'adresse suivante : http://localhost:8000

---

# 📢 Contactez-nous

- **Téléphone** : Vous pouvez nous joindre au 01 23 45 67 89 pour toute information ou prise de rendez-vous.
- **Formulaire de Contact** : Vous pouvez également nous contacter via notre formulaire de contact pour toute question ou demande spécifique.

---

# 💡 Contribuer

Tu veux contribuer au projet ? N'hésite pas à faire une pull request !

1. Fork le dépôt.

2. Crée une branche pour ta fonctionnalité (git checkout -b feature-xyz).

3. Effectue tes modifications et commit (git commit -am 'Ajout de xyz').

4. Pousse tes changements (git push origin feature-xyz).

5. Crée une pull request pour que nous puissions examiner tes changements.

---
# 📝 Licence

Ce projet est sous la licence MIT.

---
# 🧑‍💻 Auteurs

- **Mohamed Alshahoud** - Développeur

---
