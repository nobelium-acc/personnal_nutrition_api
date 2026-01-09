<?php

namespace App\Services;

class NutritionDataService
{
    public function getMenus()
    {
        return [
            'Sédentaire' => [
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Neko (porridge de maïs fermenté)', 'base' => 200, 'p' => 4.0, 'g' => 40.0, 'l' => 1.2],
                            ['nom' => 'Légumes (okra/crin-crin)', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 0.4],
                            ['nom' => 'Œuf dur', 'base' => 50, 'p' => 6.3, 'g' => 0.6, 'l' => 5.3],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Akassa', 'base' => 150, 'p' => 3.0, 'g' => 21.0, 'l' => 0.8],
                            ['nom' => 'Sauce tomate aux légumes', 'base' => 100, 'p' => 2.0, 'g' => 8.0, 'l' => 1.0],
                            ['nom' => 'Poisson tilapia grillé', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 3.0],
                            ['nom' => 'Légumes vapeur', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Riz complet', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Sauce d’amarante', 'base' => 100, 'p' => 3.0, 'g' => 6.0, 'l' => 4.0],
                            ['nom' => 'Poulet grillé sans peau', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 4.0],
                            ['nom' => 'Crudités', 'base' => 100, 'p' => 1.0, 'g' => 3.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Haricots rouges cuits', 'base' => 150, 'p' => 12.0, 'g' => 27.0, 'l' => 1.2],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 27.9, 'l' => 0.2],
                            ['nom' => 'Huile de palme', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 10.0],
                            ['nom' => 'Épinards + gboma', 'base' => 100, 'p' => 2.5, 'g' => 4.0, 'l' => 0.5],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Poisson tilapia grillé', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 3.0],
                            ['nom' => 'Akassa nature', 'base' => 150, 'p' => 3.0, 'g' => 21.0, 'l' => 0.8],
                            ['nom' => 'Sauce arachide allégée', 'base' => 30, 'p' => 4.0, 'g' => 3.0, 'l' => 6.0],
                            ['nom' => 'Salade de tomates et concombres', 'base' => 100, 'p' => 1.2, 'g' => 4.0, 'l' => 0.3],
                        ],
                        'Dîner' => [
                            ['nom' => 'Œufs brouillés', 'base' => 120, 'p' => 12.6, 'g' => 1.2, 'l' => 10.6],
                            ['nom' => 'Patate douce bouillie', 'base' => 70, 'p' => 1.1, 'g' => 14.0, 'l' => 0.1],
                            ['nom' => 'Avocat', 'base' => 50, 'p' => 1.0, 'g' => 4.3, 'l' => 7.4],
                            ['nom' => 'Légumes feuilles (oseille, amarante)', 'base' => 100, 'p' => 2.5, 'g' => 4.0, 'l' => 0.5],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie d’avoine aux arachides', 'base' => 100, 'p' => 2.4, 'g' => 12.3, 'l' => 1.4],
                            ['nom' => 'Arachides grillées non salées', 'base' => 30, 'p' => 7.7, 'g' => 4.8, 'l' => 14.8],
                            ['nom' => 'Œuf dur', 'base' => 50, 'p' => 6.3, 'g' => 0.6, 'l' => 5.3],
                            ['nom' => 'Mangue verte en tranches', 'base' => 100, 'p' => 0.8, 'g' => 15.0, 'l' => 0.4],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce graine aux épinards', 'base' => 100, 'p' => 6.0, 'g' => 10.0, 'l' => 10.0],
                            ['nom' => 'Poisson fumé', 'base' => 100, 'p' => 30.0, 'g' => 0.0, 'l' => 15.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 27.9, 'l' => 0.2],
                            ['nom' => 'Avocat', 'base' => 100, 'p' => 2.0, 'g' => 8.5, 'l' => 14.7],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce tomate légère', 'base' => 100, 'p' => 1.5, 'g' => 8.0, 'l' => 0.5],
                            ['nom' => 'Viande de bœuf maigre', 'base' => 100, 'p' => 26.0, 'g' => 0.0, 'l' => 15.0],
                            ['nom' => 'Riz complet', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Légumes verts (gombo + carotte)', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 0.5],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa (bouillie de maïs)', 'base' => 100, 'p' => 2.0, 'g' => 14.0, 'l' => 0.5],
                            ['nom' => 'Lait écrémé', 'base' => 100, 'p' => 3.4, 'g' => 5.0, 'l' => 0.1],
                            ['nom' => 'Arachides grillées non salées', 'base' => 30, 'p' => 7.7, 'g' => 4.8, 'l' => 14.8],
                            ['nom' => 'Œuf dur', 'base' => 50, 'p' => 6.3, 'g' => 0.6, 'l' => 5.3],
                            ['nom' => 'Thé vert (sans sucre)', 'base' => 200, 'p' => 0.1, 'g' => 0.5, 'l' => 0.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Poisson grillé (maquereau ou sardine)', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 13.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 27.9, 'l' => 0.2],
                            ['nom' => 'Sauce tomate aux légumes verts', 'base' => 100, 'p' => 2.0, 'g' => 10.0, 'l' => 1.0],
                            ['nom' => 'Salade de concombre', 'base' => 100, 'p' => 0.8, 'g' => 3.6, 'l' => 0.1],
                        ],
                        'Dîner' => [
                            ['nom' => 'Haricots rouges mijotés', 'base' => 150, 'p' => 12.0, 'g' => 27.0, 'l' => 1.2],
                            ['nom' => 'Riz complet', 'base' => 80, 'p' => 2.8, 'g' => 25.0, 'l' => 0.8],
                            ['nom' => 'Légumes sautés (gombo + carotte)', 'base' => 150, 'p' => 3.0, 'g' => 10.0, 'l' => 1.0],
                            ['nom' => 'Avocat', 'base' => 50, 'p' => 1.0, 'g' => 4.25, 'l' => 7.35],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Arachides grillées non salées + orange', 'p' => 7.7, 'g' => 15.0, 'l' => 14.8],
                    ['nom' => 'Boulettes de manioc cuit + melon', 'p' => 4.0, 'g' => 14.0, 'l' => 10.0],
                    ['nom' => 'Bouillie de mil sans sucre + lait de coco', 'p' => 6.0, 'g' => 28.0, 'l' => 12.0],
                    ['nom' => 'Yaourt nature + noix de coco râpée', 'p' => 5.0, 'g' => 7.0, 'l' => 3.5],
                    ['nom' => 'Goyave + noix de cajou', 'p' => 3.5, 'g' => 12.0, 'l' => 7.0]
                ]
            ],
            'Légèrement actif' => [
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Millet porridge', 'base' => 100, 'p' => 3.0, 'g' => 23.0, 'l' => 1.0],
                            ['nom' => 'Papaye', 'base' => 100, 'p' => 0.5, 'g' => 11.0, 'l' => 0.1],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Wassa-wassa (igname couscous)', 'base' => 100, 'p' => 1.5, 'g' => 27.0, 'l' => 0.2],
                            ['nom' => 'Sauce de gombo', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Poisson maigre (tilapia)', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 3.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Riz complet cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Sauce de haricots', 'base' => 100, 'p' => 8.0, 'g' => 20.0, 'l' => 1.5],
                            ['nom' => 'Légumes feuilles', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Ablo (pâte de maïs)', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce poisson fumé + légumes', 'base' => 100, 'p' => 3.0, 'g' => 5.0, 'l' => 2.0],
                            ['nom' => 'Huile de palme', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 9.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz complet local', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Ragout de bœuf maigre', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 5.0],
                            ['nom' => 'Légumes sauce (gombo + tomates)', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 1.0],
                            ['nom' => 'Huile d’arachide', 'base' => 5, 'p' => 0.0, 'g' => 0.0, 'l' => 4.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Fonio cuit', 'base' => 100, 'p' => 3.0, 'g' => 25.0, 'l' => 0.5],
                            ['nom' => 'Poulet grillé', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 4.0],
                            ['nom' => 'Sauce légumes verts', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa (pâte de maïs fermenté)', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Lait caillé', 'base' => 100, 'p' => 3.5, 'g' => 5.0, 'l' => 3.0],
                            ['nom' => 'Banane plantain grillée', 'base' => 80, 'p' => 1.0, 'g' => 20.0, 'l' => 0.3],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce okra', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Crevettes cuites', 'base' => 100, 'p' => 24.0, 'g' => 0.0, 'l' => 1.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 27.9, 'l' => 0.2],
                            ['nom' => 'Huile de palme', 'base' => 5, 'p' => 0.0, 'g' => 0.0, 'l' => 4.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce d’arachide allégée', 'base' => 100, 'p' => 8.0, 'g' => 12.0, 'l' => 10.0],
                            ['nom' => 'Poulet sans peau', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 4.0],
                            ['nom' => 'Fonio cuit', 'base' => 100, 'p' => 3.0, 'g' => 25.0, 'l' => 0.5],
                            ['nom' => 'Carotte + haricots verts', 'base' => 100, 'p' => 1.5, 'g' => 8.0, 'l' => 0.3],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Ablo (pâte de maïs)', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce poisson fumé + légumes', 'base' => 100, 'p' => 3.0, 'g' => 5.0, 'l' => 2.0],
                            ['nom' => 'Huile de palme', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 9.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz complet local', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Ragout de bœuf maigre', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 5.0],
                            ['nom' => 'Légumes sauce (gombo + tomates)', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 1.0],
                            ['nom' => 'Huile d’arachide', 'base' => 5, 'p' => 0.0, 'g' => 0.0, 'l' => 4.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Fonio cuit', 'base' => 100, 'p' => 3.0, 'g' => 25.0, 'l' => 0.5],
                            ['nom' => 'Poulet grillé', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 4.0],
                            ['nom' => 'Sauce légumes verts', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Akara grillé', 'p' => 5.0, 'g' => 8.0, 'l' => 2.0],
                    ['nom' => 'Yaourt nature', 'p' => 5.0, 'g' => 7.0, 'l' => 3.0],
                    ['nom' => 'Ananas', 'p' => 0.5, 'g' => 13.0, 'l' => 0.1],
                    ['nom' => 'Galette de mil + lait caillé', 'p' => 4.0, 'g' => 28.0, 'l' => 2.0],
                    ['nom' => 'Beignet de haricot petit + thé', 'p' => 3.0, 'g' => 15.0, 'l' => 2.0]
                ]
            ],
            'Modérément actif' => [
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Gari (farine de cassava)', 'base' => 100, 'p' => 1.0, 'g' => 80.0, 'l' => 0.5],
                            ['nom' => 'Légumes feuilles cuits', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Fruit (papaye/ananas)', 'base' => 100, 'p' => 0.5, 'g' => 11.0, 'l' => 0.1],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz brun cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Sauce mixte (légumes + légumineuses)', 'base' => 100, 'p' => 6.0, 'g' => 15.0, 'l' => 3.0],
                            ['nom' => 'Viande blanche maigre', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 4.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Pâte de maïs / yam dough (telibô)', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce aux légumes', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 1.0],
                            ['nom' => 'Œuf ou poisson', 'base' => 75, 'p' => 14.0, 'g' => 0.0, 'l' => 3.5],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 28.0, 'l' => 0.2],
                            ['nom' => 'Sauce claire au poisson', 'base' => 100, 'p' => 3.0, 'g' => 5.0, 'l' => 2.0],
                            ['nom' => 'Légumes feuilles', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Huile de palme', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 9.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz local cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Haricots blancs cuits', 'base' => 100, 'p' => 7.0, 'g' => 22.0, 'l' => 0.5],
                            ['nom' => 'Légumes sauce tomate', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Huile d’arachide', 'base' => 5, 'p' => 0.0, 'g' => 0.0, 'l' => 4.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Akassa', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce viande maigre', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 10.0],
                            ['nom' => 'Légumes verts variés', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Pâte de maïs', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Haricots rouges cuits', 'base' => 100, 'p' => 8.0, 'g' => 22.0, 'l' => 0.5],
                            ['nom' => 'Huile de palme', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 9.0],
                            ['nom' => 'Papaye', 'base' => 100, 'p' => 0.5, 'g' => 11.0, 'l' => 0.1],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce gombo', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Poisson frais', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Riz local cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Patate douce bouillie', 'base' => 100, 'p' => 1.5, 'g' => 20.0, 'l' => 0.1],
                        ],
                        'Dîner' => [
                            ['nom' => 'Sauce feuilles', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Viande de chèvre', 'base' => 100, 'p' => 20.0, 'g' => 0.0, 'l' => 6.0],
                            ['nom' => 'Tubercules mélangés', 'base' => 100, 'p' => 2.0, 'g' => 25.0, 'l' => 0.5],
                            ['nom' => 'Légumes', 'base' => 100, 'p' => 1.5, 'g' => 5.0, 'l' => 0.3],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa aux arachides', 'base' => 100, 'p' => 5.0, 'g' => 22.0, 'l' => 6.0],
                            ['nom' => 'Pain complet local', 'base' => 50, 'p' => 4.0, 'g' => 25.0, 'l' => 1.0],
                            ['nom' => 'Œuf brouillé aux légumes', 'base' => 100, 'p' => 6.0, 'g' => 3.0, 'l' => 5.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Viande de bœuf grillée', 'base' => 120, 'p' => 26.0, 'g' => 0.0, 'l' => 6.0],
                            ['nom' => 'Igname pilée', 'base' => 100, 'p' => 1.5, 'g' => 28.0, 'l' => 0.2],
                            ['nom' => 'Sauce palme légère', 'base' => 50, 'p' => 1.0, 'g' => 2.0, 'l' => 4.0],
                            ['nom' => 'Salade crudités', 'base' => 100, 'p' => 1.0, 'g' => 3.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Crevettes sautées', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Riz rouge du Bénin cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 1.5, 'g' => 7.0, 'l' => 0.5],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Klui-klui (boulettes de cacahuète)', 'p' => 5.0, 'g' => 8.0, 'l' => 5.0],
                    ['nom' => 'Banane plantain bouillie', 'p' => 1.0, 'g' => 27.0, 'l' => 0.3],
                    ['nom' => 'Graines de tournesol locales', 'p' => 6.0, 'g' => 5.0, 'l' => 10.0],
                    ['nom' => 'Jus de bissap sans sucre + dattes', 'p' => 0.5, 'g' => 10.0, 'l' => 0.2]
                ]
            ],
            'Très actif' => [
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Gari porridge', 'base' => 100, 'p' => 1.0, 'g' => 80.0, 'l' => 0.5],
                            ['nom' => 'Banane', 'base' => 100, 'p' => 1.0, 'g' => 23.0, 'l' => 0.2],
                            ['nom' => 'Lait léger', 'base' => 100, 'p' => 3.5, 'g' => 5.0, 'l' => 1.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Akassa', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce légumes', 'base' => 100, 'p' => 2.0, 'g' => 7.0, 'l' => 1.0],
                            ['nom' => 'Poisson (tilapia)', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Salade crudités', 'base' => 100, 'p' => 1.0, 'g' => 3.0, 'l' => 0.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Riz complet cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Légumineuses (haricots rouges)', 'base' => 100, 'p' => 8.0, 'g' => 22.0, 'l' => 0.5],
                            ['nom' => 'Légumes cuits', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Yaourt nature', 'base' => 100, 'p' => 3.5, 'g' => 5.0, 'l' => 3.0],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa enrichi', 'base' => 100, 'p' => 4.0, 'g' => 22.0, 'l' => 5.0],
                            ['nom' => 'Œufs', 'base' => 100, 'p' => 12.0, 'g' => 1.0, 'l' => 10.0],
                            ['nom' => 'Banane plantain', 'base' => 100, 'p' => 1.0, 'g' => 31.0, 'l' => 0.3],
                            ['nom' => 'Avocat', 'base' => 50, 'p' => 1.0, 'g' => 3.0, 'l' => 7.5],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Poisson grillé', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Riz complet cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 28.0, 'l' => 0.2],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Pâte de maïs', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce poisson', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Légumes sautés', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil', 'base' => 100, 'p' => 3.0, 'g' => 22.0, 'l' => 1.0],
                            ['nom' => 'Lait', 'base' => 100, 'p' => 3.5, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Banane', 'base' => 100, 'p' => 1.0, 'g' => 23.0, 'l' => 0.2],
                            ['nom' => 'Arachides grillées', 'base' => 30, 'p' => 7.5, 'g' => 5.0, 'l' => 14.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Sauce palava', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Poisson frais', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Riz cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Igname bouillie', 'base' => 100, 'p' => 1.5, 'g' => 28.0, 'l' => 0.2],
                            ['nom' => 'Huile modérée', 'base' => 10, 'p' => 0.0, 'g' => 0.0, 'l' => 9.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Ragoût de légumes', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Viande (poulet ou bœuf)', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 5.0],
                            ['nom' => 'Fonio cuit', 'base' => 100, 'p' => 3.5, 'g' => 27.0, 'l' => 0.5],
                            ['nom' => 'Tubercules variés', 'base' => 100, 'p' => 1.5, 'g' => 20.0, 'l' => 0.2],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Riz local cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Poulet grillé', 'base' => 100, 'p' => 23.0, 'g' => 0.0, 'l' => 3.0],
                            ['nom' => 'Sauce tomate', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Légumes variés', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.5],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Akassa', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce tomate', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Légumes sautés', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Haricots rouges cuits', 'base' => 100, 'p' => 8.0, 'g' => 22.0, 'l' => 0.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Porridge de maïs', 'base' => 100, 'p' => 3.0, 'g' => 21.0, 'l' => 1.0],
                            ['nom' => 'Œuf dur', 'base' => 50, 'p' => 6.0, 'g' => 0.5, 'l' => 5.0],
                            ['nom' => 'Légumes verts', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 0.2],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Mélange fruits secs locaux', 'p' => 2.0, 'g' => 15.0, 'l' => 5.0],
                    ['nom' => 'Galette de mil', 'p' => 2.0, 'g' => 25.0, 'l' => 0.5],
                    ['nom' => 'Eau de coco + noix', 'p' => 0.5, 'g' => 6.0, 'l' => 3.0],
                    ['nom' => 'Beignets banane plantain au four', 'p' => 1.0, 'g' => 15.0, 'l' => 3.0]
                ]
            ],
            'Extrêmement actif' => [
                'jour1' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Wassa-wassa de maïs', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Œufs brouillés', 'base' => 100, 'p' => 12.0, 'g' => 1.0, 'l' => 10.0],
                            ['nom' => 'Avocat', 'base' => 50, 'p' => 1.0, 'g' => 3.0, 'l' => 7.5],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz local cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                            ['nom' => 'Sauce tomate aux légumes', 'base' => 100, 'p' => 1.0, 'g' => 5.0, 'l' => 0.5],
                            ['nom' => 'Poisson grillé', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                            ['nom' => 'Haricots blancs cuits (niébé)', 'base' => 100, 'p' => 8.0, 'g' => 22.0, 'l' => 0.5],
                        ],
                        'Dîner' => [
                            ['nom' => 'Wassa-wassa d’igname', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce légumes verts (gboma)', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Poisson fumé', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de maïs fermenté (koko)', 'base' => 100, 'p' => 3.0, 'g' => 22.0, 'l' => 1.0],
                            ['nom' => 'Lait', 'base' => 100, 'p' => 3.5, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Omelette aux légumes', 'base' => 100, 'p' => 12.0, 'g' => 1.5, 'l' => 10.0],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte de maïs (pâte rouge)', 'base' => 100, 'p' => 2.0, 'g' => 21.0, 'l' => 0.5],
                            ['nom' => 'Sauce arachide aux légumes', 'base' => 100, 'p' => 8.0, 'g' => 8.0, 'l' => 12.0],
                            ['nom' => 'Viande bœuf ou poisson', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 5.0],
                            ['nom' => 'Riz local cuit', 'base' => 100, 'p' => 3.5, 'g' => 31.0, 'l' => 1.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Igname pilée (foutou)', 'base' => 100, 'p' => 1.5, 'g' => 28.0, 'l' => 0.2],
                            ['nom' => 'Sauce légumes (feuilles vertes)', 'base' => 100, 'p' => 2.0, 'g' => 5.0, 'l' => 1.0],
                            ['nom' => 'Poisson grillé', 'base' => 100, 'p' => 22.0, 'g' => 0.0, 'l' => 2.0],
                        ]
                    ]
                ],
                'jour2' => [
                    'Option A' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Akassa enrichi', 'base' => 200, 'p' => 4.0, 'g' => 44.0, 'l' => 0.8],
                            ['nom' => 'Omelette légumes (2 œufs)', 'base' => 120, 'p' => 15.6, 'g' => 1.2, 'l' => 10.6],
                            ['nom' => 'Légumes (tomate, oignon)', 'base' => 50, 'p' => 1.5, 'g' => 6.0, 'l' => 0.3],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Pâte de maïs', 'base' => 200, 'p' => 4.4, 'g' => 46.0, 'l' => 1.0],
                            ['nom' => 'Sauce feuilles patate', 'base' => 100, 'p' => 3.5, 'g' => 6.0, 'l' => 0.4],
                            ['nom' => 'Viande blanche', 'base' => 120, 'p' => 27.0, 'g' => 0.0, 'l' => 3.2],
                        ],
                        'Dîner' => [
                            ['nom' => 'Légumes sautés', 'base' => 100, 'p' => 3.0, 'g' => 10.0, 'l' => 3.5],
                            ['nom' => 'Plantain bouilli', 'base' => 150, 'p' => 1.8, 'g' => 46.0, 'l' => 0.4],
                            ['nom' => 'Poisson', 'base' => 120, 'p' => 24.0, 'g' => 0.0, 'l' => 2.0],
                        ]
                    ],
                    'Option B' => [
                        'Petit-déjeuner' => [
                            ['nom' => 'Bouillie de mil', 'base' => 200, 'p' => 6.2, 'g' => 42.0, 'l' => 2.2],
                            ['nom' => 'Arachides grillées', 'base' => 30, 'p' => 7.8, 'g' => 4.5, 'l' => 15.6],
                        ],
                        'Déjeuner' => [
                            ['nom' => 'Riz local', 'base' => 200, 'p' => 5.0, 'g' => 56.0, 'l' => 0.8],
                            ['nom' => 'Sauce tomate légumes', 'base' => 100, 'p' => 2.5, 'g' => 8.0, 'l' => 1.0],
                            ['nom' => 'Poulet grillé', 'base' => 120, 'p' => 27.6, 'g' => 0.0, 'l' => 4.0],
                        ],
                        'Dîner' => [
                            ['nom' => 'Igname bouillie', 'base' => 200, 'p' => 3.2, 'g' => 52.0, 'l' => 0.4],
                            ['nom' => 'Légumes verts', 'base' => 100, 'p' => 3.0, 'g' => 6.0, 'l' => 0.5],
                            ['nom' => 'Poisson grillé', 'base' => 120, 'p' => 24.0, 'g' => 0.0, 'l' => 2.2],
                        ]
                    ]
                ],
                'collations' => [
                    ['nom' => 'Arachides grillées + orange', 'p' => 8.3, 'g' => 17.0, 'l' => 14.2],
                    ['nom' => 'Bouillie légère de sorgho + lait', 'p' => 6.5, 'g' => 27.0, 'l' => 2.0],
                    ['nom' => 'Igname bouillie + avocat', 'p' => 2.5, 'g' => 31.0, 'l' => 7.7],
                    ['nom' => 'Arachides grillées + boisson moringa', 'p' => 7.8, 'g' => 4.5, 'l' => 15.6]
                ]
            ]
        ];
    }
}
