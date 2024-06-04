<?php

//one..............................................................


public function scheduleSave(Request $request)
{

    $userId = $request->input('user_id');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $activeScheduleExists = UserSchedule::where('user_id', $userId)
        ->whereDate('from_date', date('Y-m-d', strtotime($fromDate))) // Check if there's an active schedule
        ->exists();

    if ($activeScheduleExists) {
        return back()
            ->withInput($request->all())
            ->withErrors(['custom_message' => 'The user already has an active schedule.']);
    }


    $userId = $request->input('user_id');
    $fromDate = Carbon::parse($request->input('from_date')); // Use Carbon for date/time manipulation
    $toDate = Carbon::parse($request->input('to_date'));

    $activeScheduleExists = UserSchedule::where('user_id', $userId)
        ->where(function ($query) use ($fromDate, $toDate) {
            $query->where(function ($subquery) use ($fromDate) {
                $subquery->where('from_date', '<', $fromDate)
                    ->where('to_date', '>', $fromDate);
            })->orWhere(function ($subquery) use ($toDate) {
                $subquery->where('from_date', '<', $toDate)
                    ->where('to_date', '>', $toDate);
            })->orWhere(function ($subquery) use ($fromDate, $toDate) {
                $subquery->where('from_date', '>=', $fromDate)
                    ->where('to_date', '<=', $toDate);
            });
        })
        ->exists();

    if ($activeScheduleExists) {
        return back()
            ->withInput($request->all())
            ->withErrors(['custom_message' => 'The user already has an active schedule.']);
    }

    try {
        $data = $request->all();

        $data['is_split_shift'] = $request->has('is_split_shift') ? 1 : 0;
        $data['created_by'] = Auth::user()->id;
        $schedule = UserSchedule::create($data);

        $this->createShiftDays($schedule, $data);

        return redirect()->route('schedule')
            ->with('success_msg', 'Schedule created successfully.');
    } catch (\Throwable $e) {
        // DB::rollback();
        throw $e;
    }
}



//Two........................................................................................




public function scheduleUpdate(Request $request)
{


    $userId = $request->input('user_id');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $activeScheduleExists = UserSchedule::where('user_id', $userId)
        ->where('id', '!=', $request->id)
        ->whereDate('from_date', date('Y-m-d', strtotime($fromDate))) // Check if there's an active schedule
        ->exists();

    if ($activeScheduleExists) {
        return back()
            ->withInput($request->all())
            ->withErrors(['custom_message' => 'The user already has an active schedule.']);
    }


    $userId = $request->input('user_id');
    $fromDate = Carbon::parse($request->input('from_date')); // Use Carbon for date/time manipulation
    $toDate = Carbon::parse($request->input('to_date'));

    $activeScheduleExists = UserSchedule::where('user_id', $userId)
        ->where('id', '!=', $request->id)
        ->where(function ($query) use ($fromDate, $toDate) {
            $query->where(function ($subquery) use ($fromDate) {
                $subquery->where('from_date', '<', $fromDate)
                    ->where('to_date', '>', $fromDate);
            })->orWhere(function ($subquery) use ($toDate) {
                $subquery->where('from_date', '<', $toDate)
                    ->where('to_date', '>', $toDate);
            })->orWhere(function ($subquery) use ($fromDate, $toDate) {
                $subquery->where('from_date', '>=', $fromDate)
                    ->where('to_date', '<=', $toDate);
            });
        })
        ->exists();

    if ($activeScheduleExists) {
        return back()
            ->withInput($request->all())
            ->withErrors(['custom_message' => 'The user already has an active schedule.']);
    }



    $data = UserSchedule::where('id', $request->id)->first();
    try {
        $data->update([
            'title' => $request->title,
            'user_id' => $request->user_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'break' => $request->break,
            'is_split_shift' => $request->has('is_split_shift') ? 1 : 0,
            'split_shift_time' => $request->split_shift_time,
            'description' => $request->description
        ]);


        UserDailySchedule::where('user_schedule_id', $request->id)->delete();

        $fromDate = Carbon::parse($request['from_date']);
        $toDate = Carbon::parse($request['to_date']);
        $allData = [];
        while ($fromDate->lte($toDate)) {
            $UserDailySchedule = new UserDailySchedule;
            $UserDailySchedule->user_schedule_id = $request->id;
            $UserDailySchedule->date = $fromDate->toDateString();
            $UserDailySchedule->from_time = $fromDate->toTimeString();
            $UserDailySchedule->to_time = $toDate->toTimeString();
            $UserDailySchedule->break = $request['break'];
            $UserDailySchedule->is_split_shift = $request->has('is_split_shift') ? 1 : 0;
            $UserDailySchedule->split_shift_time = $request['split_shift_time'];
            $UserDailySchedule->description = $request['description'];
            $UserDailySchedule->created_by = Auth::user()->id;
            // $UserDailySchedule->schedule_status = '';
            $UserDailySchedule->save();
            $fromDate->addDay();
        }

        return redirect()->route('schedule')
            ->with('success_msg', 'Schedule updated successfully.');
    } catch (\Throwable $e) {

        throw $e;
    }
}



//Three....adddd..................................................................................................


<form id="addScheduleFrom" method="POST" action="{{ route('schedule.save') }}">
@csrf

<div class="form-styling">
    <div class="row g-3">
        <div class="col-md-12">
            <label for="your-name" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title"
                placeholder="Enter Title" value="{{ old('title') }}">
        </div>
        <div class="col-md-12">
            <label for="your-surname" class="form-label">Assign Shift</label>

            <div class="option_stylee">
                <select id="department-selection" name="user_id">
                    <option value="">Select User</option>
                    @forelse ($users as $user)
                        <option  {{ old('user_id') == $user->id ? "selected" : "" }} value="{{ $user->id }}">{{ $user->first_name }}
                        </option>
                    @empty
                        <option value="" disabled>User Not Found !</option>
                    @endforelse
                </select>
            </div>

            @if($errors->has('custom_message'))
<div class="alert alert-danger">
{{ $errors->first('custom_message') }}
</div>
@endif

        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <label for="your-name" class="form-label">From</label>
                <div class="col my-0">

                    <input id="start_date_time" class="form-control"
                        type="datetime-local" name="from_date" value="{{ old('from_date') }}" />
                </div>
                {{-- <div class="col my-0">
                    <div class="time_style">
                        <input type="time" class="appt" name="from_time">
                    </div>
                </div> --}}
            </div>


        </div>


        <div class="col-md-6">
            <div class="row g-3">
                <label for="your-name" class="form-label">To</label>
                <div class="col my-0">

                    <input id="to_date_time" class="form-control" type="datetime-local"
                        name="to_date" value="{{ old('to_date') }}" />
                </div>
                {{-- <div class="col my-0">

                    <div class="time_style">
                        <input type="time" class="appt" name="appt">
                    </div>
                </div> --}}
            </div>


        </div>

        <div class="col-md-2">
            <label for="your-name" class="form-label">Break:</label>
            {{-- <input type="time" class="form-control" id="break" name="break"
                placeholder="1:00"> --}}
            <select class="form-control" id="break" name="break">
                @foreach (User::breakHrs() as $key => $value)
                    <option {{ old('break') == $key ? "selected" : "" }}  value="{{ $key }}">
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="checkbox" id="is_split_shift" name="is_split_shift" {{ old('is_split_shift') ? "checked" : "" }}>
            <label for="split_shift_time"> Split Shift</label>
            <div class=" @if(old('is_split_shift'))  @else d-none @endif  split_shift_time_container">
                <input type="time" class="form-control" id="split_shift_time"
                    name="split_shift_time" placeholder="1:00" value="{{ old('split_shift_time') }}">
            </div>
        </div>

        <div class="col-12">
            <label for="exampleFormControlTextarea1"
                class="form-label">Description</label>
            <textarea required class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"
                placeholder="Description">{{ old('description') }}</textarea>
        </div>




    </div>
    <div class="col-12">
        <div class="row">
            <div class="mt-3">
                <div class="submit_btn_styling">
                    <button type="submit" class="btn active ">Save</button>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</form>

//four..............................................................................



<form id="addScheduleFrom" method="POST" action="{{ route('schedule.update') }}">
@csrf
<input type="hidden" name="id" value="{{ $data->id }}">
<div class="form-styling">
    <div class="row g-3">
        <div class="col-md-12">
            <label for="your-name" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title"
                placeholder="Enter Title" value="{{ old('title', $data->title ?? '') }}">
        </div>
        <div class="col-md-12">
            <label for="your-surname" class="form-label">Assign Shift</label>

            <div class="option_stylee">
                <select id="department-selection" name="user_id">
                    <option value="">Select User</option>
                    @forelse ($users as $user)
                        <option   {{ old('user_id', $data->user_id) == $user->id ? 'selected' : '' }}
                            value="{{ $user->id }}">{{ $user->first_name }}
                        </option>
                    @empty
                        <option value="" disabled>User Not Found !</option>
                    @endforelse
                </select>
            </div>

            @if($errors->has('custom_message'))
            <div class="alert alert-danger">
                {{ $errors->first('custom_message') }}
            </div>
        @endif

        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <label for="your-name" class="form-label">From</label>
                <div class="col my-0">

                    <input id="start_date_time" class="form-control"
                        type="datetime-local" name="from_date"
                        value="{{ old('from_date', $data->from_date ?? '') }}" />
                </div>
                {{-- <div class="col my-0">
                    <div class="time_style">
                        <input type="time" class="appt" name="from_time">
                    </div>
                </div> --}}
            </div>


        </div>


        <div class="col-md-6">
            <div class="row g-3">
                <label for="your-name" class="form-label">To</label>
                <div class="col my-0">

                    <input id="to_date_time" class="form-control" type="datetime-local"
                        name="to_date"  value="{{ old('to_date', $data->to_date ?? '') }}" /> 
                </div>
                {{-- <div class="col my-0">

                    <div class="time_style">
                        <input type="time" class="appt" name="appt">
                    </div>
                </div> --}}
            </div>


        </div>

        <div class="col-md-2">
            <label for="your-name" class="form-label">Break:</label>
            {{-- <input type="time" class="form-control" id="break" name="break"
                placeholder="1:00" value="{{ $data->break }}"> --}}

            <select class="form-control" id="break" name="break">



                @foreach (User::breakHrs() as $key => $value)
                    <option  {{ old('break', $data->break) == $key ? 'selected' : '' }}
                        value="{{ $key }}">
                        {{ $value }}
                    </option>
                @endforeach

            </select>
        </div>
        <div class="col-md-2">
            <input type="checkbox" id="is_split_shift" name="is_split_shift"
                {{ $data->is_split_shift ? 'checked' : '' }}>
            <label for="split_shift_time"> Split Shift</label>
            <div
                class=" {{ $data->is_split_shift ? '' : 'd-none' }}  split_shift_time_container">
                <input type="time" class="form-control" id="split_shift_time"
                    name="split_shift_time" placeholder="1:00"
                    value="{{ $data->split_shift_time }}">
            </div>
        </div>

        <div class="col-12">
            <label for="exampleFormControlTextarea1"
                class="form-label">Description</label>
            <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"
                placeholder="Description">{{ old('description', $data->description ?? '') }}
            </textarea>
        </div>




    </div>
    <div class="col-12">
        <div class="row">
            <div class="mt-3">
                <div class="submit_btn_styling">
                    <button type="submit" class="btn active ">Save</button>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</form>


//Five........................................................................................





public function employeeChangeStatus(Request $request)
{
    try {
        //PunchIn
        if ($request->status == 1) {


            $currentUser = Auth::user()->id;
            $res = $this->checkScheduleExists();
            if ($res) {
                // Check attendance table if record exist or not........
                $userAttendance =   UserAttendance::where(['user_id' => $currentUser, 'date' => date('Y-m-d', strtotime($res->from_date))])->first();
                if ($userAttendance) {


                    //  ...........
                    //Update Schedule id if not exists
                    if (empty($userAttendance->user_schedule_id)) {
                        $userAttendance->user_schedule_id = $res->id;
                        $userAttendance->update();
                    }





                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $userAttendance->id;
                    $userPunchLog->save();



                    //.............








                } else {
                    // User Schedule found than insert this..........
                    $new = new UserAttendance();
                    $new->user_id = $currentUser;
                    $new->date = date('Y-m-d', strtotime($res->from_date));
                    $new->type = 1;
                    $new->user_schedule_id = $res->id;
                    $new->first_in = Carbon::now()->toTimeString();
                    $new->save();

                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $new->id;
                    $userPunchLog->save();
                }
            } else {
                $lastAttendance =  $this->checkLastAttendance();
                if ($lastAttendance && $lastAttendance->is_completed != 1) {
                    //  dd('Update records');



                    //Update Schedule id if not exists
                    if (empty($lastAttendance->user_schedule_id)) {
                        $lastScheduleData = $this->checkAndGetScheduleId($lastAttendance);

                        $lastAttendance->user_schedule_id = $lastScheduleData ? $lastScheduleData->id : null;
                        $lastAttendance->update();
                    }

                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $lastAttendance->id;
                    $userPunchLog->save();
                } else {
                    // User Schedule not found than insert this.......
                    $new = new UserAttendance();
                    $new->user_id = $currentUser;
                    $new->date = Carbon::today();;
                    $new->type = 1;
                    $new->first_in = Carbon::now()->toTimeString();
                    $new->save();

                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $new->id;
                    $userPunchLog->save();
                }
            }
        }





        //PunchOut
        if ($request->status == 0) {



            $currentUser = Auth::user()->id;
            $res = $this->checkScheduleExists();
            if ($res) {
                // Check attendance table if record exist or not........
                $userAttendance =   UserAttendance::where(['user_id' => $currentUser, 'date' => date('Y-m-d', strtotime($res->from_date))])->first();
                if ($userAttendance) {
                    //  ...........
                    //Update Schedule id if not exists
                    if (empty($userAttendance->user_schedule_id)) {
                        $userAttendance->user_schedule_id = $res->id;
                        $userAttendance->update();
                    }
                    //Update and close futher entry of that day......
                    if ($request->has('is_final_submit')) {
                        $userAttendance->is_completed = 1;
                        $userAttendance->update();
                    }

                    $userAttendance->last_out =  Carbon::now()->toTimeString();
                    $userAttendance->update();


                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $userAttendance->id;
                    $userPunchLog->save();



                    //.............


                } else {
                    // User Schedule found than insert this..........
                    $new = new UserAttendance();
                    $new->user_id = $currentUser;
                    $new->date = date('Y-m-d', strtotime($res->from_date));
                    $new->type = 1;
                    $new->user_schedule_id = $res->id;
                    $new->first_in = Carbon::now()->toTimeString();
                    $new->save();

                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $new->id;
                    $userPunchLog->save();
                }
            } else {
                $lastAttendance =  $this->checkLastAttendance();
                if ($lastAttendance->is_completed != 1) {

                    // Means Schedule Time is end but still working and click on out


                    //Update Schedule id if not exists
                    if (empty($lastAttendance->user_schedule_id)) {
                        $lastScheduleData = $this->checkAndGetScheduleId($lastAttendance);

                        $lastAttendance->user_schedule_id = $lastScheduleData ? $lastScheduleData->id : null;
                        $lastAttendance->update();
                    }
                    //Update and close futher entry of that day......
                    if ($request->has('is_final_submit')) {
                        $lastAttendance->is_completed = 1;
                        $lastAttendance->update();
                    }

                    $lastAttendance->last_out =  Carbon::now()->toTimeString();
                    $lastAttendance->update();


                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $lastAttendance->id;
                    $userPunchLog->save();



                    //.............




                } else {
                    // User Schedule not found than insert this.......
                    $new = new UserAttendance();
                    $new->user_id = $currentUser;
                    $new->date = Carbon::today();;
                    $new->type = 1;
                    $new->first_in = Carbon::now()->toTimeString();
                    $new->save();

                    $userPunchLog =  new UserPunchLog();
                    $userPunchLog->user_id = Auth::user()->id;
                    $userPunchLog->action_time = now();
                    $userPunchLog->description = $request->description;
                    $userPunchLog->status = $request->status;
                    $userPunchLog->user_attendance_id = $new->id;
                    $userPunchLog->save();
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data saved succssfully !',
        ]);
    } catch (\Throwable $e) {
        throw $e;
    }
}


function checkAndGetScheduleId($attendance)
{
    $currentUser = Auth::user()->id;
    $firstEntry = $attendance->first_in;
    $date = $attendance->date;
    $lastEntry = Carbon::now()->toTimeString();
    return $assignSechdule = UserSchedule::where('user_id', $currentUser)
        ->whereDate('from_date', $date)
        ->first();
}


function checkScheduleExists()
{
    $currentUser = Auth::user()->id;
    $currentDateTime = Carbon::now();
    return $assignSechdule = UserSchedule::where('user_id', $currentUser)
        ->where('from_date', '<=', $currentDateTime)
        ->where('to_date', '>=', $currentDateTime)
        ->first();
}

function checkLastAttendance()
{
    $currentUser = Auth::user()->id;
    return UserAttendance::where(['user_id' => $currentUser])->orderByDesc('id')->first();
}