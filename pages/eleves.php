<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Élèves | Auto-École Pro</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/tables.css">
</head>
<body>
    <div class="app-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-container">
                <div class="page-header">
                    <h1>Gestion des Élèves</h1>
                    <p class="page-description">Ajouter, modifier et consulter les informations des élèves</p>
                </div>
                
                <?php if ($message): ?>
                    <div style="background: #d4edda; color: #155724; padding: var(--spacing-md); border-radius: var(--border-radius-sm); margin-bottom: var(--spacing-lg);">
                        ✅ <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: var(--spacing-md); border-radius: var(--border-radius-sm); margin-bottom: var(--spacing-lg);">
                        ❌ <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add Student Form -->
                <div class="card">
                    <div class="card-header">
                        <h3>Ajouter un nouvel élève</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="student-form">
                            <input type="hidden" name="action" value="add">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="sexe">Sexe *</label>
                                    <select id="sexe" name="sexe" class="form-control" required>
                                        <option value="M">Masculin</option>
                                        <option value="F">Féminin</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_naissance">Date de naissance *</label>
                                    <input type="date" id="date_naissance" name="date_naissance" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="telephone">Téléphone *</label>
                                    <input type="tel" id="telephone" name="telephone" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="categorie">Catégorie du permis *</label>
                                    <select id="categorie" name="categorie" class="form-control" required>
                                        <option value="A">Permis A - Moto</option>
                                        <option value="B">Permis B - Voiture</option>
                                        <option value="C">Permis C - Poids lourd</option>
                                        <option value="D">Permis D - Transport</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <textarea id="adresse" name="adresse" class="form-control"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="date_inscription">Date d'inscription *</label>
                                    <input type="date" id="date_inscription" name="date_inscription" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <select id="statut" name="statut" class="form-control">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                        <option value="suspendu">Suspendu</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                                <button type="submit" class="btn btn-primary">Ajouter l'élève</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Students Table -->
                <div class="card" style="margin-top: var(--spacing-xl);">
                    <div class="card-header">
                        <h3>Liste des élèves</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Sexe</th>
                                        <th>Date naissance</th>
                                        <th>Téléphone</th>
                                        <th>Catégorie</th>
                                        <th>Date inscription</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                                        <td><?php echo htmlspecialchars($student['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                                        <td><?php echo $student['sexe'] == 'M' ? 'M' : 'F'; ?></td>
                                        <td><?php echo formatDate($student['date_naissance']); ?></td>
                                        <td><?php echo htmlspecialchars($student['telephone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['categorie_permis']); ?></td>
                                        <td><?php echo formatDate($student['date_inscription']); ?></td>
                                        <td><?php echo getStatusBadge($student['statut']); ?></td>
                                        <td class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="editStudent(<?php echo $student['id']; ?>)">Modifier</button>
                                            <a href="?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet élève ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/hamburger.js"></script>
</body>
</html>