<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Guide Nutritionnel Personnalisé</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 30px; }
        h2 { color: #2980b9; margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        h3 { color: #16a085; margin-top: 15px; }
        .section { margin-bottom: 25px; }
        .info-grid { display: table; width: 100%; margin-bottom: 15px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 5px; width: 50%; }
        .label { font-weight: bold; color: #555; }
        .value { color: #000; }
        .highlight { background-color: #ecf0f1; padding: 10px; border-radius: 5px; border-left: 4px solid #3498db; }
        .warning { color: #c0392b; font-weight: bold; }
        .note { color: #7f8c8d; font-style: italic; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #333; }
        .page-break { page-break-after: always; }
        .menu-day { margin-bottom: 20px; border: 1px solid #eee; padding: 10px; border-radius: 5px; }
        .menu-day h4 { margin: 0 0 10px 0; background: #f9f9f9; padding: 5px; }
        .meal { margin-bottom: 10px; }
        .meal-title { font-weight: bold; color: #d35400; }
        .regulatory-box { border: 2px solid #e74c3c; padding: 15px; margin: 20px 0; background-color: #fff5f5; page-break-inside: avoid; }
        .regulatory-title { color: #c0392b; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="border: none;">PLAN NUTRITIONNEL PERSONNALISÉ</h1>
        <p style="font-size: 14px; color: #7f8c8d;">Généré par votre Assistant Nutritionnel</p>
    </div>

    <!-- 1. Profil du patient -->
    <div class="section">
        <h1>Profil du Patient</h1>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell"><span class="label">Nom :</span> {{ $user->nom }}</div>
                <div class="info-cell"><span class="label">Prénom :</span> {{ $user->prenom }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell"><span class="label">Sexe :</span> {{ $user->sexe }}</div>
                <div class="info-cell"><span class="label">Âge :</span> {{ $user->age }} ans</div>
            </div>
            <div class="info-row">
                <div class="info-cell"><span class="label">Poids actuel :</span> {{ $user->poids }} kg</div>
                <div class="info-cell"><span class="label">Taille :</span> {{ $user->taille }} m</div>
            </div>
            <div class="info-row">
                <div class="info-cell"><span class="label">Tour de taille :</span> {{ $user->tour_de_taille }} cm</div>
                <div class="info-cell"><span class="label">Tour de hanches :</span> {{ $user->tour_de_hanche }} cm</div>
            </div>
            <div class="info-row">
                <div class="info-cell"><span class="label">Tour de cou :</span> {{ $user->tour_du_cou ?? 'N/A' }} cm</div>
                <div class="info-cell"><span class="label">Niveau d'activité :</span> {{ $user->niveau_d_activite_physique }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Données de Santé -->
    <div class="section">
        <h1>Bilan de Santé</h1>
        <ul>
            <li><strong>Indice de Masse Corporelle (IMC) :</strong> {{ $metrics['imc'] }} kg/m² ({{ $metrics['interpretation_imc'] }})</li>
            <li><strong>Rapport Taille/Hanche (RTH) :</strong> {{ $metrics['rth'] }} cm</li>
            <li><strong>Métabolisme de Base (BMR) :</strong> {{ $metrics['bmr'] }} kcal/jour</li>
            <li><strong>Dépense Énergétique Totale (TDEE) :</strong> {{ $metrics['tdee'] }} kcal/jour</li>
            <li><strong>Indice de Masse Grasse (IMG) :</strong> {{ $metrics['img'] }} %</li>
        </ul>
    </div>

    <!-- 3. Objectifs et Recommandations -->
    <div class="section">
        <h1>Objectifs et Recommandations</h1>
        <div class="highlight">
            <p><strong>Objectif :</strong> {{ $goals['objectif'] }}</p>
            <p><strong>Perte souhaitée :</strong> {{ $goals['perte_poids'] }}</p>
            <p><strong>Niveau d'engagement :</strong> {{ $goals['niveau_changement'] }}</p>
            <hr style="border: 0; border-top: 1px solid #ccc; margin: 10px 0;">
            <p style="font-size: 14px;"><strong>Apport Calorique Recommandé : {{ $recommendations['calories'] }} kcal/jour</strong></p>
        </div>

        <h2>Répartition des Macronutriments</h2>
        <p>Pour atteindre vos objectifs, voici comment répartir vos apports quotidiens :</p>
        <table>
            <thead>
                <tr>
                    <th>Macronutriment</th>
                    <th>Pourcentage</th>
                    <th>Quantité (Grammes)</th>
                    <th>Énergie (kcal)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Protéines</strong></td>
                    <td>{{ $recommendations['macros_p_percent'] }} %</td>
                    <td>{{ $recommendations['macros_p_grams'] }} g</td>
                    <td>{{ $recommendations['macros_p_grams'] * 4 }} kcal</td>
                </tr>
                <tr>
                    <td><strong>Glucides</strong></td>
                    <td>{{ $recommendations['macros_g_percent'] }} %</td>
                    <td>{{ $recommendations['macros_g_grams'] }} g</td>
                    <td>{{ $recommendations['macros_g_grams'] * 4 }} kcal</td>
                </tr>
                <tr>
                    <td><strong>Lipides</strong></td>
                    <td>{{ $recommendations['macros_l_percent'] }} %</td>
                    <td>{{ $recommendations['macros_l_grams'] }} g</td>
                    <td>{{ $recommendations['macros_l_grams'] * 9 }} kcal</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- 4. Plan d'Intervention Détaillé -->
    <div class="section">
        <h1>Plan d'Intervention Nutritionnelle</h1>
        <p>Voici les piliers de votre transformation, adaptés à vos besoins spécifiques :</p>
        
        @if(isset($plan_intervention) && is_array($plan_intervention))
            @foreach($plan_intervention as $block)
                @if(isset($block['titre']) && isset($block['contenu']))
                    <div style="margin-bottom: 20px;">
                        <h3>{{ $block['titre'] }}</h3>
                        <div style="white-space: pre-wrap;">{{ $block['contenu'] }}</div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    <!-- 5. Vos Réponses et Notre Analyse (Q&A) -->
    <div class="section">
        <h1>Vos Habitudes et Notre Analyse</h1>
        <p>Retour sur les points clés de votre questionnaire :</p>

        @foreach($qa as $item)
            <div style="margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px;">
                <div style="font-weight: bold; color: #2c3e50; margin-bottom: 5px;">{{ $item['question'] }}</div>
                <div style="margin-left: 15px;">
                    <span style="color: #7f8c8d;">Votre réponse :</span> <strong>{{ $item['reponse'] }}</strong><br>
                    <!-- La recommandation est maintenant couverte en détail dans le plan d'intervention ci-dessus -->
                </div>
            </div>
        @endforeach
    </div>

    <div class="page-break"></div>

    <!-- 5. Guide Alimentaire 90 Jours -->
    <div class="section">
        <h1>Guide Alimentaire sur 90 Jours</h1>
        <p>Ce plan est conçu pour être suivi sur 3 mois. Il respecte vos besoins caloriques et vos préférences.</p>
        
        <!-- Affichage des menus (Groupé par semaine ou jour selon l'espace) -->
        <!-- Pour éviter un PDF de 500 pages, on peut afficher un modèle type ou la liste complète. Ici affichons les 14 premiers jours en détail et résumons le reste si besoin, ou tout si demandé. -->
        <!-- Vu la demande "Guide complet de 90 jours", on génère tout. -->
        
        @foreach($menu as $day)
            <div class="menu-day">
                <h4>Jour {{ $day['jour'] }} @if(isset($day['type']) && $day['type'] != 'Ajusté') <span class="warning">({{ $day['type'] }})</span> @endif</h4>
                
                @if(isset($day['repas']) && isset($day['repas']['Petit-déjeuner']))
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-cell">
                                <div class="meal-title">Petit-déjeuner</div>
                                @foreach($day['repas']['Petit-déjeuner'] as $item)
                                    <div>- {{ $item['nom'] ?? 'N/A' }} ({{ $item['portion_recommandee'] ?? '' }})</div>
                                @endforeach
                            </div>
                            <div class="info-cell">
                                <div class="meal-title">Déjeuner</div>
                                @if(isset($day['repas']['Déjeuner']))
                                    @foreach($day['repas']['Déjeuner'] as $item)
                                        <div>- {{ $item['nom'] ?? 'N/A' }} ({{ $item['portion_recommandee'] ?? '' }})</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell">
                                <div class="meal-title">Collation(s)</div>
                                @if(!empty($day['collations']))
                                    @foreach($day['collations'] as $item)
                                        <div>- {{ $item['nom'] ?? 'N/A' }} ({{ $item['portion'] ?? 'Standard' }})</div>
                                    @endforeach
                                @else
                                    <div>Aucune</div>
                                @endif
                            </div>
                            <div class="info-cell">
                                <div class="meal-title">Dîner</div>
                                @if(isset($day['repas']['Dîner']))
                                    @foreach($day['repas']['Dîner'] as $item)
                                        <div>- {{ $item['nom'] ?? 'N/A' }} ({{ $item['portion_recommandee'] ?? '' }})</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                   <p>{{ $day['repas']['Note'] ?? 'Détails du menu non disponibles pour ce jour.' }}</p>
                @endif
            </div>

            @if($loop->iteration % 3 == 0)
                <div class="page-break"></div>
            @endif
        @endforeach

    </div>

    <div class="page-break"></div>

    <!-- 6. Notifications Réglementaires -->
    <div class="section">
        <div class="regulatory-box">
            <div class="regulatory-title">Notifications Réglementaires</div>
            <ul>
                <li>Le plan nutritionnel proposé est prévu pour une durée maximale de 3 mois, soit 90 jours.</li>
                <li>Pour les personnes ayant des antécédents médicaux et prenant des médicaments, <strong>ne cessez jamais votre traitement</strong>. Continuez de suivre votre prescription tout en respectant nos recommandations. En cas de problème, veuillez consulter votre médecin traitant.</li>
                <li>Suivez scrupuleusement le plan nutritionnel et respectez les recommandations nutritionnelles indiquées.</li>
                <li>Certaines recommandations peuvent inclure des propositions de repas à titre éducatif. L’essentiel est de respecter les apports en protéines, glucides et lipides définis pour chaque jour.</li>
                <li>Pour les collations, suivez les instructions selon votre niveau d’activité :
                    <ul>
                        <li>Sédentaire : 0 – 1 collation/jour</li>
                        <li>Légèrement actif : 1 collation/jour</li>
                        <li>Modérément actif : 1 – 2 collations/jour</li>
                        <li>Très actif : 2 collations/jour</li>
                        <li>Extrêmement actif : 2 collations/jour (nécessaires)</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

</body>
</html>
