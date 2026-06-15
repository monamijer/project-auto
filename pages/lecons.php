<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Leçons | Auto-École Pro</title>
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
            <h1>Gestion des Leçons</h1>
            <p class="page-description">
              Planifier et suivre les leçons de conduite
            </p>
          </div>

          <!-- Add Lesson Form -->
          <div class="card">
            <div class="card-header">
              <h3>Planifier une nouvelle leçon</h3>
            </div>
            <div class="card-body">
              <form class="lesson-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="reference">Référence</label>
                    <input
                      type="text"
                      id="reference"
                      class="form-control"
                      placeholder="LEC-2024-001"
                    />
                  </div>
                  <div class="form-group">
                    <label for="eleve">Élève</label>
                    <select id="eleve" class="form-control">
                      <option value="">Sélectionner un élève</option>
                      <option value="1">Dupont Jean</option>
                      <option value="2">Martin Sophie</option>
                      <option value="3">Bernard Pierre</option>
                      <option value="4">Petit Marie</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="moniteur">Moniteur</label>
                    <select id="moniteur" class="form-control">
                      <option value="">Sélectionner un moniteur</option>
                      <option value="1">Martin Philippe</option>
                      <option value="2">Dubois Isabelle</option>
                      <option value="3">Petit Thomas</option>
                      <option value="4">Bernard Nicolas</option>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="vehicule">Véhicule</label>
                    <select id="vehicule" class="form-control">
                      <option value="">Sélectionner un véhicule</option>
                      <option value="1">AB-123-CD (Renault Clio)</option>
                      <option value="2">EF-456-GH (Peugeot 208)</option>
                      <option value="3">IJ-789-KL (Citroën C3)</option>
                      <option value="4">MN-012-OP (Yamaha MT-07)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" class="form-control" />
                  </div>
                  <div class="form-group">
                    <label for="heure">Heure</label>
                    <input type="time" id="heure" class="form-control" />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="type_lecon">Type de leçon</label>
                    <select id="type_lecon" class="form-control">
                      <option value="">Sélectionner un type</option>
                      <option value="conduite">Conduite pratique</option>
                      <option value="code">Code de la route</option>
                      <option value="simulateur">Simulateur</option>
                      <option value="accompagnee">Conduite accompagnée</option>
                      <option value="examen_blanc">Examen blanc</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="observation">Observation</label>
                  <textarea
                    id="observation"
                    class="form-control"
                    placeholder="Observations particulières, progression, difficultés..."
                  ></textarea>
                </div>

                <div class="form-actions">
                  <button type="reset" class="btn btn-secondary">
                    Réinitialiser
                  </button>
                  <button type="submit" class="btn btn-primary">
                    Planifier la leçon
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Lessons Table -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Liste des leçons</h3>
            </div>
            <div class="card-body">
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Référence</th>
                      <th>Élève</th>
                      <th>Moniteur</th>
                      <th>Véhicule</th>
                      <th>Date</th>
                      <th>Heure</th>
                      <th>Type de leçon</th>
                      <th>Observation</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>LEC-2024-001</td>
                      <td>Dupont Jean</td>
                      <td>Martin Philippe</td>
                      <td>AB-123-CD</td>
                      <td>20/01/2024</td>
                      <td>14:00</td>
                      <td>Conduite pratique</td>
                      <td>Progression satisfaisante</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>LEC-2024-002</td>
                      <td>Martin Sophie</td>
                      <td>Dubois Isabelle</td>
                      <td>EF-456-GH</td>
                      <td>21/01/2024</td>
                      <td>10:30</td>
                      <td>Code de la route</td>
                      <td>Révision série 3</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>LEC-2024-003</td>
                      <td>Bernard Pierre</td>
                      <td>Petit Thomas</td>
                      <td>IJ-789-KL</td>
                      <td>22/01/2024</td>
                      <td>09:00</td>
                      <td>Conduite accompagnée</td>
                      <td>Préparation examen</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>LEC-2024-004</td>
                      <td>Petit Marie</td>
                      <td>Martin Philippe</td>
                      <td>MN-012-OP</td>
                      <td>23/01/2024</td>
                      <td>15:00</td>
                      <td>Simulateur</td>
                      <td>Manoeuvres parking</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>LEC-2024-005</td>
                      <td>Dupont Jean</td>
                      <td>Bernard Nicolas</td>
                      <td>QR-345-ST</td>
                      <td>24/01/2024</td>
                      <td>11:00</td>
                      <td>Examen blanc</td>
                      <td>Simulation examen pratique</td>
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
            if (link.getAttribute("href") === "lecons.html") {
              link.closest("li").classList.add("active");
            }
          });
        });
    </script>
  </body>
</html>
