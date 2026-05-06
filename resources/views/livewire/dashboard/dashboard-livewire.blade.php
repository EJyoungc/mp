<div>
    @if(auth()->user()->isSystemAdmin())
        <livewire:dashboard.system-admin-dashboard />
    @elseif(auth()->user()->isPharmacyAdmin())
        <livewire:dashboard.pharmacy-dashboard />
    @elseif(auth()->user()->isOrgAdmin())
        <livewire:dashboard.org-admin-dashboard />
    @elseif(auth()->user()->isPractitioner())
        <livewire:dashboard.practitioner-dashboard />
    @elseif(auth()->user()->isMother())
        <livewire:dashboard.mother-dashboard />
    @else
        <div class="p-4 text-center">
            <h3>Unauthorized</h3>
            <p>You do not have access to the dashboard.</p>
        </div>
    @endif
</div>
