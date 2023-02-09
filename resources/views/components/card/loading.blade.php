@props(['target' => null])

<div wire:loading.delay.longest.class="overlay light" {{ $target ? "wire:target=\"{$target}\"" : null }}>
    <div class="d-none justify-content-center align-items-center" wire:loading.delay.longest.class="d-flex" wire:loading.delay.longest.class.remove="d-none" {{ $target ? "wire:target=\"{$target}\"" : null }}>
        <i class="fas fa-sync-alt fa-2x fa-spin"></i>
    </div>
</div>
