<div class="vh-100 d-flex justify-content-center align-items-center bg-light ">
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <x-modal status="{{ $modal }}" title="Add Organization">

        <form wire:submit.prevent="store">
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" wire:model="name" placeholder="Enter name">
                <x-input-error for="name" />
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control" wire:model="email" placeholder="Enter email">
                <x-input-error for="email" />
            </div>
            <div class="form-group">
                <label for="">Phone</label>
                <input type="text" class="form-control" wire:model="phone" placeholder="Enter phone">
                <x-input-error for="phone" />
            </div>
            <div class="form-group">
                <label for="">Address</label>
                <input type="text" class="form-control" wire:model="address" placeholder="Enter address">
                <x-input-error for="address" />
            </div>
            <div class="form-group">
                <label for="">Website</label>
                <input type="text" class="form-control" wire:model="website" placeholder="Enter website">
                <x-input-error for="website" />
            </div>

            <div class="form-group">
                <label for="">Description</label>
                <textarea class="form-control" wire:model="description" placeholder="Enter description"></textarea>
                <x-input-error for="description" />
            </div>

            <button type="submit" class="btn btn-dark">Save <x-spinner for="store" /> </button>
        </form>

    </x-modal>


    <div class="card col-11 col-lg-5 col-md-11 ">
        @if ($user->organization_id == null)
            <div class="card-body  ">

                <h3 class=" text-bold">Organization </h3>

                <div class="d-flex ">
                    <div class="w-100  ">
                        <input type="text" class="form-control border-1 border-primary rounded-pill  "
                            wire:model.live="search" placeholder="Search for Organization">
                        <x-input-error for="search" />
                    </div>
                    <div class="pl-1">
                        <button wire:click.prevent="create" class="btn btn-dark  rounded-circle">
                            <i class="fa fa-plus" wire:loading.remove wire:target='create' aria-hidden="true"></i>
                            <x-spinner for="create" />
                        </button>
                    </div>
                    <div class="pl-1">
                        <button wire:click.prevent="get_all_orgs" class="btn btn-dark  rounded-circle">
                            <i class="fa fa-list" wire:loading.remove wire:target='get_all_orgs' aria-hidden="true"></i>
                            <x-spinner for="get_all_orgs" />
                        </button>
                    </div>

                </div>
                <small class="text-muted ">Select the organization if not found you can add a new one</small>
                <ul class="list-group py-1">
                    @if (!empty($organization->id))
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center text-capitalize text-primary border-primary ">
                            {{ $organization->name }}
                            <button class="btn btn-outline-danger btn-sm rounded-circle"
                                wire:click.prevent="remove_org"><i class="fas fa-times" wire:loading.remove
                                    wire:target='remove_org'></i> <x-spinner for="remove_org" /></button>
                        </li>
                    @endif


                </ul>
                @if (!empty($organization->id))
                    <button class="btn btn-primary w-100" wire:click.prevent="save">Save <x-spinner
                            for="save" /></button>
                @endif

                @if (!empty($search))
                    <ul class="list-group pt-1">
                        @forelse ($organizations as $item)
                            <li class="list-group-item d-flex align-items-center"
                                wire:click.prevent="select_org({{ $item->id }})">
                                <i class="fas fa-angle-right"></i> <span
                                    class="pl-2 text-capitalize">{{ $item->name }}</span>
                            </li>
                        @empty

                            <li class="list-group-item d-flex justify-content-center align-items-center ">
                                <h4 class="text-muted text-center">EMPTY</h4>

                            </li>
                        @endforelse
                    </ul>
                @endif

                @if ($liststatus)
                    <ul class="list-group pt-1">
                        @forelse ($organizations as $item)
                            <li class="list-group-item d-flex align-items-center"
                                wire:click.prevent="select_org({{ $item->id }})">
                                <i class="fas fa-angle-right"></i> <span
                                    class="pl-2 text-capitalize">{{ $item->name }}</span>
                            </li>
                        @empty
                        @endforelse
                    </ul>
                @endif
                <form action="{{ route('logout') }}" method="POST" id="logout">
                    @csrf
                </form>

                <button class="btn btn-danger" onclick="document.getElementById('logout').submit()" >Logout</button>
            </div>
        @else
            <div class="card-body  ">


                <div class="alert alert-info" role="alert">
                    <strong>info</strong>
                    <p>
                        Dear <span class="text-dark text-bold" > {{ Auth::user()->name }}</span>  <br>

                        You have an organization <span class="text-dark text-bold" >{{ Auth::user()->organization->name }}</span> <br>
                        a notification has been sent to administrator <br>
                        Wait for approval from Administrator
                        <br>
                        You can undo the selection if you would like to change organization
                    </p>
                    <button class="btn btn-dark" wire:click.prevent="undo" >Undo <x-spinner for="undo" /></button>

                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout">
                    @csrf
                </form>
                <button class="btn btn-danger" onclick="document.getElementById('logout').submit()" >Logout</button>
            </div>


        @endif
    </div>

</div>
