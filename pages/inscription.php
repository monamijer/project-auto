<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidature d'Inscription | Auto-École Pro</title>
    <link rel="stylesheet" href="../assets/css/global.css" />
    <link rel="stylesheet" href="../assets/css/forms.css" />
    <style>
      /* Additional styles for inscription page */
      .inscription-page {
        background: linear-gradient(
          135deg,
          var(--primary-dark) 0%,
          var(--primary-color) 100%
        );
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-xl);
      }

      .inscription-container {
        max-width: 800px;
        width: 100%;
        margin: 0 auto;
      }

      .inscription-header {
        text-align: center;
        margin-bottom: var(--spacing-xl);
      }

      .inscription-header h1 {
        color: white;
        margin-bottom: var(--spacing-xs);
      }

      .inscription-header p {
        color: rgba(255, 255, 255, 0.9);
      }

      .back-link {
        display: inline-block;
        margin-top: var(--spacing-lg);
        color: white;
        text-align: center;
      }

      .back-link a {
        color: white;
        text-decoration: underline;
      }
    </style>
  </head>
  <body class="inscription-page">
    <div class="inscription-container">
      <div class="inscription-header">
        <h1>Auto-École Pro</h1>
        <p>Formulaire de candidature d'inscription</p>
      </div>

      <div class="card">
        <div class="card-header">
          <h3>Déposer une candidature</h3>
          <p class="text-muted text-small">
            Veuillez remplir tous les champs obligatoires
          </p>
        </div>
        <div class="card-body">
          <form class="candidature-form">
            <div class="form-row">
              <div class="form-group">
                <label for="nom" class="required">Nom</label>
                <input
                  type="text"
                  id="nom"
                  class="form-control"
                  placeholder="Dupont"
                  required
                />
              </div>
              <div class="form-group">
                <label for="prenom" class="required">Prénom</label>
                <input
                  type="text"
                  id="prenom"
                  class="form-control"
                  placeholder="Jean"
                  required
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="date_naissance" class="required"
                  >Date de naissance</label
                >
                <input
                  type="date"
                  id="date_naissance"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label for="telephone" class="required">Téléphone</label>
                <input
                  type="tel"
                  id="telephone"
                  class="form-control"
                  placeholder="06 12 34 56 78"
                  required
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email" class="required">Adresse e-mail</label>
                <input
                  type="email"
                  id="email"
                  class="form-control"
                  placeholder="jean.dupont@email.com"
                  required
                />
              </div>
              <div class="form-group">
                <label for="categorie" class="required"
                  >Catégorie du permis</label
                >
                <select id="categorie" class="form-control" required>
                  <option value="">Sélectionner</option>
                  <option value="A">Permis A - Moto</option>
                  <option value="B">Permis B - Voiture</option>
                  <option value="C">Permis C - Poids lourd</option>
                  <option value="D">Permis D - Transport en commun</option>
                  <option value="AC">Conduite accompagnée (AAC)</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="adresse" class="required">Adresse complète</label>
              <textarea
                id="adresse"
                class="form-control"
                placeholder="Numéro, rue, code postal, ville"
                rows="3"
                required
              ></textarea>
            </div>

            <div class="form-group">
              <label for="message">Message (optionnel)</label>
              <textarea
                id="message"
                class="form-control"
                placeholder="Informations complémentaires, disponibilités, questions..."
                rows="4"
              ></textarea>
            </div>

            <div class="form-group">
              <label class="form-check">
                <input type="checkbox" required />
                <span
                  >J'accepte les <a href="#">conditions générales</a> et la
                  <a href="#">politique de confidentialité</a></span
                >
              </label>
            </div>

            <div class="form-actions">
              <button type="reset" class="btn btn-secondary">
                Effacer le formulaire
              </button>
              <button type="submit" class="btn btn-primary">
                Envoyer ma candidature
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="back-link">
        <a href="../index.html">← Retour à la page de connexion</a>
      </div>
    </div>

    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
