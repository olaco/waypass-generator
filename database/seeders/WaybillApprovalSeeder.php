<?php

namespace Database\Seeders;

use App\Models\Waybill;
use App\Models\WaybillApproval;
use App\Models\User;
use Illuminate\Database\Seeder;

class WaybillApprovalSeeder extends Seeder
{
    public function run(): void
    {
        // Get first waybill (or create one if none exists)
        $waybill = Waybill::first();

        if (!$waybill) {
            return;
        }

        // Get a user for approver
        $user = User::first();

        // Define the approval stages
        $stages = [
            'warehouse_manager' => [
                'label' => 'Warehouse Manager',
                'name' => 'John Smith',
                'status' => 'approved',
                'approved_at' => now()->subHours(2),
            ],
            'auditor' => [
                'label' => 'Auditor',
                'name' => 'Jane Doe',
                'status' => 'approved',
                'approved_at' => now()->subHours(1),
            ],
            'supply_chain_manager' => [
                'label' => 'Supply Chain Manager',
                'name' => 'Robert Johnson',
                'status' => 'pending',
                'approved_at' => null,
            ],
            'final_authority' => [
                'label' => 'Final Authority',
                'name' => 'Michael Brown',
                'status' => 'pending',
                'approved_at' => null,
            ],
        ];

        foreach ($stages as $stage => $data) {
            // Create simulated approval
            WaybillApproval::create([
                'waybill_id' => $waybill->id,
                'approver_id' => $user->id ?? 1,
                'stage' => $stage,
                'status' => $data['status'],
                'signature_path' => null, // Simulated signature
                'signature_data' => $data['status'] === 'approved' ? $this->generateSimulatedSignature($stage) : null,
                'remarks' => $data['status'] === 'approved' ? 'Approved by ' . $data['name'] : null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Simulated',
                'approved_at' => $data['approved_at'],
            ]);
        }

        // Update the waybill with approver names
        $waybill->update([
            'warehouse_approver_name' => 'John Smith',
            'warehouse_approved_at' => now()->subHours(2),
            'auditor_approver_name' => 'Jane Doe',
            'auditor_approved_at' => now()->subHours(1),
            // Keep others null for pending
        ]);
    }

    private function generateSimulatedSignature($stage): string
    {
        // Create a simple simulated signature as base64 SVG
        $names = [
            'warehouse_manager' => 'John Smith',
            'auditor' => 'Jane Doe',
            'supply_chain_manager' => 'Robert Johnson',
            'final_authority' => 'Michael Brown',
        ];

        $name = $names[$stage] ?? 'Approver';
        $initials = implode('', array_map(fn($w) => $w[0], explode(' ', $name)));

        // Simple SVG signature simulation
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="50" viewBox="0 0 200 50">';
        $svg .= '<text x="10" y="35" font-family="Dancing Script, cursive" font-size="28" fill="#000" font-style="italic">';
        $svg .= $name;
        $svg .= '</text>';
        $svg .= '<text x="10" y="48" font-family="Arial" font-size="8" fill="#666">' . $initials . ' • ' . date('d/m/Y') . '</text>';
        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
