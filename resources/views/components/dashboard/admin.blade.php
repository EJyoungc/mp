
@props(['users', 'mothers', 'tips', 'messages'])

<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4>Users </h4>
                    <h6>{{ $users->count() }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-blue ">
                <div class="card-body">
                    <h4>Mothers</h4>
                    <h6>{{ $mothers->count() }}</h6>

                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-success ">
                <div class="card-body">
                    <h4>Tips</h4>
                    <h6>{{ $tips->count() }}</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card bg-gradient-dark">
                <div class="card-body">
                    <h4>Messages Sent </h4>
                    <h6>{{ $messages->count() }}</h6>
                </div>
            </div>
        </div>

    </div>
</div>
