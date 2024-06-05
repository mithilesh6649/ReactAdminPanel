<?php

//one..............................................................

Schema::create('user_expanses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->references('id')->on('users');
    $table->string('title')->nullable();
    $table->date('date')->nullable();
    $table->float('amount', 100)->nullable();
    $table->text('description')->nullable();
    $table->tinyInteger('state_id')->default(0);
    $table->integer('employer_id')->nullable()->index()->comment('State Updated By ID');
    $table->timestamps();
});

Schema::create('user_travel', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->references('id')->on('users');
    $table->string('travel_from')->nullable();
    $table->string('travel_to')->nullable();
    $table->decimal('travel_from_latitude', 11, 8)->nullable();
    $table->decimal('travel_from_longitude', 11, 8)->nullable();
    $table->decimal('travel_to_latitude', 11, 8)->nullable();
    $table->decimal('travel_to_longitude', 11, 8)->nullable();
    $table->date('date')->nullable();
    $table->string('break')->nullable();
    $table->tinyInteger('is_injured')->default(0);
    $table->float('amount', 100)->nullable();
    $table->text('description')->nullable();
    $table->tinyInteger('state_id')->default(0);
    $table->integer('employer_id')->nullable()->index()->comment('State Updated By ID');
    $table->timestamps();
});

Schema::create('user_expanse_attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_expanses_id')->references('id')->on('user_expanses');
    $table->string('image')->nullable();
    $table->timestamps();
});


protected $fillable = [
    'user_id',
    'title',
    'date',
    'amount',
    'description',
    'state_id',
    'employer_id',
];
//two....................................


<form id="addExpanseForm" method="POST" action="{{ route('employee.payroll.save') }}">
@csrf
  <div class="form-styling">
      <div class="form-group">
          <label class="form-label"
              for="form4Example1">Title</label>
          <input type="text" id="form4Example1"
              class="form-control" name="title" placeholder="Enter Title" />

      </div>

      <div class="form-group">
          <label class="form-label"
              for="amount">Cost</label>
          <input type="number" id="amount"
              class="form-control" required name="amount" placeholder="Enter cost" />

      </div>
      <div class="form-group">
          <div class="mt-2">
              <label class="form-label"
                  for="date">Date</label>
              <input id="date" name="date" class="form-control"
                  type="date" />
          </div>
      </div>
      <div class="form-group">
          <div class="mt-2">
              <label class="form-label"
                  for="description">Description</label>
              <textarea class="form-control"
                  id="description" rows="4"
                  placeholder="Description" name="description"></textarea>
          </div>
      </div>

      <div class="form-group">
          <div class="mt-2 attachment_styling">
              <label class="form-label"
                  for="attachment">Attachment</label>
              <input type="file" name="attachment[]" multiple class="form-control-file"
                  id="attachment">
          </div>
      </div>
      <div class="mt-3">
          <div class="submit_btn_styling">
              <button type="submit"
                  class="btn active ">Save</button>
              <button type="button" class="btn "
                  data-bs-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</form>


//Three.....................


$('#addExpanseForm').validate({
    ignore: [],
    debug: false,
    rules: {
        title: {
            required: true,
            noSpace: true
        },
        date: {
            required: true,
            remote: {
                type: "post",
                url: "{{ route('check_expanse_date') }}",
                data: {
                    "date": function() {
                        return $("#date").val();
                    },
                    "_token": "{{ csrf_token() }}",

                },
                dataFilter: function(result) {
                    var json = JSON.parse(result);
                    if (json.msg == 1) {
                        return "\"" + "Expanse Date already  exist" + "\"";
                    } else {
                        return 'true';
                    }
                }
            }
        },
        amount: {
            required: true,
        },
        description: {
            required: true,
        }
    },
    messages: {
        first_name: {
            required: "First name is required.",
        },
        email: {
            required: "Email is required",
            email_check: "Please enter a valid Email",
            remote: "Email already exists"
        },

    },
});
});



//Four........................

Route::get('employee/payroll/list', 'employeePayrollList')->name('employee.payroll.list');
Route::post('employee/payroll/save', 'employeePayrollSave')->name('employee.payroll.save');
Route::post('/check/expanse/date', 'checkExpanseDate')->name('check_expanse_date');


public function employeePayrollSave(Request $request)
{
    try {
        $expense = new UserExpanse();
        $expense->user_id = Auth::user()->id;
        $expense->title = $request->title;
        $expense->date = $request->date;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->save();
        return redirect()->route('employee.payroll.list')
            ->with('success_msg', 'Expanse created successfully.');
    } catch (\Throwable $e) {

        throw $e;
    }
}

public function checkExpanseDate(Request $request)
{
    $expanse = UserExpanse::where(["user_id" => Auth::user()->id, 'date' => $request->date])->get();

    if (count($expanse) > 0) {
        $res = 1;
        return response()->json(["msg" => $res]);
    } else {
        $res = 0;
        return response()->json(["msg" => $res]);
    }
}


//Five...............................



public function employeeTravelSave(Request $request)
{

    try {
        // Create a new travel record
        $travel = new UserTravel();
        $travel->user_id = Auth::user()->id;
        $travel->travel_from = $request->travel_from;
        $travel->travel_to = $request->travel_to;
        // $travel->travel_from_latitude = $request->travel_from_latitude;
        // $travel->travel_from_longitude = $request->travel_from_longitude;
        // $travel->travel_to_latitude = $request->travel_to_latitude;
        // $travel->travel_to_longitude = $request->travel_to_longitude;
        $travel->date = $request->travel_date;
        $travel->break = $request->break;
        $travel->is_injured = $request->is_injured ?? 0;
        // $travel->amount = $request->amount;
        $travel->description = $request->description;
        // Save the record to the database
        $travel->save();

        return redirect()->route('employee.payroll.list')
            ->with('success_msg', 'Travel Record created successfully.');
    } catch (\Throwable $e) {

        throw $e;
    }
}


//Six.......................................

<form  id="addTravelForm" method="post" action={{ route('employee.travel.save') }}>
@csrf
 <div class="form-styling">
     <div class="row g-3">
         <div class="col-md-6">
             <div class="position-relative">
                 <label for="travel_from"
                     class="form-label">From</label>
                 <input type="text" class="form-control"
                     id="travel_from" name="travel_from" required
                     placeholder="Search Location">
                 <div class="loaction_icon_styling_payroll">
                     <i
                         class="fa-solid fa-location-crosshairs"></i>
                 </div>
             </div>
         </div>
         <div class="col-md-6">
             <div class="position-relative">
                 <label for="travel_to"
                     class="form-label">To</label>
                 <input type="text" class="form-control"
                     id="travel_to" name="travel_to" required
                     placeholder="Search Location">
                 <div class="loaction_icon_styling_payroll">
                     <i
                         class="fa-solid fa-location-crosshairs"></i>
                 </div>
             </div>
         </div>
         <div class="col-md-8">
             <label class="form-label"
                 for="travel_date">Date</label>
             <input id="travel_date" class="form-control"
                 type="date" required name="travel_date" />
         </div>
         <div class="col-md-4">
             <label for="your-name"
                 class="form-label">Break:</label>
             <input type="text" class="form-control"
                 id="break" name="break"  
                 placeholder="1:00">
         </div>
     </div>
     <div class="my-3">
         <div class="col-12">
             <label class="form-label"
                 for="description">Description</label>
             <textarea class="form-control"
                 id="description" name="description" rows="4"
                 placeholder="Description"></textarea>
         </div>
     </div>

     <input type="checkbox" id="any-injury" name="is_injured"
        value="1">
     <label for="any-injury"> If any injury</label>

     <div class="my-3">

         <div class="col-12">
             <label class="form-label"
                 for="exampleFormControlFile1">Attachment</label>
             <input type="file" class="form-control-file"
                 id="exampleFormControlFile1">
         </div>
     </div>



     <div class="col-12">
         <div class="row">
             <div class="mt-3">
                 <div class="submit_btn_styling">
                     <button type="submit"
                         class="btn active ">Save</button>
                     <button type="button" class="btn "
                         data-bs-dismiss="modal">Close</button>
                 </div>

             </div>
         </div>
     </div>
 </div>
</div>
</form>


//Seven...................................



$('#addTravelForm').validate({
    ignore: [],
    debug: false,
    rules: {
        travel_from: {
            required: true,
            noSpace: true
        },
        date: {
            required: true,
            remote: {
                type: "post",
                url: "{{ route('check_expanse_date') }}",
                data: {
                    "date": function() {
                        return $("#date").val();
                    },
                    "_token": "{{ csrf_token() }}",

                },
                dataFilter: function(result) {
                    var json = JSON.parse(result);
                    if (json.msg == 1) {
                        return "\"" + "Expanse Date already  exist" + "\"";
                    } else {
                        return 'true';
                    }
                }
            }
        },
        amount: {
            required: true,
        },
        description: {
            required: true,
        }
    },
    messages: {
        first_name: {
            required: "First name is required.",
        },
        email: {
            required: "Email is required",
            email_check: "Please enter a valid Email",
            remote: "Email already exists"
        },

    },
});



});


//Eight...............

Route::post('employee/travel/save', 'employeeTravelSave')->name('employee.travel.save');