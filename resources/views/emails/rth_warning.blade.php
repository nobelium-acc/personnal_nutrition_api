<!DOCTYPE html>
<html>
<head>
    <title>Alerte RTH - Healing Nutrition</title>
</head>
<body>
    <p>Bonjour {{ $userName }},</p>
    <p>Nous vous remercions d’avoir utilisé l’application Healing Nutrition.</p>
    <p>Après analyse de vos mesures anthropométriques, nous avons calculé votre <strong>Rapport Taille/Hanche (RTH)</strong>.</p>
    
    <h3>🔎 Détails de votre RTH :</h3>
    <ul>
        <li>Votre RTH calculé : <strong>{{ $rth }}</strong></li>
        <li>Seuil recommandé pour votre sexe ({{ $gender === 'M' ? 'Homme' : 'Femme' }}) : <strong>{{ $threshold }}</strong></li>
    </ul>

    <h3>⚠️ Risques pour la santé :</h3>
    <p>Une valeur de RTH supérieure au seuil recommandé indique une accumulation excessive de graisse abdominale (graisse viscérale). Cela est associé à des risques accrus de :</p>
    <ul>
        <li>Diabète de type 2</li>
        <li>Hypertension artérielle</li>
        <li>Maladies cardiovasculaires</li>
    </ul>

    <p>Cordialement,<br>
    L’équipe Healing Nutrition</p>
</body>
</html>
