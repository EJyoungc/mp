@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" wire:model="name">
    <x-input-error for="name" />
</div>
<div class="form-group">
    <label for="email">Date of Birth</label>
    <input type="date" class="form-control" wire:model.live="date_of_birth">
    <x-input-error for="date_of_birth" />
</div>
<div class="form-group">
    <label for="Age">Age</label>
    <input type="text" class="form-control" readonly wire:model="age">
    <x-input-error for="age" />

</div>

<div class="form-group">
    <label for="Age">Phone</label>
    <input type="text" class="form-control" wire:model="phone">
    <x-input-error for="phone" />

</div>



<div class="form-group">
    <label for="">Marial Status</label>
    <select name="" id="" class="form-control" wire:model="marital_status">
        <option value="">Select</option>
        @foreach (SD::getMaritalStatuses() as $key => $value)
            <option value="{{ $value }}">{{ $value }}</option>
        @endforeach
    </select>
    <x-input-error for="marital_status" />
</div>


<div class="form-group">
    <label for="">Religion</label>
    <select name="" id="" class="form-control" wire:model="religion">
        <option value="">Select</option>
        @foreach (SD::getReligions() as $key => $value)
            <option value="{{ $value }}">{{ $value }}</option>
        @endforeach
    </select>
    <x-input-error for="religion" />
</div>

<div class="form-group">
    <label for="">Level of Education</label>
    <select name="" id="" class="form-control" wire:model="level_of_education">
        <option value="">Select</option>
        @foreach (SD::getEducationLevels() as $key => $value)
            <option value="{{ $value }}">{{ $value }}</option>
        @endforeach
    </select>
    <x-input-error for="level_of_education" />

</div>


<div class="form-group">
    <label for="">Occupation</label>
    <input type="text" class="form-control" wire:model="occupation">
    <x-input-error for="occupation" />
</div>

<div class="form-group">
    <label for="">Address</label>
    <input type="text" class="form-control" wire:model="address">
    <x-input-error for="address" />
</div>

<div class="form-group">
    <label for="traditional Authority">Traditional Authority</label>
    <input type="text" class="form-control" wire:model="traditional_authority">
    <x-input-error for="traditional_authority" />
</div>

<div class="form-group">
    <label for="">Next of Kin</label>
    <input type="text" class="form-control" wire:model="next_of_kin">
    <x-input-error for="next_of_kin" />
</div>

<div class="form-group">
    <label for="">Next of Kin mobile</label>
    <input type="text" class="form-control" wire:model="next_of_kin_mobile">
    <x-input-error for="next_of_kin_mobile" />
</div>


<!-- Height Input -->
<div class="mb-3 form-group">
    <label for="height" class="form-label">Height (cm)</label>
    <input type="number" id="height" class="form-control" wire:model="height">
    <x-input-error for="height" />

</div>

<!-- Leg or Spine Issues -->
<div class="mb-3 form-group">
    <label class="form-label">Leg or Spine Issues</label>
    <div>
        <div class="form-check form-check-inline">
            <input type="radio" id="legOrSpineNo" value="no" wire:model="legOrSpine" class="form-check-input">
            <label class="form-check-label" for="legOrSpineNo">No</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" id="legOrSpineYes" value="yes" wire:model="legOrSpine"
                class="form-check-input">
            <label class="form-check-label" for="legOrSpineYes">Yes</label>
        </div>
    </div>
    @error('legOrSpine')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Deformity -->
<div class="mb-3">
    <label class="form-label">Deformity</label>
    <div>
        <div class="form-check form-check-inline">
            <input type="radio" id="deformityNo" value="no" wire:model="deformity" class="form-check-input">
            <label class="form-check-label" for="deformityNo">No</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" id="deformityYes" value="yes" wire:model="deformity" class="form-check-input">
            <label class="form-check-label" for="deformityYes">Yes</label>
        </div>
    </div>
    @error('deformity')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Deliveries -->
<div class="mb-3 form-group">
    <label for="deliveries" class="form-label">Deliveries</label>
    <select id="deliveries" class="form-control" wire:model="deliveries">
        <option value="">Select</option>

        @for ($i = 0; $i <= 10; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
    @error('deliveries')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<!-- Abortions -->
<div class="mb-3 form-group">
    <label for="abortions" class="form-label">Abortions</label>
    <select id="abortions" class="form-control" wire:model="abortions">
        <option value="">Select</option>
        @for ($i = 0; $i <= 3; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
    @error('abortions')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>




{{-- <!-- Abnormal Deliveries --> --}}





<!-- Still Births -->
<div class="form-group">
    <label>Still Births</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="stillBirthsNo" class="form-check-input" value="no" wire:model="stillBirths">
        <label for="stillBirthsNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="stillBirthsYes" class="form-check-input" value="yes" wire:model="stillBirths">
        <label for="stillBirthsYes" class="form-check-label">Yes</label>
    </div>
    @error('stillBirths')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- C-Section -->
<div class="form-group">
    <label>C-Section</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="cSectionNo" class="form-check-input" value="no" wire:model="cSection">
        <label for="cSectionNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="cSectionYes" class="form-check-input" value="yes" wire:model="cSection">
        <label for="cSectionYes" class="form-check-label">Yes</label>
    </div>
    @error('cSection')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Vacum -->
<div class="form-group">
    <label>Vacum</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="vacumNo" class="form-check-input" value="no" wire:model="vacum">
        <label for="vacumNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="vacumYes" class="form-check-input" value="yes" wire:model="vacum">
        <label for="vacumYes" class="form-check-label">Yes</label>
    </div>
    @error('vacum')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Multiple Deliveries -->
<div class="form-group">
    <label>Multiple Deliveries</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="multipleNo" class="form-check-input" value="no" wire:model="multiple">
        <label for="multipleNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="multipleYes" class="form-check-input" value="yes" wire:model="multiple">
        <label for="multipleYes" class="form-check-label">Yes</label>
    </div>
    @error('multiple')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>



<!-- Tuberculosis -->
<div class="form-group">
    <label>Tuberculosis</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="tuberculosisNo" class="form-check-input" value="no"
            wire:model="tuberculosis">
        <label for="tuberculosisNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="tuberculosisYes" class="form-check-input" value="yes"
            wire:model="tuberculosis">
        <label for="tuberculosisYes" class="form-check-label">Yes</label>
    </div>
    @error('tuberculosis')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Asthma -->
<div class="form-group">
    <label>Asthma</label><br>
    <div class="form-check form-check-inline">
        <input type="radio" id="asthmaNo" class="form-check-input" value="no" wire:model="asthma">
        <label for="asthmaNo" class="form-check-label">No</label>
    </div>
    <div class="form-check form-check-inline">
        <input type="radio" id="asthmaYes" class="form-check-input" value="yes" wire:model="asthma">
        <label for="asthmaYes" class="form-check-label">Yes</label>
    </div>
    @error('asthma')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Menstrual Cycle -->
<div class="form-group">
    <label>Menstrual Cycle</label>
    <select wire:model="menstrualCycle" class="form-control">
        <option value="">Select</option>
        <option value="regular">Regular</option>
        <option value="abnormal">Abnormal or Variable</option>
    </select>
    @error('menstrualCycle')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

