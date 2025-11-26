<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = [
            ['name' => 'WEL_CP', 'system_name' => 'captacion', 'db_name' => 'banorte_captacion_pvh'],
            ['name' => 'WEL_CSM', 'system_name' => 'consumo', 'db_name' => 'banorte_consumo_pvh'],
            ['name' => 'WEL_NOM', 'system_name' => 'nomina', 'db_name' => 'banorte_nomina_pvh'],
            ['name' => 'PTB', 'system_name' => 'plenitud', 'db_name' => 'banorte_plenitud'],
            ['name' => 'PVH_TRADICIONAL', 'system_name' => 'pvh', 'db_name' => 'banorte_pvh'],
						['name' => 'BLP', 'system_name' => 'tdc', 'db_name' => 'banorte_tdc_arquetipos'],
						['name' => 'AUTO WEB', 'system_name' => 'auto_seguro', 'db_name' => 'banorte_auto_seguro'],
						['name' => 'AUTO IN', 'system_name' => 'clientes_auto', 'db_name' => 'banorte_clientes_auto'],
						['name' => 'AUTOS MEXICO', 'system_name' => 'seguros_de_auto_mexico', 'db_name' => 'banorte_segurosdeautomexico'],
						['name' => 'RASTREADOR', 'system_name' => 'rastreator', 'db_name' => 'banorte_rastreator'],
						['name' => 'RTMK', 'system_name' => 'rtmk', 'db_name' => 'banorte_rtmk'],
        ];

        foreach ($campaigns as $campaign) {
            \App\Models\Campaign::firstOrCreate($campaign);
        }
    }
}
