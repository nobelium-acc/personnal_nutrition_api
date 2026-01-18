<?php

namespace App\Services;

class MenuGenerator
{
    protected array $userMacros;
    protected array $baseMenu;
    protected string $activity;
    protected int $days;

    protected array $pathologies;

    public function __construct(array $userMacros, array $baseMenu, string $activity, int $days = 90, array $pathologies = [])
    {
        $this->userMacros = $userMacros; 
        $this->activity = $activity;
        // Correctly filter base menu by activity level
        $this->baseMenu = $baseMenu[$activity] ?? ($baseMenu['Sédentaire'] ?? []);
        $this->days = $days;
        $this->pathologies = $pathologies;
    }

    /**
     * Génère les menus pour la durée spécifiée
     */
    public function generate(): array
    {
        $menu90Jours = [];
        $globalFactors = ['g' => 0, 'p' => 0, 'l' => 0];

        for ($i = 1; $i <= $this->days; $i++) {
            $dayMenu = $this->generateDailyMenu();
            
            if (!$dayMenu) {
                // Relâchement des contraintes si nécessaire
                $dayMenu = $this->generateDailyMenu(true);
            }

            // Sécurité pour éviter l'erreur array_merge si aucun menu n'est trouvé malgré tout
            if (!$dayMenu) {
                $dayMenu = [
                    'type' => 'Standard (Non ajusté)',
                    'repas' => ['Note' => 'Ajustement impossible pour ce jour avec les contraintes actuelles'],
                    'collations' => [],
                    'factors' => ['glucides' => 1.0, 'proteines' => 1.0, 'lipides' => 1.0]
                ];
            }

            $menu90Jours[] = array_merge(['jour' => $i], $dayMenu);

            $globalFactors['g'] += $dayMenu['factors']['glucides'] ?? 1.0;
            $globalFactors['p'] += $dayMenu['factors']['proteines'] ?? 1.0;
            $globalFactors['l'] += $dayMenu['factors']['lipides'] ?? 1.0;
        }

        return [
            'menu' => $menu90Jours,
            'average_factors' => [
                'glucides' => round($globalFactors['g'] / $this->days, 3),
                'proteines' => round($globalFactors['p'] / $this->days, 3),
                'lipides' => round($globalFactors['l'] / $this->days, 3),
            ]
        ];
    }

    protected function generateDailyMenu(bool $relax = false): ?array
    {
        $maxRetries = 50;
        $bestRelaxed = null;

        $isCardio = $this->pathologies['cardio'] ?? false;
        $isHypertension = $this->pathologies['hypertension'] ?? false;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $dayKey = (rand(0, 1) ? 'jour1' : 'jour2');
            $base = $this->baseMenu;

            $dailyMeals = [
                'Petit-déjeuner' => $base[$dayKey]['Option ' . (rand(0, 1) ? 'A' : 'B')]['Petit-déjeuner'],
                'Déjeuner' => $base[$dayKey]['Option ' . (rand(0, 1) ? 'A' : 'B')]['Déjeuner'],
                'Dîner' => $base[$dayKey]['Option ' . (rand(0, 1) ? 'A' : 'B')]['Dîner']
            ];

            $snackCount = $this->getSnackCount();
            $snacks = [];
            if ($snackCount > 0 && isset($base['collations'])) {
                $availableSnacks = $base['collations'];
                $selectedIndices = (array) array_rand($availableSnacks, min($snackCount, count($availableSnacks)));
                foreach ($selectedIndices as $idx) {
                    $snacks[] = $availableSnacks[$idx];
                }
            }

            if ($isHypertension && !$isCardio) {
                // Stratégie Hypertension
                $result = $this->scaleAndValidate($dailyMeals, $snacks, false);
                if ($result) return $result;

                $res1 = $this->scaleAndValidate($dailyMeals, $snacks, true, 1);
                if ($res1) return $res1;

                $res2 = $this->scaleAndValidate($dailyMeals, $snacks, true, 2);
                if ($res2) return $res2;
            } else {
                // Stratégie Cardio / Défaut
                if ($relax) {
                    $result = $this->scaleAndValidate($dailyMeals, $snacks, true, 3);
                    if ($result) return $result;
                } else {
                    $result = $this->scaleAndValidate($dailyMeals, $snacks, false);
                    if ($result) return $result;

                    $r1 = $this->scaleAndValidate($dailyMeals, $snacks, true, 1);
                    if ($r1) return $r1;

                    $r2 = $this->scaleAndValidate($dailyMeals, $snacks, true, 2);
                    if ($r2) return $r2;

                    $r3 = $this->scaleAndValidate($dailyMeals, $snacks, true, 3);
                    if ($r3) return $r3;
                }
            }

            // Capture du meilleur résultat relaxé si aucun succès strict/partiel
            if (!$bestRelaxed) {
                $bestRelaxed = $this->scaleAndValidate($dailyMeals, $snacks, true, ($isHypertension && !$isCardio) ? 2 : 3);
            }
        }

        return $bestRelaxed;
    }

    protected function getSnackCount(): int
    {
        switch ($this->activity) {
            case 'Sédentaire': return 1; // Toujours au moins 1 collation pour la flexibilité
            case 'Légèrement actif': return 1;
            case 'Modérément actif': return rand(1, 2);
            case 'Très actif': return 2;
            case 'Extrêmement actif': return 3; // Plus de collations pour les très actifs
            default: return 1;
        }
    }

    protected function scaleAndValidate(array $meals, array $snacks, bool $relax, int $relaxLevel = 0): ?array
    {
        // 1. Calculer les macros des snacks (fixes)
        $snackTotals = ['g' => 0, 'p' => 0, 'l' => 0];
        foreach ($snacks as $s) {
            $snackTotals['g'] += $s['g'];
            $snackTotals['p'] += $s['p'];
            $snackTotals['l'] += $s['l'];
        }

        // 2. Déterminer les cibles pour les repas principaux
        $targetG = max(0, $this->userMacros['glucides'] - $snackTotals['g']);
        $targetP = max(0, $this->userMacros['proteines'] - $snackTotals['p']);
        $targetL = max(0, $this->userMacros['lipides'] - $snackTotals['l']);

        // 3. Calculer les totaux de base des repas choisis
        $baseTotals = ['g' => 0, 'p' => 0, 'l' => 0];
        foreach ($meals as $items) {
            foreach ($items as $it) {
                $baseTotals['g'] += $it['g'];
                $baseTotals['p'] += $it['p'];
                $baseTotals['l'] += $it['l'];
            }
        }

        // 4. Calculer les facteurs initiaux
        $fG = $targetG / max(1, $baseTotals['g']);
        $fP = $targetP / max(1, $baseTotals['p']);
        $fL = $targetL / max(1, $baseTotals['l']);

        // 5. Validation par priorité
        if (!$this->isValid($fG, $fP, $fL, $relax, $relaxLevel)) {
            return null;
        }

        // 6. Application des facteurs ajustés
        // On s'assure que les facteurs ne sont pas trop extrêmes pour la palatabilité
        $fG = max(0.4, min(1.8, $fG));
        $fP = max(0.4, min(1.8, $fP));
        $fL = max(0.4, min(1.8, $fL));

        $adjustedMeals = [];
        foreach ($meals as $moment => $items) {
            $momentAdjusted = [];
            foreach ($items as $item) {
                // Déterminer le macro dominant pour choisir le facteur
                $f = $fL; // Par défaut
                if ($item['g'] > 15 && $item['g'] > $item['p'] * 1.5) $f = $fG;
                elseif ($item['p'] > 5 && $item['p'] > $item['g']) $f = $fP;

                $momentAdjusted[] = [
                    'nom' => $item['nom'],
                    'portion_recommandee' => round($item['base'] * $f) . ' g',
                    'details' => "Macros: ".round($item['p']*$f,1)."g P, ".round($item['g']*$f,1)."g G, ".round($item['l']*$f,1)."g L"
                ];
            }
            $adjustedMeals[$moment] = $momentAdjusted;
        }

        $adjustedSnacks = [];
        foreach ($snacks as $s) {
            $adjustedSnacks[] = [
                'nom' => $s['nom'],
                'portion' => 'Portion standard',
                'details' => "Macros: {$s['p']}g P, {$s['g']}g G, {$s['l']}g L"
            ];
        }

        return [
            'type' => 'Ajusté',
            'repas' => $adjustedMeals,
            'collations' => $adjustedSnacks,
            'factors' => ['glucides' => $fG, 'proteines' => $fP, 'lipides' => $fL]
        ];
    }

    protected function isValid(float $fG, float $fP, float $fL, bool $relax, int $relaxLevel = 0): bool
    {
        $isCardio = $this->pathologies['cardio'] ?? false;
        $isHypertension = $this->pathologies['hypertension'] ?? false;

        // Normalisation : on évalue l'équilibre relatif, pas l'échelle absolue
        // Cela permet au système de fonctionner quel que soit le TDEE de l'utilisateur
        $avgFactor = ($fG + $fP + $fL) / 3;
        if ($avgFactor < 0.01) return false;

        $nG = $fG / $avgFactor;
        $nP = $fP / $avgFactor;
        $nL = $fL / $avgFactor;

        // === STRATÉGIE HYPERTENSION (Spécifiée par le message utilisateur) ===
        if ($isHypertension && !$isCardio) {
            // Glucides : Priorité absolue, on ne relâche pas ici
            $gMin = 0.80; $gMax = 1.13; $gRejet = 1.18;
            
            // Lipides et Protéines : Variables d'ajustement
            $lMin = 0.70; $lMax = 1.15; $lRejet = 1.25;
            $pMin = 0.82; $pMax = 1.30;

            if ($relaxLevel === 1) { // 1. Relâcher Lipides
                $lMin = 0.65;
            } elseif ($relaxLevel === 2) { // 2. Relâcher Protéines
                $lMin = 0.65;
                $pMin = 0.75;
            }

            // Filtrage Glucides (Priorité)
            if ($nG > $gRejet) return false;
            if ($nG < $gMin || $nG > $gMax) return false;

            // Filtrage Lipides
            if ($nL < $lMin || $nL > $lRejet) return false;

            // Filtrage Protéines
            if ($nP < $pMin || $nP > $pMax) return false;

            return true;
        }

        // === STRATÉGIE PAR DÉFAUT / CARDIO (L2.txt) ===
        // Lipides (Priorité absolue)
        $lMin = 0.85; $lMax = 1.08; $lRejet = 1.12;
        // Glucides
        $gMin = 0.80; $gMax = 1.12; $gRejet = 1.18;
        // Protéines
        $pMin = 0.75; $pMax = 1.25;

        // Relaxation progressive par défaut
        if ($relax) {
            if ($relaxLevel >= 1 || $relaxLevel === 0) $pMin = 0.68; // Niveau 1 : Protéines
            if ($relaxLevel >= 2) $gMin = 0.72; // Niveau 2 : Glucides
            if ($relaxLevel >= 3) $lMin = 0.82; // Niveau 3 : Lipides
        }

        // Filtrage Lipides (Hard filter)
        if ($nL < $lMin || $nL > $lMax) {
            if ($nL > $lRejet) return false;
            // Si pas relaxé, on rejette le hors-fenêtre minime
            if (!$relax) return false;
        }

        // Filtrage Glucides
        if ($nG > $gRejet) return false;
        if (!$relax && ($nG < $gMin || $nG > $gMax)) return false;

        // Filtrage Protéines
        if (!$relax && ($nP < $pMin || $nP > $pMax)) return false;

        return true;
    }
}
