<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Paramètres | Auto-École Pro</title>
    <link rel="stylesheet" href="../assets/css/global.css" />
    <link rel="stylesheet" href="../assets/css/layout.css" />
    <link rel="stylesheet" href="../assets/css/sidebar.css" />
    <link rel="stylesheet" href="../assets/css/forms.css" />
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
            <h1>Paramètres</h1>
            <p class="page-description">
              Configurer l'application et les préférences de l'auto-école
            </p>
          </div>

          <!-- Auto-école Information -->
          <div class="card">
            <div class="card-header">
              <h3>Informations de l'auto-école</h3>
            </div>
            <div class="card-body">
              <form class="settings-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="nom_autoecole">Nom de l'auto-école</label>
                    <input
                      type="text"
                      id="nom_autoecole"
                      class="form-control"
                      value="Auto-École Pro"
                    />
                  </div>
                  <div class="form-group">
                    <label for="siret">Numéro SIRET</label>
                    <input
                      type="text"
                      id="siret"
                      class="form-control"
                      value="123 456 789 00012"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label for="adresse_autoecole">Adresse</label>
                  <textarea id="adresse_autoecole" class="form-control">
15 Rue des Écoles, 75005 Paris, France</textarea
                  >
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="telephone_autoecole">Téléphone</label>
                    <input
                      type="tel"
                      id="telephone_autoecole"
                      class="form-control"
                      value="01 23 45 67 89"
                    />
                  </div>
                  <div class="form-group">
                    <label for="email_autoecole">Email</label>
                    <input
                      type="email"
                      id="email_autoecole"
                      class="form-control"
                      value="contact@autoecolepro.fr"
                    />
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Tarifs et Forfaits -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Tarifs et Forfaits</h3>
            </div>
            <div class="card-body">
              <form class="pricing-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="tarif_code">Forfait Code (€)</label>
                    <input
                      type="number"
                      id="tarif_code"
                      class="form-control"
                      value="200"
                      step="10"
                    />
                  </div>
                  <div class="form-group">
                    <label for="tarif_lecon">Leçon de conduite (€/heure)</label>
                    <input
                      type="number"
                      id="tarif_lecon"
                      class="form-control"
                      value="45"
                      step="5"
                    />
                  </div>
                  <div class="form-group">
                    <label for="tarif_forfait_10">Forfait 10 leçons (€)</label>
                    <input
                      type="number"
                      id="tarif_forfait_10"
                      class="form-control"
                      value="400"
                      step="10"
                    />
                  </div>
                  <div class="form-group">
                    <label for="tarif_forfait_20">Forfait 20 leçons (€)</label>
                    <input
                      type="number"
                      id="tarif_forfait_20"
                      class="form-control"
                      value="750"
                      step="10"
                    />
                  </div>
                  <div class="form-group">
                    <label for="tarif_examen">Frais d'examen (€)</label>
                    <input
                      type="number"
                      id="tarif_examen"
                      class="form-control"
                      value="50"
                      step="5"
                    />
                  </div>
                  <div class="form-group">
                    <label for="tarif_accompagnee"
                      >Conduite accompagnée (€/heure)</label
                    >
                    <input
                      type="number"
                      id="tarif_accompagnee"
                      class="form-control"
                      value="40"
                      step="5"
                    />
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Horaires d'ouverture -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Horaires d'ouverture</h3>
            </div>
            <div class="card-body">
              <form class="schedule-form">
                <div class="form-row">
                  <div class="form-group">
                    <label>Lundi - Vendredi</label>
                    <input
                      type="text"
                      class="form-control"
                      value="09:00 - 12:00 / 14:00 - 19:00"
                    />
                  </div>
                  <div class="form-group">
                    <label>Samedi</label>
                    <input
                      type="text"
                      class="form-control"
                      value="09:00 - 13:00"
                    />
                  </div>
                  <div class="form-group">
                    <label>Dimanche</label>
                    <input type="text" class="form-control" value="Fermé" />
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Préférences d'affichage -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Préférences d'affichage</h3>
            </div>
            <div class="card-body">
              <form class="preferences-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="langue">Langue</label>
                    <select id="langue" class="form-control">
                      <option value="fr" selected>Français</option>
                      <option value="en">English</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="date_format">Format de date</label>
                    <select id="date_format" class="form-control">
                      <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                      <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                      <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="items_par_page">Éléments par page</label>
                    <select id="items_par_page" class="form-control">
                      <option value="10">10</option>
                      <option value="25" selected>25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                    </select>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Sauvegarde -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Sauvegarde et Maintenance</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <button class="btn btn-primary">
                  Sauvegarder les modifications
                </button>
                <button
                  class="btn btn-secondary"
                  style="margin-left: var(--spacing-sm)"
                >
                  Exporter les données
                </button>
                <button
                  class="btn btn-danger"
                  style="margin-left: var(--spacing-sm)"
                >
                  Sauvegarde de secours
                </button>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="reset" class="btn btn-secondary">Annuler</button>
            <button type="submit" class="btn btn-primary">
              Appliquer tous les paramètres
            </button>
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
            if (link.getAttribute("href") === "parametres.html") {
              link.closest("li").classList.add("active");
            }
          });
        });
    </script>
  </body>
</html>
