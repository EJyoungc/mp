@props(["title"=>'',"slot"=>'',"status"=>false])

<div class="modal fade {{ $status ? "show d-block": "d-none" }}" id="bs-example-modal-lg" tabindex="-1"
    aria-labelledby="bs-example-modal-lg" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title" id="myLargeModalLabel">
                    {{ $title }}
                </h4>
                <span type="button"  @click="
                    $wire.cancel();
                    $wire.dispatch('modal-cancel');
                "> X <x-spinner for="cancel" /> </span>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
