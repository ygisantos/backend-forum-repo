<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Forum;
use App\Models\Liked;

class CreateLikedEntries extends Command
{
    protected $signature = 'create:liked-entries';
    protected $description = 'Create liked entries for existing users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::all();
        $forums = Forum::all();

        foreach ($users as $user) {
            foreach ($forums as $forum) {
                Liked::firstOrCreate(['user_id' => $user->id, 'forum_id' => $forum->id]);
            }
        }

        $this->info('Liked entries created for all existing users and forums.');
    }
}
