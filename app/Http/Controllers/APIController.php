<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Thana;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helper\Array\Flatten;
use App\Helper\Cron\RankBonus;
use App\Models\BusinessSetting;
use App\Helper\Cron\SetUserRank;
use App\Helper\Cron\ExpireMembers;
use Illuminate\Support\Collection;
use App\Helper\Cron\DailyBonusCron;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helper\Cron\FounderBonusCron;
use App\Helper\Ui\FlashMessageGenerator;

class APIController extends Controller
{

  
}
