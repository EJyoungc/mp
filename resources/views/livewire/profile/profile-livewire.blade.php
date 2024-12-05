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
                                <div class="form-group">
                                    <label for="name">Description</label>
                                    <textarea type="text" wire:model.defer='description' class="form-control">

                                    </textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

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
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
</div>
