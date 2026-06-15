<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Moniteurs | Auto-École Pro</title>
    <link rel="stylesheet" href="../assets/css/global.css" />
    <link rel="stylesheet" href="../assets/css/layout.css" />
    <link rel="stylesheet" href="../assets/css/sidebar.css" />
    <link rel="stylesheet" href="../assets/css/forms.css" />
    <link rel="stylesheet" href="../assets/css/tables.css" />
  </head>
  <body>
    <div class="app-wrapper">
      <div id="sidebar-container">
        <!-- Sidebar Component - Reusable across all pages -->
        <aside class="sidebar">
          <div class="sidebar-header">
            <h2>Auto-École Pro</h2>
            <p>Système de Gestion</p>
          </div>

          <nav class="sidebar-nav">
            <ul>
              <li>
                <a href="dashboard.html"
                  ><span class="menu-icon">📊</span> Tableau de bord</a
                >
              </li>
              <li>
                <a href="eleves.html"
                  ><span class="menu-icon">👨‍🎓</span> Élèves</a
                >
              </li>
              <li>
                <a href="moniteurs.html"
                  ><span class="menu-icon">👨‍🏫</span> Moniteurs</a
                >
              </li>
              <li>
                <a href="vehicules.html"
                  ><span class="menu-icon">🚗</span> Véhicules</a
                >
              </li>
              <li>
                <a href="lecons.html"
                  ><span class="menu-icon">📚</span> Leçons</a
                >
              </li>
              <li>
                <a href="examens.html"
                  ><span class="menu-icon">✍️</span> Examens</a
                >
              </li>
              <li>
                <a href="paiements.html"
                  ><span class="menu-icon">💰</span> Paiements</a
                >
              </li>
              <li>
                <a href="parametres.html"
                  ><span class="menu-icon">⚙️</span> Paramètres</a
                >
              </li>
            </ul>
          </nav>
        </aside>
      </div>

      <main class="main-content">
        <div class="content-container">
          <div class="page-header">
            <h1>Gestion des Moniteurs</h1>
            <p class="page-description">
              Ajouter, modifier et consulter les informations des moniteurs
              d'auto-école
            </p>
          </div>

          <!-- Add Instructor Form -->
          <div class="card">
            <div class="card-header">
              <h3>Ajouter un nouveau moniteur</h3>
            </div>
            <div class="card-body">
              <form class="instructor-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="matricule">Matricule</label>
                    <input
                      type="text"
                      id="matricule"
                      class="form-control"
                      placeholder="MON-2024-001"
                    />
                  </div>
                  <div class="form-group">
                    <label for="nom">Nom</label>
                    <input
                      type="text"
                      id="nom"
                      class="form-control"
                      placeholder="Martin"
                    />
                  </div>
                  <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input
                      type="text"
                      id="prenom"
                      class="form-control"
                      placeholder="Philippe"
                    />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input
                      type="tel"
                      id="telephone"
                      class="form-control"
                      placeholder="06 12 34 56 78"
                    />
                  </div>
                  <div class="form-group">
                    <label for="specialite">Spécialité</label>
                    <select id="specialite" class="form-control">
                      <option value="">Sélectionner une spécialité</option>
                      <option value="Permis B">Permis B (Voiture)</option>
                      <option value="Permis A">Permis A (Moto)</option>
                      <option value="Permis C">Permis C (Poids lourd)</option>
                      <option value="Permis D">
                        Permis D (Transport en commun)
                      </option>
                      <option value="Code">Code de la route</option>
                      <option value="Conduite accompagnée">
                        Conduite accompagnée
                      </option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="date_embauche">Date d'embauche</label>
                    <input
                      type="date"
                      id="date_embauche"
                      class="form-control"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label for="adresse">Adresse</label>
                  <textarea
                    id="adresse"
                    class="form-control"
                    placeholder="Adresse complète"
                  ></textarea>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" class="form-control">
                      <option value="actif">Actif</option>
                      <option value="vacances">En vacances</option>
                      <option value="maladie">Arrêt maladie</option>
                      <option value="inactif">Inactif</option>
                    </select>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="reset" class="btn btn-secondary">
                    Réinitialiser
                  </button>
                  <button type="submit" class="btn btn-primary">
                    Ajouter le moniteur
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Instructors Table -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Liste des moniteurs</h3>
            </div>
            <div class="card-body">
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Prénom</th>
                      <th>Téléphone</th>
                      <th>Adresse</th>
                      <th>Spécialité</th>
                      <th>Date d'embauche</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>MON-2024-001</td>
                      <td>Martin</td>
                      <td>Philippe</td>
                      <td>06 12 34 56 78</td>
                      <td>15 Rue des Écoles, 75005 Paris</td>
                      <td>Permis B, Conduite accompagnée</td>
                      <td>15/03/2019</td>
                      <td>
                        <span class="status-badge status-active">Actif</span>
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>MON-2024-002</td>
                      <td>Dubois</td>
                      <td>Isabelle</td>
                      <td>06 98 76 54 32</td>
                      <td>8 Avenue de la République, 69003 Lyon</td>
                      <td>Permis A, Permis B</td>
                      <td>10/01/2020</td>
                      <td>
                        <span class="status-badge status-active">Actif</span>
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>MON-2024-003</td>
                      <td>Petit</td>
                      <td>Thomas</td>
                      <td>07 11 22 33 44</td>
                      <td>3 Rue Nationale, 59000 Lille</td>
                      <td>Code de la route, Permis B</td>
                      <td>05/06/2021</td>
                      <td>
                        <span class="status-badge status-pending"
                          >Vacances</span
                        >
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>MON-2024-004</td>
                      <td>Bernard</td>
                      <td>Nicolas</td>
                      <td>06 55 66 77 88</td>
                      <td>25 Rue Saint-Genès, 33000 Bordeaux</td>
                      <td>Permis C, Permis D</td>
                      <td>12/02/2022</td>
                      <td>
                        <span class="status-badge status-active">Actif</span>
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <script src="../assets/js/hamburger.js"></script>
    <script>
      fetch("sidebar-component.html")
        .then((response) => response.text())
        .then((data) => {
          document.getElementById("sidebar-container").innerHTML = data;
          const links = document.querySelectorAll(".sidebar-nav a");
          links.forEach((link) => {
            if (link.getAttribute("href") === "moniteurs.html") {
              link.closest("li").classList.add("active");
            }
          });
        });
    </script>
  </body>
</html>
