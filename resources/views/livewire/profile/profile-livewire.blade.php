<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profile</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12 text-center">
                                <img src="{{ $user->profile_picture ?  asset('assets/uploads/' . $user->profile_picture) : asset('face-0.jpg') }}"
                                    class="border rounded-circle" width="150" height="150" alt="User Image">
                                <br />
                                <button class="btn btn-dark" onclick="document.getElementById('photo_field').click();">Upload <div wire:loading wire:target='photo'>
                                        <span class="spinner-border spinner-border-sm " role="status"
                                            aria-hidden="true"></span>
                                    </div></button>
                                <button class="btn btn-danger" wire:click='remove'> Remove<div wire:loading wire:target='remove'>
                                        <span class="spinner-border spinner-border-sm " role="status"
                                            aria-hidden="true"></span>
                                    </div></button>
                                <div class="form-group"> <input type="file" wire:model='photo' id='photo_field' hidden>
                                    @error('photo')
                                        <span class='text-danger'>{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <p>
                            <form action="" method="get">

                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" wire:model.defer='name' class="form-control">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>


                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" wire:model.defer='email' class="form-control"
                                        autocomplete="false">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="name">Occupation</label>
                                    <input type="text" wire:model.defer='occupation' class="form-control">
                                    @error('occupation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                                {{-- <div class="form-group">
                                    <label for="name">Description</label>
                                    <textarea type="text" wire:model.defer='description' class="form-control">

                                    </textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div> --}}

                                <div class="form-group">
                                    <label for="">Old Password</label>
                                    <input type="password" class="form-control" wire:model.defer='current_password'>
                                    @error('current_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">New Password</label>
                                    <input type="password" class="form-control" wire:model.defer='new_password'>
                                    @error('new_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button wire:click.prevent='update' class="btn btn-dark">Save
                                    <div wire:loading wire:target='update'>
                                        <span class="spinner-border spinner-border-sm " role="status"
                                            aria-hidden="true"></span>
                                    </div>


                                    {{--  --}}
                                </button>
                            </form>
                            </p>

                            @if(auth()->user()->isOrgAdmin() && auth()->user()->organization)
                                <hr>
                                <div class="mt-4">
                                    <h5 class="text-bold mb-3"><i class="fas fa-hospital-alt mr-2"></i> Organization Details</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="org_name">Organization Name</label>
                                                <input type="text" wire:model.defer="org_name" class="form-control" placeholder="Enter name">
                                                @error('org_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="org_email">Organization Email</label>
                                                <input type="email" wire:model.defer="org_email" class="form-control" placeholder="Enter email">
                                                @error('org_email') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="org_phone">Organization Phone</label>
                                                <input type="text" wire:model.defer="org_phone" class="form-control" placeholder="Enter phone">
                                                @error('org_phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="org_website">Website URL</label>
                                                <input type="text" wire:model.defer="org_website" class="form-control" placeholder="https://example.com">
                                                @error('org_website') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="org_address">Physical Address</label>
                                        <input type="text" wire:model.defer="org_address" class="form-control" placeholder="Enter physical address">
                                        @error('org_address') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="org_description">Description / Bio</label>
                                        <textarea wire:model.defer="org_description" class="form-control" rows="3" placeholder="Briefly describe your organization..."></textarea>
                                        @error('org_description') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_pharmacy_profile" wire:model="is_pharmacy">
                                            <label class="custom-control-label" for="is_pharmacy_profile">This is a Pharmacy</label>
                                        </div>
                                        <small class="text-muted">Uncheck if this is a Medical Institution (Clinic/Hospital).</small>
                                    </div>
                                    
                                    <button wire:click.prevent="update" class="btn btn-navy mt-2">
                                        <i class="fas fa-save mr-1"></i> Update Organization Details
                                        <div wire:loading wire:target="update">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </div>
                                    </button>
                                </div>
                                <style>
                                    .btn-navy { background-color: #001f3f; color: #fff; }
                                    .btn-navy:hover { background-color: #002d5c; color: #fff; }
                                </style>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
</div>
