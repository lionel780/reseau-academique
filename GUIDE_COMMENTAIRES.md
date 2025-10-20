# Guide de commentaires pour le projet Réseau Académique

Ce guide définit les conventions de commentaires à suivre pour maintenir une documentation cohérente dans le code source du projet.

## Principes généraux

- Tous les commentaires doivent être rédigés en français
- Les commentaires doivent être clairs, concis et informatifs
- Éviter les commentaires qui répètent simplement le code
- Privilégier les commentaires qui expliquent le "pourquoi" plutôt que le "comment"
- Utiliser la documentation PHPDoc/JSDoc pour les fonctions et classes

## Structure des commentaires

### Fichiers PHP (Backend)

```php
<?php

/**
 * Description du fichier
 * 
 * Ce fichier contient [description de son contenu et de son rôle]
 * 
 * @package App\[Namespace]
 */

namespace App\[Namespace];

use [Classes importées];

/**
 * Classe [NomClasse]
 * 
 * [Description détaillée de la classe]
 */
class NomClasse
{
    /**
     * [Description de la propriété]
     * 
     * @var [type]
     */
    private $propriete;
    
    /**
     * [Description de la méthode]
     * 
     * @param [type] $parametre [Description du paramètre]
     * @return [type] [Description de la valeur de retour]
     * @throws [type] [Description de l'exception]
     */
    public function methode($parametre)
    {
        // Commentaires en ligne pour expliquer les parties complexes
        
        return $resultat;
    }
}
```

### Fichiers JavaScript/React (Frontend)

```javascript
/**
 * Description du fichier
 * 
 * Ce fichier contient [description de son contenu et de son rôle]
 */

// Importation des dépendances
import React, { useState, useEffect } from 'react';
import { autresDependances } from 'autresModules';

/**
 * Composant [NomComposant]
 * 
 * [Description détaillée du composant]
 * 
 * @param {Object} props - Propriétés du composant
 * @param {string} props.prop1 - Description de la propriété 1
 * @returns {JSX.Element} - Élément JSX rendu
 */
const NomComposant = ({ prop1, prop2 }) => {
    // États du composant
    const [etat1, setEtat1] = useState(valeurInitiale);
    
    /**
     * Effet pour [description de ce que fait l'effet]
     */
    useEffect(() => {
        // Logique de l'effet
        
        return () => {
            // Nettoyage si nécessaire
        };
    }, [dependances]);
    
    /**
     * Gère [description de l'événement]
     * 
     * @param {Event} e - Événement déclencheur
     */
    const gestionnaireEvenement = (e) => {
        // Logique du gestionnaire
    };
    
    // Rendu du composant
    return (
        <div>
            {/* Commentaires JSX pour expliquer des parties spécifiques */}
            <Composant />
        </div>
    );
};

export default NomComposant;
```

## Sections à commenter en priorité

1. **Contrôleurs** - Expliquer les actions et leur logique métier
2. **Modèles** - Documenter les relations et les attributs importants
3. **Services** - Détailler les opérations et leur interaction avec l'API
4. **Composants React** - Documenter les props, états et effets
5. **Fonctions utilitaires** - Expliquer leur but et leur utilisation

## Exemples de bons commentaires

```php
// Bon commentaire
/**
 * Récupère les messages d'une conversation entre deux utilisateurs
 * et marque les messages non lus comme lus
 * 
 * @param int $userId ID de l'autre utilisateur dans la conversation
 * @return \Illuminate\Http\JsonResponse Liste des messages de la conversation
 */
```

```javascript
// Bon commentaire
/**
 * Envoie un message et l'affiche immédiatement dans l'interface
 * pour une expérience utilisateur optimiste, puis met à jour
 * son statut une fois la réponse du serveur reçue
 */
```

## Exemples de mauvais commentaires à éviter

```php
// Mauvais commentaire - répète simplement le code
// Récupère les utilisateurs
$users = User::all();
```

```javascript
// Mauvais commentaire - trop vague
// Fonction pour gérer les données
const handleData = () => { ... }
```

---

En suivant ces conventions, nous maintiendrons une documentation cohérente et utile qui facilitera la maintenance et l'évolution du projet.
