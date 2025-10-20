/**
 * Script pour ajouter automatiquement des commentaires aux fichiers du projet
 * Ce script utilise Node.js pour parcourir les fichiers et ajouter des commentaires
 * 
 * Utilisation: node comment-code.js
 */

const fs = require('fs');
const path = require('path');

// Configuration des dossiers à traiter
const FRONTEND_DIR = path.join(__dirname, 'frontend', 'src');
const BACKEND_DIR = path.join(__dirname, 'backend', 'app');

// Liste des extensions de fichiers à traiter
const FILE_EXTENSIONS = ['.js', '.jsx', '.php'];

// Commentaires génériques par type de fichier
const GENERIC_COMMENTS = {
  // Commentaires pour les fichiers React
  '.jsx': {
    imports: '// Importation des bibliothèques et composants nécessaires',
    useState: '// Déclaration des états du composant',
    useEffect: '// Effet de bord pour gérer le cycle de vie du composant',
    function: '// Fonction pour',
    return: '// Rendu du composant',
    export: '// Exportation du composant pour utilisation dans d\'autres fichiers'
  },
  // Commentaires pour les fichiers JavaScript
  '.js': {
    imports: '// Importation des modules et dépendances',
    class: '// Classe pour',
    constructor: '// Constructeur de la classe',
    function: '// Fonction pour',
    export: '// Exportation du module'
  },
  // Commentaires pour les fichiers PHP
  '.php': {
    namespace: '// Espace de noms pour organiser les classes',
    use: '// Importation des classes et traits',
    class: '// Classe pour',
    function: '// Méthode pour',
    public: '// Propriété publique',
    protected: '// Propriété protégée',
    private: '// Propriété privée'
  }
};

// Fichiers spécifiques à commenter en priorité
const PRIORITY_FILES = [
  'frontend/src/components/AdminDashboard.jsx',
  'frontend/src/components/ChatWindow.jsx',
  'frontend/src/components/TeacherGradesPage.jsx',
  'frontend/src/services/message.service.js',
  'frontend/src/services/grade.service.js',
  'backend/app/Http/Controllers/MessageController.php',
  'backend/app/Http/Controllers/GradeController.php'
];

/**
 * Fonction pour ajouter des commentaires à un fichier
 * @param {string} filePath - Chemin du fichier à commenter
 */
function commentFile(filePath) {
  console.log(`Traitement du fichier: ${filePath}`);
  
  // Lecture du contenu du fichier
  const content = fs.readFileSync(filePath, 'utf8');
  const extension = path.extname(filePath);
  
  // Vérification si l'extension est supportée
  if (!FILE_EXTENSIONS.includes(extension)) {
    console.log(`Extension non supportée: ${extension}`);
    return;
  }
  
  // Récupération des commentaires génériques pour cette extension
  const comments = GENERIC_COMMENTS[extension];
  if (!comments) {
    console.log(`Pas de commentaires définis pour l'extension: ${extension}`);
    return;
  }
  
  // Séparation du contenu en lignes
  let lines = content.split('\n');
  let commentedLines = [];
  let skipNextLines = 0;
  
  // Parcours des lignes pour ajouter des commentaires
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    
    // Si on doit sauter des lignes (déjà commentées)
    if (skipNextLines > 0) {
      commentedLines.push(line);
      skipNextLines--;
      continue;
    }
    
    // Vérification si la ligne contient déjà un commentaire
    if (line.trim().startsWith('//') || line.trim().startsWith('/*') || line.trim().startsWith('*')) {
      commentedLines.push(line);
      continue;
    }
    
    // Ajout de commentaires spécifiques selon le contenu de la ligne
    let commentAdded = false;
    
    // Parcours des patterns de commentaires
    for (const [pattern, comment] of Object.entries(comments)) {
      if (line.trim().startsWith(pattern) || line.includes(` ${pattern} `)) {
        // Ajout du commentaire avant la ligne actuelle
        commentedLines.push(`${comment}`);
        commentedLines.push(line);
        commentAdded = true;
        break;
      }
    }
    
    // Si aucun commentaire n'a été ajouté, on ajoute simplement la ligne
    if (!commentAdded) {
      commentedLines.push(line);
    }
  }
  
  // Écriture du contenu commenté dans le fichier
  fs.writeFileSync(filePath, commentedLines.join('\n'), 'utf8');
  console.log(`Commentaires ajoutés au fichier: ${filePath}`);
}

/**
 * Fonction pour parcourir récursivement un dossier et traiter les fichiers
 * @param {string} dir - Dossier à parcourir
 */
function processDirectory(dir) {
  // Lecture du contenu du dossier
  const items = fs.readdirSync(dir);
  
  // Parcours des éléments du dossier
  for (const item of items) {
    const itemPath = path.join(dir, item);
    const stats = fs.statSync(itemPath);
    
    if (stats.isDirectory()) {
      // Si c'est un dossier, on le parcourt récursivement
      processDirectory(itemPath);
    } else if (stats.isFile() && FILE_EXTENSIONS.includes(path.extname(itemPath))) {
      // Si c'est un fichier avec une extension supportée, on le traite
      commentFile(itemPath);
    }
  }
}

/**
 * Fonction principale pour exécuter le script
 */
function main() {
  console.log('Début du processus de commentaire de code');
  
  // Traitement des fichiers prioritaires
  console.log('Traitement des fichiers prioritaires...');
  for (const file of PRIORITY_FILES) {
    const filePath = path.join(__dirname, file);
    if (fs.existsSync(filePath)) {
      commentFile(filePath);
    } else {
      console.log(`Fichier prioritaire non trouvé: ${filePath}`);
    }
  }
  
  // Traitement des dossiers frontend et backend
  console.log('Traitement du dossier frontend...');
  if (fs.existsSync(FRONTEND_DIR)) {
    processDirectory(FRONTEND_DIR);
  } else {
    console.log(`Dossier frontend non trouvé: ${FRONTEND_DIR}`);
  }
  
  console.log('Traitement du dossier backend...');
  if (fs.existsSync(BACKEND_DIR)) {
    processDirectory(BACKEND_DIR);
  } else {
    console.log(`Dossier backend non trouvé: ${BACKEND_DIR}`);
  }
  
  console.log('Processus de commentaire de code terminé');
}

// Exécution de la fonction principale
main();
