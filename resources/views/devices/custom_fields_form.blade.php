@foreach($fields as $f)
<div class="row custom-field-row">
    <div class="form-group col-md-12">
        <label for="{{ $f->nameToColumn() }}" class="control-label col-md-4 @if($f->pivot->required) mandatory @endif">{{ $f->name }}</label>
        @if($f['element']=='dropdown')
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-random"></i></span>
                    <select name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="cf-select2 form-control 
                    @if($f->preDefinedOptions == 1) custFieldLocation 
                    @elseif($f->preDefinedOptions == 2) custFieldUser 
                    @elseif($f->preDefinedOptions == 18) custFieldDept 
                    @elseif($f->preDefinedOptions == 3) custFieldDevice
                    @elseif($f->preDefinedOptions == 4) custFieldInternalPlace
                    @elseif($f->preDefinedOptions == 5) custFieldManufacturers
                    @elseif($f->preDefinedOptions == 6) custFieldModels
                    @elseif($f->preDefinedOptions == 7) custFieldComponent
                    @elseif($f->preDefinedOptions == 8) custFieldTickets
                    @elseif($f->preDefinedOptions == 9) custFieldRequest
                    @elseif($f->preDefinedOptions == 10) custFieldChangeManagement
                    @elseif($f->preDefinedOptions == 11) custFieldTasks
                    @elseif($f->preDefinedOptions == 12) custFieldLicense
                    @elseif($f->preDefinedOptions == 13) custFieldProjects
                    @elseif($f->preDefinedOptions == 14) custFieldPurchase
                    @elseif($f->preDefinedOptions == 15) custFieldSupplier
                    @elseif($f->preDefinedOptions == 16) custFieldContract
                    @elseif($f->preDefinedOptions == 17) custFieldESMSSoftwareList
                    @elseif($f->preDefinedOptions == '') custFieldDrop @endif">
                        <option value="">Select</option>
                        @if(isset($f['options']) && !empty($f['options']))
                            @if(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 1)
                                <option value="{{ $device[$f->nameToColumn()] }}" selected> {{ optional( App\Models\Location::where('id', $device[$f->nameToColumn()])->first() )->name }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 2)
                                <?php $userObj = App\Models\User::find($device[$f->nameToColumn()]); ?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected> {{ $userObj->username."-".$userObj->getGuranteedNameText() }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 18)
                                <option value="{{ $device[$f->nameToColumn()] }}" selected> {{ optional( App\Models\Department::where('id', $device[$f->nameToColumn()])->first() )->name }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 3)
                                <?php $deviceObj = App\Models\Device::select(DB::raw("case when name is not null and name != '' then concat(name, ' (', asset_tag, ')') else asset_tag end as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 4)
                                <?php $deviceObj = App\Models\Place::select(DB::raw('CONCAT(locations.name, " - ", places.place) as text'))->leftJoin('locations', 'places.location_id', '=', 'locations.id')->where('places.id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 5)
                                <?php $deviceObj = App\Models\Manufacture::select('name as text')->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 6)
                                <?php $deviceObj = App\Models\Model::select('name as text')->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 7)
                                <?php $deviceObj = App\Models\Component::select(DB::raw('concat(name, " (", unique_tag, ")") as text'))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 8)
                                <?php $deviceObj = App\Models\Ticket\Ticket::select(DB::raw("CONCAT('#',id, ' (', subject, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 9)
                                <?php $deviceObj = App\Models\Ticket\TicketProcureRequest::select(DB::raw("CONCAT(procure_tag, ' (', subject, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 10)
                                <?php $deviceObj = App\Models\ChangeManagement\Record::select(DB::raw("CONCAT(record_tag, ' (', subject, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 11)
                                <?php $deviceObj = App\Models\TaskManagement\Task::select(DB::raw("CONCAT('#',id, ' (', name, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 12)
                                <?php $deviceObj = App\Models\License::select(DB::raw("CONCAT(name, ' (', serial, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 13)
                                <?php $deviceObj = App\Models\ProjectManagement\Project::select(DB::raw("CONCAT('#',id, ' (', name, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 14)
                                <?php $deviceObj = App\Models\Purchase::select(DB::raw("CONCAT('#',invoice_no, ' (', invoice_date, ')') as text"))->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 15)
                                <?php $deviceObj = App\Models\Supplier::select('name as text')->where('id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @elseif(isset($device[$f->nameToColumn()]) && $f->preDefinedOptions == 16)
                                <?php $deviceObj = App\Models\Lease::select(DB::raw("CONCAT('#',lease_agreements.contract_number, ' (', s.name, ')') as text"))->leftJoin('suppliers as s', 's.id', '=', 'lease_agreements.leaser')->where('lease_agreements.id', $device[$f->nameToColumn()])->first();?>
                                <option value="{{ $device[$f->nameToColumn()] }}" selected>{{ $deviceObj->text }}</option>
                            @else
                                @foreach($f['options'] as $key => $fd)
                                    <option value="{{ $fd}}" @if(isset($device[$f->nameToColumn()]) && $device[$f->nameToColumn()] == $fd) selected @endif > {{ $fd }}</option>
                                @endforeach
                            @endif
                        @endif
                    </select>
                </div>
            </div>
        @elseif($f['element']=='date')
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="date" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" style="line-height: 100%;" placeholder="Enter {{ $f->name }} value" value="{{ isset($device[$f->nameToColumn()]) ? \Carbon\Carbon::parse($device[$f->nameToColumn()])->format('Y-m-d') : '' }}" />
                </div>
            </div>
        @elseif($f['element']=='time')
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                    <input type="time" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" style="line-height: 100%;" placeholder="Enter {{ $f->name }} value"  value="{{ isset($device[$f->nameToColumn()]) ? \Carbon\Carbon::parse($device[$f->nameToColumn()])->format('H:i') : '' }}" />
                </div>
            </div>

        @elseif($f['element']=='datetime')
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="datetime-local" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control"  style="line-height: 100%;"  placeholder="Enter {{ $f->name }} value"  value="{{ isset($device[$f->nameToColumn()]) ? \Carbon\Carbon::parse($device[$f->nameToColumn()])->format('Y-m-d\TH:i') : '' }}" />
                </div>
            </div>
        @elseif($f['element']=='text')
            @if($f['format']=='[0-9]*')
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-random"></i></span>
                        <input type="number" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" style="line-height: 100%;" placeholder="Enter {{ $f->name }} value" value="{{ isset($device[$f->nameToColumn()]) ? $device[$f->nameToColumn()] : '' }}" />
                    </div>
                </div>
            @else
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-random"></i></span>
                        <input type="text" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}" class="form-control" style="line-height: 100%;" placeholder="Enter {{ $f->name }} value" value="{{ isset($device[$f->nameToColumn()]) ? $device[$f->nameToColumn()] : '' }}" />
                    </div>
                </div>
            @endif
        @elseif($f['element']=='radio')
        <div class="col-md-8">
            <div class="input-group" style="direction: flex;">
                @if(isset($f['options']) && is_array($f['options']) && count($f['options']) > 0)
                    @foreach($f['options'] as $key => $fd)
                        <div class="radio">
                            <label>
                                <input type="radio" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}_{{ $key }}" value="{{ $fd }}" @if(isset($device[$f->nameToColumn()]) && $device[$f->nameToColumn()] == $fd) checked @endif />
                                {{ $fd }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @elseif($f['element']=='checkbox')
        <div class="col-md-8">
            <div class="input-group">
                @if(isset($f['options']) && is_array($f['options']) && count($f['options']) > 0)
                    @foreach($f['options'] as $key => $fd)
                        <div class="radio">
                            <label>
                                <input type="checkbox" name="fields[{{ $f->nameToColumn() }}]" id="{{ $f->nameToColumn() }}_{{ $key }}" value="{{ $fd }}" @if(isset($device[$f->nameToColumn()]) && $device[$f->nameToColumn()] == $fd) checked @endif />
                                {{ $fd }}
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endforeach