<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
</div>
<div class="form-group">
    <label for="">Name</label>
    <input type="text" class="form-control" wire:model="name">
    <x-input-error for="name" />
</div>
<div class="form-group">
    <label for="">Email</label>
    <input type="email" class="form-control" wire:model="email"  >
    <x-input-error for="email" />
</div>

<div class="form-group">
    <label for="">Phone</label>
    <input type="text" class="form-control" wire:model="phone">
    <x-input-error for="phone" />
</div>