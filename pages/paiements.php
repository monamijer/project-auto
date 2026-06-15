<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Paiements | Auto-École Pro</title>
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
            <h1>Gestion des Paiements</h1>
            <p class="page-description">
              Suivre les paiements et la situation financière des élèves
            </p>
          </div>

          <!-- Add Payment Form -->
          <div class="card">
            <div class="card-header">
              <h3>Enregistrer un paiement</h3>
            </div>
            <div class="card-body">
              <form class="payment-form">
                <div class="form-row">
                  <div class="form-group">
                    <label for="reference">Référence</label>
                    <input
                      type="text"
                      id="reference"
                      class="form-control"
                      placeholder="PAY-2024-001"
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
                      <option value="5">Robert Julie</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="montant">Montant (€)</label>
                    <input
                      type="number"
                      id="montant"
                      class="form-control"
                      placeholder="0.00"
                      step="0.01"
                    />
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label for="date_paiement">Date de paiement</label>
                    <input
                      type="date"
                      id="date_paiement"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <label for="mode_paiement">Mode de paiement</label>
                    <select id="mode_paiement" class="form-control">
                      <option value="">Sélectionner un mode</option>
                      <option value="especes">Espèces</option>
                      <option value="carte">Carte bancaire</option>
                      <option value="cheque">Chèque</option>
                      <option value="virement">Virement bancaire</option>
                      <option value="paypal">PayPal</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="observation">Observation</label>
                  <textarea
                    id="observation"
                    class="form-control"
                    placeholder="Détails du paiement, forfait, leçons payées..."
                  ></textarea>
                </div>

                <div class="form-actions">
                  <button type="reset" class="btn btn-secondary">
                    Réinitialiser
                  </button>
                  <button type="submit" class="btn btn-primary">
                    Enregistrer le paiement
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Payments Table -->
          <div class="card" style="margin-top: var(--spacing-xl)">
            <div class="card-header">
              <h3>Liste des paiements</h3>
            </div>
            <div class="card-body">
              <div class="table-container">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Référence</th>
                      <th>Élève</th>
                      <th>Montant</th>
                      <th>Date de paiement</th>
                      <th>Mode de paiement</th>
                      <th>Observation</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>PAY-2024-001</td>
                      <td>Dupont Jean</td>
                      <td>750,00 €</td>
                      <td>05/01/2024</td>
                      <td>Carte bancaire</td>
                      <td>Forfait code + 10 leçons</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>PAY-2024-002</td>
                      <td>Martin Sophie</td>
                      <td>450,00 €</td>
                      <td>10/01/2024</td>
                      <td>Espèces</td>
                      <td>5 leçons de conduite</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>PAY-2024-003</td>
                      <td>Bernard Pierre</td>
                      <td>200,00 €</td>
                      <td>12/01/2024</td>
                      <td>Chèque</td>
                      <td>Forfait code</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>PAY-2024-004</td>
                      <td>Petit Marie</td>
                      <td>1 200,00 €</td>
                      <td>15/01/2024</td>
                      <td>Virement</td>
                      <td>Forfait complet (code + 20 leçons)</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>PAY-2024-005</td>
                      <td>Robert Julie</td>
                      <td>300,00 €</td>
                      <td>18/01/2024</td>
                      <td>Carte bancaire</td>
                      <td>Acompte 30%</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                    <tr>
                      <td>PAY-2024-006</td>
                      <td>Dupont Jean</td>
                      <td>350,00 €</td>
                      <td>20/01/2024</td>
                      <td>PayPal</td>
                      <td>5 leçons supplémentaires</td>
                      <td class="table-actions">
                        <button class="btn btn-sm btn-primary">Modifier</button>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                      </td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="text-bold">
                      <td colspan="2"><strong>Total</strong></td>
                      <td><strong>3 250,00 €</strong></td>
                      <td colspan="4"></td>
                    </tr>
                  </tfoot>
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
            if (link.getAttribute("href") === "paiements.html") {
              link.closest("li").classList.add("active");
            }
          });
        });
    </script>
  </body>
</html>
