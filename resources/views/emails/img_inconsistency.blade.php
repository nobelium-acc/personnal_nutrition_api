<!DOCTYPE html>
<html>
<head>
    <title>Incohérence Indice de Masse Grasse (IMG)</title>
</head>
<body>
    <p>Bonjour {{ $userName }},</p>
    <p>Nous vous remercions d’avoir utilisé l’application Healing Nutrition et d’avoir pris le temps de renseigner vos informations corporelles.</p>
    <p>Après analyse de vos données, notre système a identifié une incohérence entre votre seuil d’obésité et le résultat de votre Indice de Masse Grasse (IMG), calculé automatiquement à partir de vos mesures anthropométriques.</p>
    
    <h3>🔎 Détails de l’analyse</h3>
    <p>Les éléments suivants ont été utilisés pour estimer votre pourcentage de masse grasse :</p>
    <ul>
        <li>Sexe renseigné : {{ $data['gender'] === 'M' ? 'Homme' : 'Femme' }}</li>
        <li>Taille : {{ $data['height'] }} cm</li>
        <li>Tour de taille : {{ $data['waist'] }} cm</li>
        <li>Tour du cou : {{ $data['neck'] }} cm</li>
        @if(isset($data['hip']) && $data['hip'] > 0)
        <li>Tour de hanche : {{ $data['hip'] }} cm</li>
        @endif
    </ul>

    <h3>🧮 IMG calculé par notre système : {{ $img }} %</h3>

    <h3>📊 Interprétation selon les seuils médicaux</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Sexe</th>
                <th>Seuil d’obésité selon l’IMG</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Homme</td>
                <td>IMG ≥ 25 %</td>
            </tr>
            <tr>
                <td>Femme</td>
                <td>IMG ≥ 32 %</td>
            </tr>
        </tbody>
    </table>
    <p>D’après ces critères, votre IMG est <strong>inférieur</strong> au seuil d’obésité recommandé pour votre sexe.</p>

    <h3>⚠️ Problème rencontré</h3>
    <p>Malgré ce résultat, vous avez indiqué dans l’application souffrir d’obésité.</p>
    <p>Or, selon les règles d’interprétation médicales utilisées par Healing Nutrition, un IMG inférieur aux seuils définis ne permet pas de conclure à une situation d’obésité, même si l’IMC étant élevé vous déclarant obèse. Ce cas peut notamment concerner des profils présentant une masse musculaire importante.</p>
    <p>Cette incohérence empêche la génération d’un plan nutritionnel fiable et adapté à votre profil. Afin de garantir un accompagnement nutritionnel personnalisé et sécurisé, nous vous invitons à :</p>
    <ol>
        <li>Revenir dans l’application,</li>
        <li>Vérifier vos mesures corporelles (tours et taille),</li>
    </ol>
    <p>Une fois ces informations mises à jour, notre système pourra relancer l'analyse et générer automatiquement un plan nutritionnel conforme à votre profil.</p>
    <p>Nous restons à votre disposition pour toute information complémentaire et vous remercions pour votre compréhension.</p>

    <p>Cordialement,<br>
    L’équipe Healing Nutrition</p>
</body>
</html>
