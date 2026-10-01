# MagixModuleNews

[![Release](https://img.shields.io/github/release/magix-cms/MagixModuleNews.svg)](https://github.com/magix-cms/MagixModuleNews/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixModuleNews.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixModuleNews** est un plugin officiel pour Magix CMS 4.x permettant d'associer et d'afficher une sélection d'actualités ou d'événements au sein des fiches de contenu du CMS (Pages, Produits, Catégories, À propos, etc.).

## 🌟 Fonctionnalités principales

* **Injection contextuelle dans le Core** : Ajoute automatiquement un onglet dédié (« Actualités liées ») au sein des fiches d'édition des modules sélectionnés dans l'administration.
* **Recherche autocomplétée (TomSelect)** : Recherche dynamique en AJAX des actualités publiées avec filtrage automatique.
* **Tri par Drag & Drop** : Ordonnancement fluide des actualités sélectionnées grâce à `Sortable.js`.
* **Ciblage modulaire** : Choix des modules compatibles (pages, catalogue, boutique) paramétrable directement depuis la gestion des extensions.
* **Affichage adaptatif par contexte** : Transmission du nom du module parent (`$modulenews_current_module`) au template pour personnaliser le rendu frontend selon la section (pleine largeur sur une page, bloc condensé sur une fiche produit, etc.).
* **Intégration native & Overrides** : Utilisation du présentateur officiel (`NewsPresenter::format`) et prise en compte du filtre Core `extendNewsData` (images, dates d'événements, tags, données structurées).
* **Cache SQL natif** : Mise en cache SQL des liaisons d'identifiants pour préserver les performances en production.

## ⚙️ Installation

1. Téléchargez la dernière version du plugin.
2. Décompressez l'archive et déposez le dossier `MagixModuleNews` dans le répertoire `plugins/` de votre site Magix CMS.
3. Connectez-vous au panneau d'administration de Magix CMS.
4. Rendez-vous dans **Extensions > Plugins**.
5. Repérez **MagixModuleNews** dans la liste et cliquez sur **Installer**.
6. Éditez le plugin pour cocher les modules cibles souhaités (Pages, Produits, Catégories...) puis enregistrez.

*Note : Lors de l'installation, le plugin crée la table de liaison `mc_plug_module_news` et se greffe automatiquement sur les hooks de bas de page (`displayPageBottom`, `displayProductExtraContent`, `displayCategoryBottom`, etc.).*

## 🚀 Utilisation

### Côté Administration
1. Ouvrez une page, un produit ou une catégorie en édition.
2. Rendez-vous sur l'onglet **Actualités liées**.
3. Saisissez au moins deux lettres du titre d'une actualité dans le champ de recherche pour l'ajouter.
4. Réorganisez l'ordre des éléments par glisser-déposer à l'aide de la poignée de déplacement.
5. Cliquez sur **Sauvegarder la liste** pour valider vos modifications en AJAX.

### Côté Public (Frontend)
Le widget s'exécute automatiquement aux emplacements configurés dans le layout du thème. Si aucune actualité n'est associée à l'élément courant, le widget reste invisible et aucun contenu superflu n'est injecté dans le code source.

## 🛠️ Architecture Technique (Pour les développeurs)

Le plugin respecte les standards de **Magix CMS V4** :

* **Amorçage sécurisé (`Boot.php`)** : Déclaration de l'espace de template d'administration (`SmartyTool::addTemplateDir('modulenews', ...)`) et injection via les hooks `{module}_edit_tab` et `{module}_edit_content`.
* **Respect du MVC & DRY** : Le plugin ne duplique pas les requêtes du module News ; il stocke une table pivot (`ModuleNewsFrontDb`) et s'appuie sur `NewsDb::getNewsPage()` et `NewsPresenter::format()` du Core.
* **Interface Backend** : Orchestration combinée de `TomSelect` pour la sélection AJAX et `Sortable.js` pour le tri visuel.
* **Sécurité & Sandboxing** : Exécution encapsulée dans le `FrontendController` avec validation des identifiants et vérification de la présence des templates (`file_exists`).

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.  
Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)  
Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU telle que publiée par la Free Software Foundation ; soit la version 3 de la Licence, ou (à votre discrétion) toute version ultérieure.