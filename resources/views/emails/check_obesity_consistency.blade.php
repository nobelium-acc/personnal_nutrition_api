<!DOCTYPE html>
<html>
<head>
    <title>Incohérence détectée</title>
</head>
<body>
    <p>Bonjour {{ $userName }},</p>
    <p>Nous vous remercions d’avoir utilisé l’application Healing Nutrition et d’avoir pris le temps de renseigner vos informations personnelles.</p>
    <p>Après analyse de vos données, nous avons constaté une incohérence entre le type d’obésité que vous avez sélectionné dans l’application et le résultat calculé automatiquement par notre système à partir de votre poids et de votre taille.</p>
    
    <h3>🔎 Détails de l’analyse :</h3>
    <ul>
        <li>Poids renseigné : {{ $data['weight'] }} kg</li>
        <li>Taille renseignée : {{ $data['height'] }} cm → soit {{ $data['height'] / 100 }} m</li>
        <li>IMC calculé : {{ $data['weight'] }} / ({{ $data['height'] / 100 }})² = {{ $imc }}</li>
    </ul>

    <h3>🧮 Résultat du calcul :</h3>
    <p>D’après notre système, votre indice de masse corporelle (IMC) est de {{ $imc }}, ce qui correspond à une <strong>{{ $calcGrade }}</strong> selon les standards médicaux :</p>
    
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Type d’obésité</th>
                <th>Valeurs de l’IMC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Obésité modérée (Grade 1)</td>
                <td>30 ≤ IMC < 34,9</td>
            </tr>
            <tr>
                <td>Obésité sévère (Grade 2)</td>
                <td>35 ≤ IMC < 39,9</td>
            </tr>
            <tr>
                <td>Obésité morbide (Grade 3)</td>
                <td>IMC ≥ 40</td>
            </tr>
        </tbody>
    </table>

    <h3>⚠️ Problème rencontré :</h3>
    <p>Lors de votre sélection dans l’application, vous avez indiqué souffrir d’une forme d’obésité différente de celle déduite par nos calculs. Cette incohérence nous empêche de générer un plan nutritionnel adapté et fiable à votre profil.</p>
    <p>Afin de garantir un accompagnement nutritionnel personnalisé et sécuritaire, nous vous invitons à revenir à l’écran de sélection et corriger le type d’obésité pour qu’il corresponde à votre IMC réel.</p>
    <p>Une fois cette correction effectuée, notre système pourra générer automatiquement un plan nutritionnel conforme à votre profil.</p>

    <p>Cordialement,<br>
    L’équipe Healing Nutrition</p>
</body>
</html>
