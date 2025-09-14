<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionPossibleAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        $yes_or_no_questions = ['Oui', 'Non'];
        $questions = [
            // Questions pour l'Obésité de Classe 1 (IMC entre 30 et 34,9)
            
            // Antécédents médicaux
            [
                'maladie_chronique_id' => 1, 
                'texte_question' => "Avez-vous des antécédents de diabète de type 1, de diabète de type 2, d'hypertension,  de maladies cardiovasculaires et autres ?",
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Prenez-vous des médicaments actuellement qui sont liés à vos antécédents précédents ? Si oui, lesquels ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Prenez-vous actuellement l'un des types de médicaments suivants ?",
                'possible_answers' => [
                    "Médicaments pour baisser la glycémie (ex : metformine, glimepiride…)",
                    "Insuline",
                    "Médicaments pour perdre du poids (ex : orlistat, liraglutide…)",
                    "Médicaments pour la tension artérielle (ex : IEC, ARA2, bêta-bloquants…)",
                    "Médicaments pour le cholestérol (ex : statines)",
                    "Aucun de ces médicaments",
                ]
            ],
            // Habitudes alimentaires
            [
                'maladie_chronique_id' => 1, 
                'texte_question' => 'Décrivez une journée typique de vos repas et collations.'
            ],
            [
                'maladie_chronique_id' => 1, 
                'texte_question' => "Combien de portions de fruits et légumes consommez-vous par jour ?" ,
                'possible_answers' => [
                    '1-2',
                    '3-4',
                    '5 ou plus'
                ]
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Grignotez-vous entre les repas ? Si oui, quels types d\'aliments consommez-vous ?',
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "A quelle fréquence consommez-vous des boissons sucrées ou alcoolisées ?" ,
                'possible_answers' => [
                    'Tous les jours',
                    'Une fois par semaine',
                    'Occasionnellement',
                    'Jamais'
                ]
            ],

            // Comportement et bien etre
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Combien d'heures de sommeil avez-vous par nuit ? ",
                'possible_answers' => [
                    'Moins de 6',
                    '6-7',
                    '8-9',
                    'Plus de 9'
                ],
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Avez-vous l’apnée du sommeil ? ",
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 1, 
                'texte_question' => "Avez-vous récemment fait un régime de perte du poids ? Si oui, combien de poids avez-vous perdu ?",
                'possible_answers' => [
                    'Moins de 5 kg',
                    '5 - 10 kg',
                    'Plus de 10 kg',
                ],
            ],

            // Activité physique
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Combien de jours par semaine pratiquez-vous une activité physique ?",
                'possible_answers' => [
                    'Aucun',
                    '1',
                    '3-4',
                    '5-7'
                ]
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Combien de temps passez-vous en position assise chaque jour ?',
                'possible_answers' => [
                    'Moins de 4h',
                    '4h-6h',
                    '6h-8h',
                    'Plus de 8h'
                ]
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Quelles activités physiques appréciez-vous ? (marche, yoga, natation, etc.)'
            ],
            [
                'maladie_chronique_id' => 1, 
                'texte_question' => "Connaissez-vous votre fréquence cardiaque au repos ? Si oui, quelle est-elle ?",
                'possible_answers' => $yes_or_no_questions,
            ],

            // Comportements alimentaires
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Mangez-vous souvent par stress ou ennui ? (Oui/Non)',
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Avez-vous tendance à finir votre assiette même si vous n'avez plus faim ? (Oui/Non)",
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Avez-vous déjà suivi un régime alimentaire particulier ? Si oui, lequel, et a-t-il été efficace pour vous ?",
                'possible_answers' => $yes_or_no_questions,
            ],
            // [
            //     'maladie_chronique_id' => 1,
            //     'texte_question' => "Suivez-vous un régime alimentaire spécifique (végétarien, sans gluten, etc.) ?"
            // ],

            // Objectifs de santé
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Quel est votre objectif principal en matière de santé ? Perdre du poids ou améliorer votre forme physique ou autre ?'
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Combien de kg souhaitez-vous perdre ?',
                'possible_answers' => [
                    'Moins de 5 kg',
                    '5 - 10 kg',
                    'Plus de 10 kg'
                ]
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => "Quel niveau de changement etes vous prêts à suivre pour atteindre votre objectif ? ",
                'possible_answers' => [
                    "Moins de 5 kg : Léger (300kcal)",
                    "Moins de 5 kg : Moyen (400kcal)",
                    "Moins de 5 kg : Serein (500kcal)",
                    "Entre 5 et 10 kg : Léger (500kcal)",
                    "Entre 5 et 10 kg : Moyen (600kcal)",
                    "Entre 5 et 10 kg : Serein (700kcal)",
                    "Plus de 10 kg : Leger (500kg)",
                    "Plus de 10 kg : Moyen (700kg)",
                    "Plus de 10 kg : Serein (1000kg)",
                ]
            ],
            [
                'maladie_chronique_id' => 1,
                'texte_question' => 'Y a-t-il des événements récents ou à venir qui vous motivent à améliorer votre santé ?'
            ],

            // Questions pour l'Obésité de Classe 2 (IMC entre 35 et 39,9)

            // Antécédents médicaux
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Avez-vous été diagnostiqué avec des problèmes de santé liés à l\'obésité, comme le diabète de type 2, l\'hypertension, ou l\'hypercholestérolémie ?',
                'possible_answers' => $yes_or_no_questions
            ],
            // [
            //     'maladie_chronique_id' => 2,
            //     'texte_question' => "Avez-vous d'autres conditions médicales ?",
            //     'possible_answers' => $yes_or_no_questions
            // ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Avez-vous subi des opérations chirurgicales liées à votre poids, comme une chirurgie bariatrique ?',
                'possible_answers' => $yes_or_no_questions
            ],
            // [
            //     'maladie_chronique_id' => 2, 
            //     'texte_question' => 'Avez-vous des douleurs articulaires ou des limitations de mobilité ?',
            //     'possible_answers' => $yes_or_no_questions,
            // ],
            // [
            //     'maladie_chronique_id' => 2, 
            //     'texte_question' => 'Avez-vous récemment perdu ou pris du poids ? Si oui, combien et en combien de temps ? Pourquoi ?',
            //     'possible_answers' => $yes_or_no_questions,
            // ],

            // Habitudes alimentaires
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Avez-vous des restrictions alimentaires ? (allergies, intolérances, préférences culturelles ou religieuses)',
                'possible_answers' => [
                    'Allergies',
                    'Intolérences',
                    'Préférences culturelles',
                    'Réligieuses',
                    'Autres'
                ],
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Consommez-vous régulièrement des aliments riches en calories vides, comme les boissons sucrées, les fast-foods, ou les desserts ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Suivez-vous déjà un plan nutritionnel ou avez-vous consulté un diététicien auparavant ? Si oui, donnez nous les details en quelques lignes',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Buvez-vous régulièrement des boissons sucrées ou alcoolisées ? Si oui, Quand ? Quelle quantité ?',
                'possible_answers' => $yes_or_no_questions,
            ],

            // Comportements et bien etre
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Combien d\'heures de sommeil avez-vous par nuit ?',
                'possible_answers' => [
                    'Moins de 6h',
                    '6h-7h',
                    '8h-9h',
                    'Plus de 9h'
                ],
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous l'apnée du sommeil ? ",
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous des douleurs articulaires ou des limitations de mobilité ? Si oui, A quelle fréquence ?",
                'possible_answers' => $yes_or_no_questions
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous récemment fait un régime de perte du poids ? Si oui, combien de poids avez-vous perdu ?",
                'possible_answers' => $yes_or_no_questions
            ],
            


            // Activité physique et mobilité
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Combien de jours par semaine pratiquez-vous une activité physique ?',
                'possible_answers' => [
                    'Aucun', 
                    '1 à 3 jour',
                    '4 à 6 Jours',
                    '7 jours'
                ]
            ],
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Combien de temps passez-vous en position assise chaque jour ?',
                'possible_answers' => [
                    'Moins de 4h', 
                    '4h à 6h',
                    '46 à 8h',
                    'Plus de 8h'
                ]
            ],
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Quelles activités physiques appréciez-vous ? (marche, yoga ou autre) ?',
            ],
            // [
            //     'maladie_chronique_id' => 2,
            //     'texte_question' => 'Pratiquez-vous des activités de loisirs qui incluent de l\'activité physique, comme la marche, la natation, ou le jardinage ?',
            //     'possible_answers' => $yes_or_no_questions,
            // ],
            // [
            //     'maladie_chronique_id' => 2, 
            //     'texte_question' => 'Avez-vous envisagé de commencer un programme d\'exercices encadré par un professionnel ?',
            //     'possible_answers' => $yes_or_no_questions,
            // ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Quelle est votre fréquence cardiaque au repos (si vous la connaissez) ?'
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous envisagé de commencer un programme d'exercices encadré par un professionnel de sport",
                'possible_answers' => $yes_or_no_questions
            ],

            // Comportements alimentaires et émotions
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Comment vous sentez-vous émotionnellement en ce qui concerne votre poids ?'
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Mangez vous souvent par stress ou ennui?",
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous tendance à finir votre assiette même si vous n’avez plus faim ?",
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Avez-vous des habitudes alimentaires liées à des émotions, comme le stress ou la dépression ?",
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Avez-vous essayé de perdre du poids dans le passé ? Si oui, qu\'est-ce qui a fonctionné ou n\'a pas fonctionné pour vous ?',
                'possible_answers' => $yes_or_no_questions,
            ],

            // Objectifs de santé et attentes
            [
                'maladie_chronique_id' => 2, 
                'texte_question' => 'Quels sont vos objectifs à court et à long terme pour votre poids et votre santé ?',
                'possible_answers' => [
                    'Perdre moins 5 kg à court terme et maintenir un poids sain à long terme',
                    'Perdre entre 5 et 10 kg à court terme et maintenir un poids sain à long terme',
                    'Perdre plus de 10 kg à court terme et maintenir un poids sain à long terme',
                    'Améliorer ma santé générale et augmenter mon énergie',
                    'Atteindre un poids spécifique et maintenir un mode de vie sain',
                ]
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Avez-vous un réseau de soutien (famille, amis) pour vous aider dans votre parcours de perte de poids ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => 'Quelle est votre motivation principale pour perdre du poids ?'
            ],
            [
                'maladie_chronique_id' => 2,
                'texte_question' => "Quel niveau de changement etes vous prêts à suivre pour atteindre votre objectif ? ",
                'possible_answers' => [
                    "Moins de 5 kg : Léger (300kcal)",
                    "Moins de 5 kg : Moyen (400kcal)",
                    "Moins de 5 kg : Serein (500kcal)",
                    "Entre 5 et 10 kg : Léger (500kcal)",
                    "Entre 5 et 10 kg : Moyen (600kcal)",
                    "Entre 5 et 10 kg : Serein (700kcal)",
                    "Plus de 10 kg : Leger (500kg)",
                    "Plus de 10 kg : Moyen (700kg)",
                    "Plus de 10 kg : Serein (1000kg)",
                ]
            ],

            // Questions pour l'Obésité de Classe 3 (IMC de 40 ou plus)

            // État de santé général
            [
                'maladie_chronique_id' => 3, 
                'texte_question' => "Avez-vous été diagnostiqué avec des maladies chroniques graves liées à votre poids, comme l\'insuffisance cardiaque, les maladies rénales, ou des troubles respiratoires graves ?",
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous l\'apnée du sommeil ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Utilisez-vous un appareil CPAP (Continuous Positive Airway Pressure) pour l\'apnée du sommeil ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous des difficultés à effectuer des activités quotidiennes de base en raison de votre poids ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            // [
            //     'maladie_chronique_id' => 3,
            //     'texte_question' => 'Avez-vous des antécédents de problèmes de santé mentale ?',
            //     'possible_answers' => $yes_or_no_questions,
            // ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous récemment perdu ou pris du poids ? Si oui, combien et en combien de temps ? Pourquoi ? ',
                'possible_answers' => $yes_or_no_questions,
            ],

            // Habitudes alimentaires
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous des épisodes de compulsions alimentaires ou de boulimie ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Suivez-vous un régime alimentaire extrêmement restrictif ou avez-vous suivi des régimes yo-yo dans le passé ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3, 
                'texte_question' => 'Avez-vous des difficultés à contrôler les portions ou à manger de manière équilibrée ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Buvez-vous régulièrement des boissons sucrées ou alcoolisées ? Quand ? Quelle quantité ?',
                'possible_answers' => $yes_or_no_questions,
            ],
                       

            // Activité physique
        
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Votre poids vous empêche-t-il de pratiquer certaines activités physiques ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3, 
                'texte_question' => 'Quelle est votre capacité à vous déplacer de manière autonome (marche, escaliers, etc.) ?'
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous besoin d\'un équipement spécialisé pour vous aider à bouger ou à vous asseoir confortablement ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Quelle est votre fréquence cardiaque au repos (si vous la connaissez) ?'
            ],
            
            
            // Psychologique Comportements alimentaires
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous un suivi psychologique ou psychiatrique en lien avec votre poids ou votre alimentation ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3, 
                'texte_question' => 'Avez-vous déjà participé à des groupes de soutien pour la perte de poids ou l\'obésité ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Comment gérez-vous les critiques ou le stigma lié à votre poids ?'
            ],
        
        

        // Objectifs et soutien
        
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Avez-vous envisagé une chirurgie bariatrique ou d\'autres interventions médicales pour gérer votre poids ?',
                'possible_answers' => $yes_or_no_questions,
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Quels sont vos objectifs de santé immédiats et à long terme ?',
                'possible_answers' => [
                    "Mon objectif immédiat est de perdre 5 kg et à long terme amélqueiorer ma condition physique",
                    "Mon objectif immédiat est de perdre entre 5 et 10 kg et à long terme améliorer ma condition physique",
                    "Mon objectif immédiat est de perdre plus de 10 kg et à long terme améliorer ma condition physique",
                    "Maintenir un poids stable dans l'immédiat et vivre de manière plus saine en évitant les comorbidités à longue terme."
                ]
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => 'Quels aspects de votre vie vous motivent le plus pour changer vos habitudes alimentaires et physiques ?'
            ],
            [
                'maladie_chronique_id' => 3,
                'texte_question' => "Quel niveau de changement etes vous prêts à suivre pour atteindre votre objectif ? ",
                'possible_answers' => [
                    "Moins de 5 kg : Léger (300kcal)",
                    "Moins de 5 kg : Moyen (400kcal)",
                    "Moins de 5 kg : Serein (500kcal)",
                    "Entre 5 et 10 kg : Léger (500kcal)",
                    "Entre 5 et 10 kg : Moyen (600kcal)",
                    "Entre 5 et 10 kg : Serein (700kcal)",
                    "Plus de 10 kg : Leger (500kg)",
                    "Plus de 10 kg : Moyen (700kg)",
                    "Plus de 10 kg : Serein (1000kg)",
                ]
            ],     
        ];

        foreach($questions as $question) {

            $new_question = Question::create([
                'maladie_chronique_id' => $question['maladie_chronique_id'],
                'texte_question' => $question['texte_question'],
                'has_possible_answers' => isset($question['possible_answers']),
            ]);

            if(isset($question['possible_answers'])) {
                foreach($question['possible_answers'] as $answer) {
                    QuestionPossibleAnswer::create([
                        'question_id' => $new_question->id,
                        'value' => $answer
                    ]);
                }
            }
        }
    }
}
