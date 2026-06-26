<?php
/**
 * pages/uml.php — Diagrammes UML
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$pageTitle = 'Diagrammes UML — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h4 mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Diagrammes UML</h1></div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimer</button>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#uc">Cas d'utilisation</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cl">Classes</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ac">Connexion</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ac2">Lecon</button></li>
</ul>

<div class="tab-content">

<div class="tab-pane fade show active" id="uc">
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Cas d'utilisation</h5></div>
    <div class="card-body" id="diag-uc"></div>
</div>
</div>

<div class="tab-pane fade" id="cl">
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Classes</h5></div>
    <div class="card-body" id="diag-cl"></div>
</div>
</div>

<div class="tab-pane fade" id="ac">
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Connexion</h5></div>
    <div class="card-body" id="diag-ac"></div>
</div>
</div>

<div class="tab-pane fade" id="ac2">
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0">Planification lecon</h5></div>
    <div class="card-body" id="diag-ac2"></div>
</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
mermaid.initialize({startOnLoad:false,theme:'default',securityLevel:'loose'});

async function renderDiag(id, code) {
    const el = document.getElementById(id);
    if (!el) return;
    try {
        const { svg } = await mermaid.render(id + '-svg', code);
        el.innerHTML = svg;
    } catch(e) {
        el.innerHTML = '<div class="alert alert-danger">Erreur: ' + e.message + '</div>';
        console.error(e);
    }
}

// Cas d'utilisation
renderDiag('diag-uc', `graph TD
    A((Admin)) --- B[Gerer comptes]
    A --- C[Gerer eleves]
    A --- D[Gerer moniteurs]
    A --- E[Gerer vehicules]
    A --- F[Planifier lecons]
    A --- G[Paiements]
    A --- H[Rapports]
    I((Directeur)) --- C
    I --- D
    I --- E
    I --- F
    I --- H
    J((Secretaire)) --- C
    J --- F
    J --- G
    K((Caissier)) --- G
    L((Moniteur)) --- F
    M((Stagiaire)) --- N[Consulter]`);

// Classes
renderDiag('diag-cl', `classDiagram
    class Utilisateur {
        +int id
        +string nom
        +string prenom
        +string email
        +ajouter()
        +modifier()
    }
    class Formation {
        +int id
        +string nom
        +decimal prix
        +int duree
    }
    class Instructeur {
        +int id
        +string nom
        +int experience
    }
    class Vehicule {
        +int id
        +string marque
        +string modele
        +string immat
        +bool dispo
    }
    class Lecon {
        +int id
        +datetime date
        +string statut
        +planifier()
        +completer()
    }
    class Paiement {
        +int id
        +decimal montant
        +date date_p
        +string methode
    }
    class Document {
        +int id
        +string type
        +string fichier
        +int version
    }
    class Compte {
        +int id
        +string login
        +string role
        +datetime expire
    }
    Utilisateur -- Lecon
    Instructeur -- Lecon
    Vehicule -- Lecon
    Utilisateur -- Formation
    Utilisateur -- Paiement
    Utilisateur -- Document
    Utilisateur -- Compte`);

// Connexion
renderDiag('diag-ac', `flowchart TD
    A([Debut]) --> B[Saisir login]
    B --> C{Compte trouve}
    C -->|Non| Z([Erreur])
    C -->|Oui| D{Actif}
    D -->|Non| Z
    D -->|Oui| E{MDP correct}
    E -->|Non| F[+1 tentative]
    F --> G{5 tentatives}
    G -->|Oui| H[Verrouiller]
    H --> X([Fin])
    G -->|Non| Z
    E -->|Oui| I[Reset]
    I --> J{2FA}
    J -->|Oui| K[Verifier OTP]
    K --> L{OTP ok}
    L -->|Non| Z
    L -->|Oui| M[Session]
    J -->|Non| M
    M --> X
    Z --> X`);

// Lecon
renderDiag('diag-ac2', `flowchart TD
    A([Debut]) --> B[Choisir eleve]
    B --> C[Choisir moniteur]
    C --> D[Choisir vehicule]
    D --> E[Choisir date]
    E --> F{Conflit}
    F -->|Oui| E
    F -->|Non| G[Creer lecon]
    G --> H{Succes}
    H -->|Non| X([Fin])
    H -->|Oui| I[Confirmer]
    I --> J{Lecon faite}
    J -->|Oui| K[Marquer OK]
    K --> L{3 lecons}
    L -->|Oui| M[Eligible]
    L -->|Non| N[Continuer]
    J -->|Non| O[Annuler]
    M --> X
    N --> X
    O --> X`);
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
