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
            ['name' => 'WEL_CP', 'system_name' => 'CAPATACION', 'db_name' => 'banorte_captacion_pvh'],
            ['name' => 'WEL_CSM', 'system_name' => 'CONSUMO', 'db_name' => 'banorte_consumo_pvh'],
            ['name' => 'WEL_NOM', 'system_name' => 'NOMINA', 'db_name' => 'banorte_nomina_pvh'],
            ['name' => 'PTB', 'system_name' => 'PLENITUD', 'db_name' => 'banorte_plenitud'],
            ['name' => 'PVH_TRADICIONAL', 'system_name' => 'PVH', 'db_name' => 'banorte_pvh'],
						['name' => 'BLP', 'system_name' => 'TDC', 'db_name' => 'banorte_tdc_arquetipos'],
						['name' => 'AUTO WEB', 'system_name' => 'AUTO SEGURO', 'db_name' => 'banorte_auto_seguro'],
						['name' => 'AUTO IN', 'system_name' => 'CLIENTES AUTO', 'db_name' => 'banorte_clientes_auto'],
						['name' => 'AUTOS MEXICO', 'system_name' => 'SEGUROS DE AUTO MEXICO', 'db_name' => 'banorte_segurosdeautomexico'],
						['name' => 'RASTREADOR', 'system_name' => 'RASTREATOR', 'db_name' => 'banorte_rastreator'],
						['name' => 'RTMK', 'system_name' => 'RTMK', 'db_name' => 'banorte_rtmk'],
        ];

        foreach ($campaigns as $campaign) {
            \App\Models\Campaign::firstOrCreate($campaign);
        }
    }
}
