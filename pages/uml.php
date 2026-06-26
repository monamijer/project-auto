<?php
/**
 * pages/uml.php — Diagrammes UML (#50)
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
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#uc">🎯 Cas d'utilisation</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cl">🏛️ Classes</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ac">⚙️ Connexion</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ac2">📅 Leçon</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="uc"><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Cas d'utilisation</h5></div><div class="card-body"><div class="mermaid">
graph LR
    Admin((Admin))-->UC1[Gérer comptes];Admin-->UC2[Gérer élèves];Admin-->UC3[Gérer moniteurs]
    Directeur((Directeur))-->UC2;Directeur-->UC3;Directeur-->UC5[Planifier leçons]
    Secretaire((Secrétaire))-->UC2;Secretaire-->UC5;Secretaire-->UC6[Paiements]
    Caissier((Caissier))-->UC6;Moniteur((Moniteur))-->UC5;Stagiaire((Stagiaire))-->UC11[Consulter]
</div></div></div></div>

<div class="tab-pane fade" id="cl"><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Classes</h5></div><div class="card-body"><div class="mermaid">
classDiagram
    class Utilisateur{+int id +string nom +string prenom +string email +ajouter() +modifier()}
    class Formation{+int id +string nom +decimal prix}
    class Lecon{+int id +datetime date_lecon +string statut +planifier() +completer()}
    class Paiement{+int id +decimal montant +date date_paiement +enregistrer()}
    class Document{+int id +string type +int version +telecharger()}
    Utilisateur "1"-->"*" Lecon;Utilisateur "*"-->"1" Formation;Utilisateur "1"-->"*" Paiement
</div></div></div></div>

<div class="tab-pane fade" id="ac"><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Connexion</h5></div><div class="card-body"><div class="mermaid">
flowchart TD A([Début])-->B[Saisir identifiant]-->C{Verrouillé?} C-->|Oui|D[Erreur] C-->|Non|E[sp_connexion]-->F{MDP ok?} F-->|Oui|G[Session] F-->|Non|H[Incrémenter]-->I{5?} I-->|Oui|J[Verrouiller]
</div></div></div></div>

<div class="tab-pane fade" id="ac2"><div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h5 class="mb-0">Planification leçon</h5></div><div class="card-body"><div class="mermaid">
flowchart TD A([Début])-->B[Choisir élève]-->C[Choisir moniteur]-->D[Choisir véhicule]-->E[Choisir date]-->F{Conflit?} F-->|Oui|G[Erreur]-->E F-->|Non|H[sp_planifier_lecon]-->I[Confirmation]
</div></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>mermaid.initialize({startOnLoad:true,theme:'default'});</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>  