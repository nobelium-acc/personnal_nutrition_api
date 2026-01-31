<?php

namespace App\Services;

class NoPathologyNutritionDataService
{
    public function getMenus()
    {
        return [
            'Sédentaire' => [ // 30% Protéines, 35% Glucides, 35% Lipides
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Omelette aux légumes (2 œufs)', 'base' => 150, 'p' => 12.6, 'g' => 3.0, 'l' => 10.2],
                            ['nom' => 'Pain complet (petite tranche)', 'base' => 30, 'p' => 2.4, 'g' => 12.0, 'l' => 0.9],
                            ['nom' => 'Salade de papaye verte', 'base' => 100, 'p' => 0.5, 'g' => 10.0, 'l' => 0.2],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce graine (poisson fumé)', 'base' => 200, 'p' => 12.0, 'g' => 10.0, 'l' => 14.0],
                            ['nom' => 'Pâte de maïs', 'base' => 150, 'p' => 3.2, 'g' => 39.0, 'l' => 1.4],
                            ['nom' => 'Salade concombre-tomates', 'base' => 150, 'p' => 1.0, 'g' => 8.0, 'l' => 0.2],
                            ['nom' => 'Poisson braisé', 'base' => 120, 'p' => 24.0, 'g' => 0.0, 'l' => 4.8],
                        ],
                        'Dîner' => [
                            ['nom' => 'Soupe de poisson + légumes', 'base' => 300, 'p' => 9.0, 'g' => 12.0, 'l' => 6.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.4, 'g' => 26.0, 'l' => 0.2],
                            ['nom' => 'Salade d’avocat', 'base' => 100, 'p' => 1.6, 'g' => 8.0, 'l' => 10.0],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de maïs enrichie', 'base' => 150, 'p' => 2.0, 'g' => 20.0, 'l' => 1.0],
                            ['nom' => 'Lait concentré non sucré', 'base' => 50, 'p' => 1.5, 'g' => 5.0, 'l' => 2.0],
                            ['nom' => 'Arachides grillées', 'base' => 30, 'p' => 9.0, 'g' => 9.0, 'l' => 15.0],
                            ['nom' => 'Avocat (1/2)', 'base' => 70, 'p' => 1.1, 'g' => 6.0, 'l' => 13.3],
                            ['nom' => 'Eau citronnée', 'base' => 250, 'p' => 0.0, 'g' => 1.0, 'l' => 0.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Ragoût de légumes', 'base' => 250, 'p' => 5.0, 'g' => 20.0, 'l' => 5.0],
                            ['nom' => 'Poulet DG modifié', 'base' => 120, 'p' => 26.4, 'g' => 0.0, 'l' => 6.0],
                            ['nom' => 'Riz blanc cuit', 'base' => 100, 'p' => 2.5, 'g' => 28.0, 'l' => 0.3],
                            ['nom' => 'Salade verte', 'base' => 100, 'p' => 1.1, 'g' => 2.0, 'l' => 0.1],
                        ],
                        'Dîner' => [
                            ['nom' => 'Haricots rouges sauce tomate', 'base' => 180, 'p' => 11.0, 'g' => 30.0, 'l' => 3.0],
                            ['nom' => 'Alloco (plantain frit)', 'base' => 80, 'p' => 1.2, 'g' => 24.0, 'l' => 8.0],
                            ['nom' => 'Poisson grillé', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Salade de laitue', 'base' => 100, 'p' => 0.9, 'g' => 1.5, 'l' => 0.1],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa (maïs fermenté)', 'base' => 200, 'p' => 3.2, 'g' => 34.0, 'l' => 2.0],
                            ['nom' => 'Sauce d’arachide légère', 'base' => 120, 'p' => 6.0, 'g' => 8.0, 'l' => 10.0],
                            ['nom' => 'Œuf dur', 'base' => 50, 'p' => 6.3, 'g' => 0.6, 'l' => 5.3],
                            ['nom' => 'Orange pressée', 'base' => 250, 'p' => 1.7, 'g' => 22.0, 'l' => 0.2],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce d’arachide allégée', 'base' => 180, 'p' => 8.0, 'g' => 12.0, 'l' => 14.0],
                            ['nom' => 'Bœuf maigre', 'base' => 120, 'p' => 26.4, 'g' => 0.0, 'l' => 6.0],
                            ['nom' => 'Igname (pâte)', 'base' => 120, 'p' => 1.7, 'g' => 31.2, 'l' => 0.2],
                            ['nom' => 'Salade de carottes râpées', 'base' => 150, 'p' => 1.2, 'g' => 11.0, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce tomate aux légumes', 'base' => 250, 'p' => 4.0, 'g' => 20.0, 'l' => 6.0],
                            ['nom' => 'Couscous de maïs', 'base' => 100, 'p' => 3.2, 'g' => 70.0, 'l' => 1.8],
                            ['nom' => 'Poulet grillé (sans peau)', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 4.0],
                            ['nom' => 'Salade mixte', 'base' => 150, 'p' => 1.6, 'g' => 5.0, 'l' => 0.5],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akoumé (pâte de maïs)', 'base' => 200, 'p' => 4.0, 'g' => 40.0, 'l' => 2.0],
                            ['nom' => 'Soupe de poisson légère', 'base' => 300, 'p' => 18.0, 'g' => 6.0, 'l' => 4.0],
                            ['nom' => 'Avocat (1/2)', 'base' => 70, 'p' => 1.1, 'g' => 6.0, 'l' => 13.3],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Atassi (riz + haricots)', 'base' => 150, 'p' => 6.8, 'g' => 35.0, 'l' => 1.0],
                            ['nom' => 'Poisson frit', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 10.0],
                            ['nom' => 'Salade de chou', 'base' => 150, 'p' => 1.3, 'g' => 7.0, 'l' => 0.2],
                            ['nom' => 'Tomates fraîches', 'base' => 100, 'p' => 0.9, 'g' => 3.9, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Soupe de gombos', 'base' => 350, 'p' => 7.0, 'g' => 10.0, 'l' => 3.5],
                            ['nom' => 'Foufou d’igname', 'base' => 100, 'p' => 1.4, 'g' => 26.0, 'l' => 0.3],
                            ['nom' => 'Crabe ou crevettes', 'base' => 80, 'p' => 17.6, 'g' => 0.0, 'l' => 1.6],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Noix de cajou + 1 petit fruit', 'p' => 4.8, 'g' => 20.0, 'l' => 12.2],
                    ['nom' => 'Yaourt nature + ananas frais', 'p' => 6.3, 'g' => 19.0, 'l' => 3.7],
                    ['nom' => 'Arachides bouillies', 'p' => 6.0, 'g' => 5.0, 'l' => 15.0],
                    ['nom' => 'Mangue fraîche', 'p' => 1.2, 'g' => 25.0, 'l' => 0.6],
                ]
            ],
            'Légèrement actif' => [ // 30% Protéines, 40% Glucides, 30% Lipides
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil enrichie', 'base' => 200, 'p' => 2.8, 'g' => 36.0, 'l' => 2.0],
                            ['nom' => 'Œuf à la coque', 'base' => 60, 'p' => 7.6, 'g' => 0.66, 'l' => 6.36],
                            ['nom' => 'Banane plantain bouillie', 'base' => 80, 'p' => 0.64, 'g' => 23.1, 'l' => 0.14],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz jollof', 'base' => 180, 'p' => 5.0, 'g' => 50.4, 'l' => 0.54],
                            ['nom' => 'Poulet grillé', 'base' => 140, 'p' => 37.8, 'g' => 0.0, 'l' => 4.2],
                            ['nom' => 'Salade composée', 'base' => 50, 'p' => 1.0, 'g' => 3.0, 'l' => 0.3],
                            ['nom' => 'Vinaigrette légère', 'base' => 15, 'p' => 0.0, 'g' => 3.0, 'l' => 5.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce feuilles de patate + arachide', 'base' => 150, 'p' => 6.0, 'g' => 8.0, 'l' => 12.0],
                            ['nom' => 'Igname pilée', 'base' => 120, 'p' => 2.0, 'g' => 33.4, 'l' => 0.24],
                            ['nom' => 'Viande de chèvre grillée', 'base' => 100, 'p' => 27.0, 'g' => 0.0, 'l' => 14.0],
                            ['nom' => 'Salade verte', 'base' => 50, 'p' => 1.0, 'g' => 3.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Pain complet', 'base' => 60, 'p' => 5.4, 'g' => 24.0, 'l' => 2.4],
                            ['nom' => 'Omelette (2 œufs)', 'base' => 100, 'p' => 12.6, 'g' => 1.1, 'l' => 10.6],
                            ['nom' => 'Avocat (1/2)', 'base' => 70, 'p' => 1.4, 'g' => 6.3, 'l' => 10.5],
                            ['nom' => 'Jus de bissap (sans sucre)', 'base' => 200, 'p' => 0.0, 'g' => 8.0, 'l' => 0.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte rouge avec sauce gombo', 'base' => 180, 'p' => 7.2, 'g' => 60.0, 'l' => 1.8],
                            ['nom' => 'Poisson braisé', 'base' => 140, 'p' => 28.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Légumes sautés', 'base' => 100, 'p' => 1.0, 'g' => 4.0, 'l' => 5.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Ragoût de haricots blancs', 'base' => 150, 'p' => 13.2, 'g' => 30.0, 'l' => 0.75],
                            ['nom' => 'Gari', 'base' => 100, 'p' => 0.3, 'g' => 28.0, 'l' => 0.1],
                            ['nom' => 'Poisson fumé', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 2.0],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akpan (bouillie de maïs)', 'base' => 200, 'p' => 3.0, 'g' => 30.0, 'l' => 2.0],
                            ['nom' => 'Beignet haricot (2 petits)', 'base' => 50, 'p' => 7.0, 'g' => 20.0, 'l' => 3.0],
                            ['nom' => 'Orange', 'base' => 150, 'p' => 1.35, 'g' => 16.5, 'l' => 0.15],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Tchep (riz au poisson)', 'base' => 180, 'p' => 5.0, 'g' => 50.4, 'l' => 0.54],
                            ['nom' => 'Poisson', 'base' => 140, 'p' => 37.8, 'g' => 0.0, 'l' => 3.0],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                            ['nom' => 'Salade de carottes', 'base' => 50, 'p' => 0.45, 'g' => 5.0, 'l' => 0.1],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce arachide légère', 'base' => 100, 'p' => 6.0, 'g' => 8.0, 'l' => 12.0],
                            ['nom' => 'Couscous de manioc', 'base' => 120, 'p' => 0.48, 'g' => 33.6, 'l' => 0.12],
                            ['nom' => 'Poulet villageois', 'base' => 120, 'p' => 32.0, 'g' => 0.0, 'l' => 3.0],
                            ['nom' => 'Légumes vapeur', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Sandwich pain complet (sardines, tomates, oignons)', 'base' => 100, 'p' => 25.8, 'g' => 27.0, 'l' => 12.5],
                            ['nom' => 'Papaye fraîche', 'base' => 150, 'p' => 0.75, 'g' => 16.5, 'l' => 0.15],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Spaghetti sauce tomate-viande', 'base' => 200, 'p' => 8.0, 'g' => 47.5, 'l' => 1.75],
                            ['nom' => 'Viande hachée maigre', 'base' => 120, 'p' => 25.0, 'g' => 0.0, 'l' => 10.0],
                            ['nom' => 'Salade verte', 'base' => 50, 'p' => 0.5, 'g' => 2.0, 'l' => 0.1],
                        ],
                        'Dîner' => [
                            ['nom' => 'Soupe de crabe aux légumes', 'base' => 200, 'p' => 30.0, 'g' => 16.0, 'l' => 6.0],
                            ['nom' => 'Foufou de maïs', 'base' => 120, 'p' => 1.8, 'g' => 33.6, 'l' => 0.6],
                            ['nom' => 'Salade d’avocat', 'base' => 50, 'p' => 1.0, 'g' => 4.0, 'l' => 11.0],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Fruit de saison + arachides (25g)', 'p' => 9.0, 'g' => 28.0, 'l' => 14.2],
                    ['nom' => 'Galette de soja locale', 'p' => 10.5, 'g' => 4.0, 'l' => 3.5],
                    ['nom' => 'Noix de coco fraîche', 'p' => 1.0, 'g' => 5.0, 'l' => 3.0],
                    ['nom' => 'Yaourt local', 'p' => 4.0, 'g' => 6.0, 'l' => 3.0],
                ]
            ],
            'Modérément actif' => [ // 27% Protéines, 45% Glucides, 28% Lipides
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de maïs enrichie', 'base' => 250, 'p' => 3.75, 'g' => 37.5, 'l' => 2.5],
                            ['nom' => 'Pain complet + beurre cacahuète', 'base' => 90, 'p' => 11.3, 'g' => 32.0, 'l' => 12.8],
                            ['nom' => 'Banane', 'base' => 100, 'p' => 1.0, 'g' => 23.0, 'l' => 0.2],
                            ['nom' => 'Lait', 'base' => 200, 'p' => 6.6, 'g' => 9.6, 'l' => 6.4],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz blanc + sauce tomate-poisson', 'base' => 300, 'p' => 10.6, 'g' => 66.0, 'l' => 2.6],
                            ['nom' => 'Poisson braisé', 'base' => 150, 'p' => 40.5, 'g' => 0.0, 'l' => 4.5],
                            ['nom' => 'Alloco', 'base' => 100, 'p' => 1.0, 'g' => 30.0, 'l' => 8.0],
                            ['nom' => 'Grande salade mixte', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Haricots en sauce', 'base' => 200, 'p' => 16.0, 'g' => 40.0, 'l' => 2.0],
                            ['nom' => 'Gari', 'base' => 120, 'p' => 0.36, 'g' => 33.6, 'l' => 0.12],
                            ['nom' => 'Viande de bœuf grillée', 'base' => 120, 'p' => 31.2, 'g' => 0.0, 'l' => 9.6],
                            ['nom' => 'Salade de chou', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa + sauce arachide', 'base' => 200, 'p' => 5.25, 'g' => 26.5, 'l' => 7.5],
                            ['nom' => '2 œufs brouillés', 'base' => 100, 'p' => 12.6, 'g' => 1.1, 'l' => 10.6],
                            ['nom' => 'Avocat', 'base' => 70, 'p' => 1.4, 'g' => 6.3, 'l' => 10.5],
                            ['nom' => 'Jus d\'orange frais', 'base' => 150, 'p' => 0.9, 'g' => 15.0, 'l' => 0.2],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte d\'igname', 'base' => 180, 'p' => 3.06, 'g' => 50.4, 'l' => 0.36],
                            ['nom' => 'Sauce graine (poisson fumé)', 'base' => 230, 'p' => 32.0, 'g' => 8.0, 'l' => 14.6],
                            ['nom' => 'Légumes sautés', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Atassi complet (riz-haricots)', 'base' => 200, 'p' => 6.0, 'g' => 50.0, 'l' => 1.0],
                            ['nom' => 'Poulet grillé', 'base' => 120, 'p' => 32.4, 'g' => 0.0, 'l' => 3.6],
                            ['nom' => 'Alloco', 'base' => 80, 'p' => 0.8, 'g' => 24.0, 'l' => 6.4],
                            ['nom' => 'Salade verte', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil + lait concentré', 'base' => 300, 'p' => 7.25, 'g' => 49.5, 'l' => 6.5],
                            ['nom' => 'Beignet haricot (3 petits)', 'base' => 75, 'p' => 10.5, 'g' => 30.0, 'l' => 4.5],
                            ['nom' => 'Papaye', 'base' => 100, 'p' => 0.5, 'g' => 11.0, 'l' => 0.1],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz jollof', 'base' => 220, 'p' => 6.16, 'g' => 61.6, 'l' => 0.66],
                            ['nom' => 'Poulet braisé', 'base' => 150, 'p' => 40.5, 'g' => 0.0, 'l' => 4.5],
                            ['nom' => 'Salade composée', 'base' => 150, 'p' => 1.5, 'g' => 7.5, 'l' => 0.3],
                            ['nom' => 'Plantain bouilli', 'base' => 100, 'p' => 1.0, 'g' => 30.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Couscous de manioc', 'base' => 150, 'p' => 1.5, 'g' => 42.0, 'l' => 0.15],
                            ['nom' => 'Ragoût de viande', 'base' => 120, 'p' => 18.0, 'g' => 6.0, 'l' => 9.6],
                            ['nom' => 'Salade d’avocat', 'base' => 100, 'p' => 4.0, 'g' => 8.0, 'l' => 22.0],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Sandwich complet (pain, œuf, avocat, tomate)', 'base' => 220, 'p' => 14.05, 'g' => 35.05, 'l' => 15.7],
                            ['nom' => 'Banane', 'base' => 100, 'p' => 1.0, 'g' => 23.0, 'l' => 0.2],
                            ['nom' => 'Thé au lait', 'base' => 150, 'p' => 0.75, 'g' => 7.5, 'l' => 3.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte de maïs', 'base' => 200, 'p' => 3.0, 'g' => 56.0, 'l' => 0.6],
                            ['nom' => 'Sauce gombo-crabe', 'base' => 100, 'p' => 10.0, 'g' => 5.0, 'l' => 3.0],
                            ['nom' => 'Poisson fumé', 'base' => 140, 'p' => 28.0, 'g' => 0.0, 'l' => 2.8],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Igname pilée', 'base' => 150, 'p' => 2.25, 'g' => 42.0, 'l' => 0.3],
                            ['nom' => 'Sauce feuilles d’amarante', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Poisson grillé', 'base' => 120, 'p' => 32.4, 'g' => 0.0, 'l' => 3.6],
                            ['nom' => 'Salade mixte', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Dattes + arachides', 'p' => 8.0, 'g' => 41.0, 'l' => 14.2],
                    ['nom' => 'Smoothie fruits locaux', 'p' => 2.0, 'g' => 20.0, 'l' => 1.0],
                    ['nom' => 'Mangue séchée + noix', 'p' => 5.5, 'g' => 45.0, 'l' => 14.2],
                    ['nom' => 'Yaourt + granola maison', 'p' => 7.0, 'g' => 26.0, 'l' => 8.0],
                ]
            ],
            'Très actif' => [ // 25% Protéines, 50% Glucides, 25% Lipides
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de maïs + pain complet + confiture', 'base' => 380, 'p' => 11.7, 'g' => 77.0, 'l' => 6.2],
                            ['nom' => '2 œufs', 'base' => 100, 'p' => 12.6, 'g' => 1.2, 'l' => 10.6],
                            ['nom' => 'Banane + Jus d\'ananas', 'base' => 250, 'p' => 1.75, 'g' => 41.0, 'l' => 0.2],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz blanc + sauce tomate-viande', 'base' => 390, 'p' => 18.2, 'g' => 77.0, 'l' => 6.35],
                            ['nom' => 'Alloco', 'base' => 120, 'p' => 1.2, 'g' => 36.0, 'l' => 9.6],
                            ['nom' => 'Grande salade', 'base' => 150, 'p' => 1.5, 'g' => 7.5, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Atassi (riz-haricots)', 'base' => 250, 'p' => 15.0, 'g' => 125.0, 'l' => 2.5],
                            ['nom' => 'Poulet grillé', 'base' => 130, 'p' => 35.1, 'g' => 0.0, 'l' => 3.9],
                            ['nom' => 'Plantain bouilli', 'base' => 120, 'p' => 1.2, 'g' => 36.0, 'l' => 0.24],
                            ['nom' => 'Salade composée', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akpan + beignet haricot (4 petits)', 'base' => 400, 'p' => 18.5, 'g' => 85.0, 'l' => 9.0],
                            ['nom' => 'Avocat + papaye', 'base' => 150, 'p' => 1.5, 'g' => 15.5, 'l' => 7.6],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte d\'igname + sauce arachide poisson', 'base' => 370, 'p' => 17.91, 'g' => 75.6, 'l' => 17.26],
                            ['nom' => 'Légumes sautés + mangue', 'base' => 200, 'p' => 1.5, 'g' => 20.0, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Spaghetti sauce tomate-viande', 'base' => 320, 'p' => 19.6, 'g' => 56.0, 'l' => 6.8],
                            ['nom' => 'Viande hachée', 'base' => 120, 'p' => 31.2, 'g' => 0.0, 'l' => 9.6],
                            ['nom' => 'Pain (petit) + salade', 'base' => 130, 'p' => 3.7, 'g' => 11.0, 'l' => 0.8],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil enrichie', 'base' => 300, 'p' => 4.5, 'g' => 45.0, 'l' => 3.0],
                            ['nom' => 'Omelette (2 œufs) + légumes', 'base' => 100, 'p' => 6.3, 'g' => 1.0, 'l' => 5.5],
                            ['nom' => 'Pain complet', 'base' => 70, 'p' => 6.3, 'g' => 28.0, 'l' => 2.8],
                            ['nom' => 'Orange', 'base' => 150, 'p' => 1.35, 'g' => 18.0, 'l' => 0.15],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz jollof', 'base' => 270, 'p' => 7.56, 'g' => 75.6, 'l' => 0.81],
                            ['nom' => 'Poulet braisé', 'base' => 150, 'p' => 40.5, 'g' => 0.0, 'l' => 4.5],
                            ['nom' => 'Alloco', 'base' => 120, 'p' => 1.2, 'g' => 36.0, 'l' => 9.6],
                            ['nom' => 'Salade abondante + ananas', 'base' => 250, 'p' => 2.0, 'g' => 20.5, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Igname pilée', 'base' => 200, 'p' => 3.0, 'g' => 56.0, 'l' => 0.4],
                            ['nom' => 'Sauce feuilles d’épinard', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Viande de bœuf', 'base' => 130, 'p' => 33.8, 'g' => 0.0, 'l' => 10.4],
                            ['nom' => 'Salade mixte', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akoumé + sauce poisson', 'base' => 400, 'p' => 14.5, 'g' => 65.0, 'l' => 4.5],
                            ['nom' => 'Avocat + plantain bouilli', 'base' => 200, 'p' => 3.0, 'g' => 39.0, 'l' => 15.2],
                            ['nom' => 'Jus de bissap', 'base' => 150, 'p' => 0.75, 'g' => 18.0, 'l' => 0.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte rouge + sauce graine crabe', 'base' => 400, 'p' => 18.75, 'g' => 82.0, 'l' => 18.75],
                            ['nom' => 'Poisson fumé', 'base' => 130, 'p' => 26.0, 'g' => 0.0, 'l' => 2.6],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Couscous de manioc', 'base' => 180, 'p' => 1.8, 'g' => 50.4, 'l' => 0.18],
                            ['nom' => 'Ragoût de poulet + légumes', 'base' => 140, 'p' => 21.0, 'g' => 7.0, 'l' => 11.2],
                            ['nom' => 'Gari', 'base' => 50, 'p' => 0.5, 'g' => 14.0, 'l' => 0.05],
                            ['nom' => 'Salade verte', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Smoothie banane-arachide-lait', 'p' => 3.0, 'g' => 25.0, 'l' => 10.0],
                    ['nom' => 'Pain + beurre d’arachide + fruits', 'p' => 34.5, 'g' => 60.0, 'l' => 52.2],
                    ['nom' => 'Barre énergétique maison', 'p' => 5.0, 'g' => 40.0, 'l' => 12.0],
                    ['nom' => 'Yaourt + granola', 'p' => 7.0, 'g' => 26.0, 'l' => 8.0],
                ]
            ],
            'Extrêmement actif' => [ // 25% Protéines, 55% Glucides, 20% Lipides
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de maïs + pain complet + confiture', 'base' => 450, 'p' => 14.25, 'g' => 92.5, 'l' => 7.5],
                            ['nom' => '2 œufs', 'base' => 100, 'p' => 12.6, 'g' => 1.2, 'l' => 10.6],
                            ['nom' => '2 bananes + Lait entier', 'base' => 400, 'p' => 8.4, 'g' => 55.6, 'l' => 7.4],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz blanc + sauce tomate-poisson', 'base' => 450, 'p' => 23.4, 'g' => 91.5, 'l' => 6.9],
                            ['nom' => 'Alloco + igname bouillie', 'base' => 250, 'p' => 3.0, 'g' => 73.0, 'l' => 12.2],
                            ['nom' => 'Grande salade', 'base' => 150, 'p' => 1.5, 'g' => 7.5, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Atassi copieux (300g) + pain', 'base' => 350, 'p' => 22.5, 'g' => 170.0, 'l' => 5.0],
                            ['nom' => 'Poulet grillé', 'base' => 140, 'p' => 37.8, 'g' => 0.0, 'l' => 4.2],
                            ['nom' => 'Alloco + salade', 'base' => 250, 'p' => 2.5, 'g' => 37.5, 'l' => 8.3],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa + sauce arachide', 'base' => 450, 'p' => 15.25, 'g' => 78.0, 'l' => 13.75],
                            ['nom' => 'Beignet haricot (5 petits)', 'base' => 125, 'p' => 17.5, 'g' => 50.0, 'l' => 7.5],
                            ['nom' => 'Avocat + papaye', 'base' => 250, 'p' => 2.75, 'g' => 25.5, 'l' => 15.15],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte d\'igname + sauce graine', 'base' => 430, 'p' => 19.76, 'g' => 90.4, 'l' => 18.56],
                            ['nom' => 'Poisson braisé + plantain bouilli', 'base' => 250, 'p' => 31.0, 'g' => 30.0, 'l' => 3.2],
                            ['nom' => 'Légumes + mangue', 'base' => 200, 'p' => 1.0, 'g' => 20.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Spaghetti + sauce viande', 'base' => 380, 'p' => 25.5, 'g' => 69.0, 'l' => 7.7],
                            ['nom' => 'Pain complet', 'base' => 60, 'p' => 5.4, 'g' => 24.0, 'l' => 2.4],
                            ['nom' => 'Grande salade', 'base' => 150, 'p' => 1.5, 'g' => 7.5, 'l' => 0.3],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil + pain complet + beurre arachide', 'base' => 470, 'p' => 19.25, 'g' => 96.5, 'l' => 17.5],
                            ['nom' => 'Omelette (3 œufs)', 'base' => 150, 'p' => 9.45, 'g' => 0.9, 'l' => 7.95],
                            ['nom' => 'Banane plantain bouillie + Jus orange', 'base' => 320, 'p' => 2.2, 'g' => 60.0, 'l' => 0.24],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz jollof', 'base' => 320, 'p' => 8.96, 'g' => 89.6, 'l' => 0.96],
                            ['nom' => 'Poulet braisé', 'base' => 160, 'p' => 43.2, 'g' => 0.0, 'l' => 4.8],
                            ['nom' => 'Alloco', 'base' => 150, 'p' => 1.5, 'g' => 45.0, 'l' => 12.0],
                            ['nom' => 'Salade + Pain', 'base' => 200, 'p' => 5.5, 'g' => 32.5, 'l' => 1.05],
                        ],
                        'Dîner' => [
                            ['nom' => 'Igname pilée + sauce feuilles', 'base' => 400, 'p' => 6.75, 'g' => 77.5, 'l' => 1.25],
                            ['nom' => 'Viande de bœuf', 'base' => 140, 'p' => 36.4, 'g' => 0.0, 'l' => 11.2],
                            ['nom' => 'Gari + salade', 'base' => 200, 'p' => 2.0, 'g' => 21.5, 'l' => 0.35],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akoumé + sauce poisson', 'base' => 500, 'p' => 20.25, 'g' => 77.5, 'l' => 6.25],
                            ['nom' => 'Avocat + igname bouillie + papaye', 'base' => 350, 'p' => 4.25, 'g' => 53.5, 'l' => 15.35],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte rouge + sauce gombo-crabe', 'base' => 450, 'p' => 19.5, 'g' => 96.0, 'l' => 18.9],
                            ['nom' => 'Poisson fumé + plantain + légumes', 'base' => 400, 'p' => 32.5, 'g' => 37.5, 'l' => 3.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Couscous manioc + ragoût poulet', 'base' => 370, 'p' => 24.7, 'g' => 66.6, 'l' => 12.22],
                            ['nom' => 'Riz blanc + légumes sautés', 'base' => 250, 'p' => 4.3, 'g' => 35.5, 'l' => 0.6],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Barre énergétique + banane + lait', 'p' => 7.0, 'g' => 80.0, 'l' => 14.0],
                    ['nom' => 'Yaourt + granola + fruits + miel', 'p' => 14.5, 'g' => 121.0, 'l' => 8.1],
                    ['nom' => 'Sandwich beurre d’arachide + confiture', 'p' => 10.0, 'g' => 50.0, 'l' => 20.0],
                    ['nom' => 'Smoothie bowl', 'p' => 3.0, 'g' => 25.0, 'l' => 10.0],
                ]
            ]
        ];
    }
}
