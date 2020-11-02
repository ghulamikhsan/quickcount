<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;

class CalculatePointGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $history_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($history_id)
    {
        $this->history_id = $history_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $history_point = DB::table('history_points')->where('id', '=', $this->history_id)->first();
        $point = $history_point->self_point;
        $user = DB::table('users')->where('id', '=', $history_point->user_id)->first();
        $reff_code = $user->referral_code;

        DB::table('history_points')
            ->where('id', '=', $this->history_id)
            ->update([
                'up' => 1
            ]);
        
        
        while ($reff_code != null) {
            $referral = DB::table('users')->where('code', '=', $reff_code)->first;

            $check_point = DB::table('history_points')->where([
                                ['user_id', '=', $referral->id],
                                ['month', '=', date('m')],
                                ['year', '=', date('Y')],
                            ]);
            
            if ($check_point->count() > 0) {
                $check_point->update([
                    'group_point' => DB::raw('group_point + '.$point),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                DB::table('history_points')
                    ->insert([
                        'user_id' => $referral->id,
                        'self_point' => 0,
                        'group_point' => $point,
                        'month' => date('m'),
                        'year' => date('Y'),
                        'up' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $reff_code = $referral->code;
        }
    }
}
