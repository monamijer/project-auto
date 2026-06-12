<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Auto-École Pro | Connexion</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Auto-École Pro</h1>
                <p>Système de Gestion</p>
            </div>
            
            <form class="login-form" action="pages/dashboard.html">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="exemple@autoecole.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                
                <div class="login-footer">
                    <a href="pages/inscription.html" class="candidature-link">Déposer une candidature d'inscription</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>