{{--
/**
* ------------------------------------------------------------
* File: custom_fields_form.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-09
* Created On: 2026-23-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

{{-- doing like this in this case since in this case, need col-md-6 for all other fields and col-md-12 for radio and checkboxes due to many options being present in this case --}}
@php
    $collection = collect($fields);
    $standardFields = $collection->filter(function($f) {
        return !in_array($f['element'], ['radio', 'checkbox']);
    });
    $choiceFields = $collection->filter(function($f) {
        return in_array($f['element'], ['radio', 'checkbox']);
    });
@endphp


<div class="row">
    @foreach($standardFields as $f)
        <div class="col-md-6 amg-form-field-row custom-field-row custom-fields-follow">
            <div class="form-group mb-4">
                <label for="{{ $f->nameToColumn() }}" class="@if($f->pivot->required) mandatory @endif form-label b1-text fw-bold mb-2">
                    {{ $f->name }}
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        @if($f['element'] == 'date' || $f['element'] == 'datetime')
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="currentColor"></path></svg>
                        @elseif($f['element'] == 'text')
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z" fill="black"></path></svg>
                        @elseif($f['element'] == 'time')
                            <i class="bi bi-clock"></i>
                        @elseif($f['element'] == 'dropdown')
                            <i class="bi bi-chevron-down"></i>
                        @endif
                    </span>
                    @if($f['element'] == 'dropdown')
                        @php
                            $selectedValue = $consumable[$f->nameToColumn()] ?? null;
                            $selectedClass = $f->getSelect2Class();
                            $selectedObj = $f->getSelectedOption($selectedValue);
                        @endphp
                        <select name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="cf-select2 form-control {{ $selectedClass }}">
                            <option value="">Select</option>
                            @if(isset($f['options']) && !empty($f['options']))
                                @if($selectedObj)
                                    <option value="{{ $selectedValue }}" selected>
                                        @if($f->preDefinedOptions == 1 || $f->preDefinedOptions == 17)
                                            {{ $selectedObj->name }}
                                        @elseif($f->preDefinedOptions == 2)
                                            {{ $selectedObj->username . '-' . $selectedObj->getGuranteedNameText() }}
                                        @else
                                            {{ $selectedObj->text }}
                                        @endif
                                    </option>
                                @else
                                    @foreach($f['options'] as $fd)
                                        <option value="{{ $fd }}" @selected($selectedValue == $fd)>
                                            {{ $fd }}
                                        </option>
                                    @endforeach
                                @endif
                            @endif
                        </select>
                    @elseif($f['element'] == 'date')
                        <input type="date" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" value="{{ isset($consumable[$f->nameToColumn()]) ? \Carbon\Carbon::parse($consumable[$f->nameToColumn()])->format('Y-m-d') : '' }}">
                    @elseif($f['element'] == 'time')
                        <input type="time" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" value="{{ isset($consumable[$f->nameToColumn()]) ? \Carbon\Carbon::parse($consumable[$f->nameToColumn()])->format('H:i') : '' }}">
                    @elseif($f['element'] == 'datetime')
                        <input type="datetime-local" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" value="{{ isset($consumable[$f->nameToColumn()]) ? \Carbon\Carbon::parse($consumable[$f->nameToColumn()])->format('Y-m-d\TH:i') : '' }}">
                    @elseif($f['element'] == 'text')
                        @if($f['format'] == '[0-9]*')
                            <input type="number" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" placeholder="Enter {{ $f->name }}" value="{{ $consumable[$f->nameToColumn()] ?? '' }}">
                        @else
                            <input type="text" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" placeholder="Enter {{ $f->name }}" value="{{ $consumable[$f->nameToColumn()] ?? '' }}">
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row">
    @foreach($choiceFields as $f)
        <div class="col-md-12 amg-form-field-row custom-field-row custom-fields-follow">
            <div class="form-group mb-4">
                <label for="{{ $f->nameToColumn() }}" class="@if($f->pivot->required) mandatory @endif form-label b1-text fw-bold mb-2">
                    {{ $f->name }}
                </label>
                @if($f['element'] == 'radio')
                    <div class="d-flex justify-content-start align-items-center gap-3 flex-wrap">
                        @foreach($f['options'] as $key => $fd)
                            <label class="form-label b1-text me-2 mb-0 d-flex justify-content-center align-items-center cursor-pointer">
                                {{ $fd }}
                                <input autocomplete="off" type="radio" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}_{{ $key }}" value="{{ $fd }}" class="form-check-input ms-2" @if(isset($consumable[$f->nameToColumn()]) && $consumable[$f->nameToColumn()] == $fd) checked @endif>
                            </label>
                        @endforeach
                    </div>
                @else
                    @php
                        $selectedValues = collect(json_decode($consumable[$f->nameToColumn()] ?? '[]', true) ?? []);
                    @endphp
                    <div class="row permissions-container w-100">
                        @foreach($f['options'] as $key => $fd)
                            <div class="col-6 col-md-3">
                                <div class="form-check py-2">
                                    <input type="checkbox" class="form-check-input" name="fields[{{ $f->nameToColumn() }}][]" id="{{ $f->nameToColumn() }}_{{ $key }}" value="{{ $fd }}" @if($selectedValues->contains($fd)) checked @endif>
                                    <label class="b3-text fw-normal" style="color:#7F7F7F" for="{{ $f->nameToColumn() }}_{{ $key }}">
                                        {{ $fd }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>