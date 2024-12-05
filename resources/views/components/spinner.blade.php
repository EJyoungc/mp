@props(['for'])
<span class="spinner-border spinner-border-sm" wire:loading wire:target='{{ $for }}' aria-hidden="true"></span>