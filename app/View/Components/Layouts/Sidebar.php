<?php

namespace App\View\Components\Layouts;

use App\Models\Waybill;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $draftCount;
    public $warehousePendingCount;
    public $auditorPendingCount;
    public $supplyChainPendingCount;
    public $pendingApprovalsCount;

    public function __construct()
    {
        $user = Auth::user();

        // Draft count for Distribution Officer
        if ($user && $user->role === 'dist_officer') {
            $this->draftCount = Waybill::where('prepared_by', $user->id)
                ->where('status', 'draft')
                ->count();
        } else {
            $this->draftCount = 0;
        }

        // Role-specific pending counts
        $this->warehousePendingCount = Waybill::where('status', 'pending_warehouse')->count();
        $this->auditorPendingCount = Waybill::where('status', 'pending_auditor')->count();
        $this->supplyChainPendingCount = Waybill::where('status', 'pending_supply_chain')->count();

        // Total pending approvals (for admin)
        $this->pendingApprovalsCount = Waybill::whereIn('status', [
            'pending_warehouse',
            'pending_auditor',
            'pending_supply_chain',
            'pending_final'
        ])->count();
    }

    public function render(): View|Closure|string
    {
        return view('components.layouts.sidebar');
    }
}
