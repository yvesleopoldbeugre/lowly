<?php

namespace App\Domains\Availability\Controllers\Web;

use App\Domains\Availability\Actions\ListAvailabilityBlocks;
use App\Domains\Catalogue\Actions\ListPartnerResidences;
use App\Domains\Catalogue\Actions\ListPartnerVehicles;
use App\Domains\Catalogue\Models\Residence;
use App\Domains\Catalogue\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Domaine Availability — calendrier de disponibilité du partenaire connecté,
 * voir UX_UI.md §6.3 et docs/ux/mockups/06-disponibilites.html.
 */
class PartnerAvailabilityController extends Controller
{
    public function index(
        Request $request,
        ListPartnerResidences $listResidences,
        ListPartnerVehicles $listVehicles,
        ListAvailabilityBlocks $listBlocks,
    ): View {
        $partner = $request->user()->partner;

        $residences = $listResidences->executer($partner, ['per_page' => 100]);
        $vehicles = $listVehicles->executer($partner, ['per_page' => 100]);

        $blockable = $this->resolveBlockable(
            $request->string('blockable_type', 'residence')->value(),
            $request->string('blockable_id')->value() ?: null,
            $residences->getCollection(),
            $vehicles->getCollection(),
        );

        $month = Carbon::create(
            $request->integer('year') ?: now()->year,
            $request->integer('month') ?: now()->month,
            1,
        );

        $blocks = $blockable ? $listBlocks->executer($blockable, $month->month, $month->year) : collect();

        return view('pages.partner.availability.index', [
            'title' => 'Disponibilités',
            'residences' => $residences,
            'vehicles' => $vehicles,
            'blockable' => $blockable,
            'month' => $month,
            'days' => $blockable ? $this->buildDays($month, $blocks) : [],
        ]);
    }

    /**
     * Résout le bien sélectionné en le cherchant uniquement dans les biens
     * du partenaire connecté (jamais depuis un id arbitraire de la requête)
     * — garde-fou contre la consultation du calendrier d'un tiers.
     *
     * @param  Collection<int, Residence>  $residences
     * @param  Collection<int, Vehicle>  $vehicles
     */
    private function resolveBlockable(string $type, ?string $id, Collection $residences, Collection $vehicles): Residence|Vehicle|null
    {
        $pool = $type === 'vehicle' ? $vehicles : $residences;

        $blockable = $id ? $pool->firstWhere('id', $id) : $pool->first();

        return $blockable ?? $residences->first() ?? $vehicles->first();
    }

    /**
     * @return array<int, array{date: Carbon, status: string, label: ?string}>
     */
    private function buildDays(Carbon $month, Collection $blocks): array
    {
        $days = [];

        for ($day = 1; $day <= $month->daysInMonth; $day++) {
            $date = $month->copy()->day($day);

            $block = $blocks->first(
                fn ($b) => $date->betweenIncluded($b->period['start'], $b->period['end']->copy()->subDay())
            );

            $days[] = [
                'date' => $date,
                'status' => $block ? ($block->origin === 'reservation' ? 'reserved' : 'manual') : 'available',
                'label' => $block && $block->origin !== 'reservation' ? $block->origin : null,
                'block' => $block,
            ];
        }

        return $days;
    }
}
