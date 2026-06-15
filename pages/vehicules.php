<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Véhicules | Auto-École Pro</title>
    <link rel="stylesheet" href="../assets/css/global.css" />
    <link rel="stylesheet" href="../assets/css/layout.css" />
    <link rel="stylesheet" href="../assets/css/sidebar.css" />
    <link rel="stylesheet" href="../assets/css/forms.css" />
    <link rel="stylesheet" href="../assets/css/tables.css" />
  </head>
  <body>
    <div class="app-wrapper">
      <div id="sidebar-container">
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
            <h1>Gestion des Véhicules</h1>
            <p class="page-description">
              Ajouter, modifier et consulter le parc automobile de l'auto-école
            </p>
          </div>

          <!-- Add Vehicle Form -->
          <div class="card">
            <div class="card-header">
              <h3>Ajouter un nouveau véhicule</h3>
            </div>
            <div class="card-body">
              <form class="vehicle-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="immatriculation">Immatriculation</label>
                    <input
                      type="text"
                      id="immatriculation"
                      class="form-control"
                      placeholder="AB-123-CD"
                    />
                  </div>
                  <div class="form-group">
                    <label for="marque">Marque</label>
                    <input
                      type="text"
                      id="marque"
                      class="form-control"
                      placeholder="Renault"
                    />
                  </div>
                  <div class="form-group">
                    <label for="modele">Modèle</label>
                    <input
                      type="text"
                      id="modele"
                      class="form-control"
                      placeholder="Clio"
                    />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="annee">Année</label>
                    <input
                      type="number"
                      id="annee"
                      class="form-control"
                      placeholder="2022"
                      min="2000"
                      max="2024"
                    />
                  </div>
                  <div class="form-group">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" class="form-control">
                      <option value="">Sélectionner une catégorie</option>
                      <option value="B">Permis B (Voiture)</option>
                      <option value="A">Permis A (Moto)</option>
                      <option value="C">Permis C (Poids lourd)</option>
                      <option value="D">Permis D (Bus)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="date_acquisition">Date d'acquisition</label>
                    <input
                      type="date"
                      id="date_acquisition"
                      class="form-control"
                    />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="statut">Statut</label>
                    <select id="statut" class="form-control">
                      <option value="disponible">Disponible</option>
                      <option value="maintenance">En maintenance</option>
                      <option value="reserve">Réservé pour leçon</option>
                      <option value="hors_service">Hors service</option>
                    </select>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="reset" class="btn btn-secondary">
                    Réinitialiser
                  </button>
                  <button type="submit" class="btn btn-primary">
                    Ajouter le véhicule
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Vehicles Table -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Liste des véhicules</h3>
            </div>
            <div class="card-body">
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Immatriculation</th>
                      <th>Marque</th>
                      <th>Modèle</th>
                      <th>Année</th>
                      <th>Catégorie</th>
                      <th>Date d'acquisition</th>
                      <th>Statut</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>AB-123-CD</td>
                      <td>Renault</td>
                      <td>Clio V</td>
                      <td>2022</td>
                      <td>B</td>
                      <td>15/01/2022</td>
                      <td>
                        <span class="status-badge status-active"
                          >Disponible</span
                        >
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>EF-456-GH</td>
                      <td>Peugeot</td>
                      <td>208</td>
                      <td>2021</td>
                      <td>B</td>
                      <td>10/03/2021</td>
                      <td>
                        <span class="status-badge status-warning"
                          >Maintenance</span
                        >
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>IJ-789-KL</td>
                      <td>Citroën</td>
                      <td>C3</td>
                      <td>2023</td>
                      <td>B</td>
                      <td>05/06/2023</td>
                      <td>
                        <span class="status-badge status-active"
                          >Disponible</span
                        >
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>MN-012-OP</td>
                      <td>Yamaha</td>
                      <td>MT-07</td>
                      <td>2022</td>
                      <td>A</td>
                      <td>20/02/2022</td>
                      <td>
                        <span class="status-badge status-pending">Réservé</span>
                      </td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>QR-345-ST</td>
                      <td>Mercedes</td>
                      <td>Sprinter</td>
                      <td>2020</td>
                      <td>C</td>
                      <td>10/11/2020</td>
                      <td>
                        <span class="status-badge status-active"
                          >Disponible</span
                        >
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
            if (link.getAttribute("href") === "vehicules.html") {
              link.closest("li").classList.add("active");
            }
          });
        });
    </script>
  </body>
</html>
