<?php
namespace App\Http\Services;

use App\Models\leave;
use Carbon\Carbon;

class LeaveService {

 protected $user;

  public function construct($user)
  {
      $this->user = $user;
  }

  public function create($data)
  {
      $leave = new leave($data);
      $leave->save();
//      SyncEventWithGoogle::dispatch($leave ,$this->user);
      return $leave;

  }
    public function update($id,$data)
    {
        $leave = leave::find($id);
        $leave->fill($data);
        $leave->save();
        return $leave;

    }

  public function allLeave($filters){

      $leaveQuery = Leave::query();
      $leaveQuery->where('user_id',auth()->user()->id);
      if($filters['start']){
          $leaveQuery->where('start','>=',$filters['start']);
      }
      if($filters['end']){
          $leaveQuery->where('end','<=',$filters['end']);
      }
      $leaves = $leaveQuery->get();
      $data = [];
      foreach ($leaves as $leave) {
          $event = [
              'id' => $leave->id,
              'type' => $leave->type,
              'description' => $leave->description, // Include the description
              'start' => $leave->start,
              'end' => $leave->end,
              'allDay' => !$leave->is_all_day, // Modify as needed
          ];
          $data[] = $event;
      }
     return $data;
  }
}
?>
