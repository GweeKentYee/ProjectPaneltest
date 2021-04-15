<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServerMonitorController extends Controller
{
    //
    public function disk_total()
{
        $disktotal = disk_total_space('/'); //DISK usage
        $disktotalsize = $disktotal / 1073741824;

        $diskfree  = disk_free_space('/');
        $used = $disktotal - $diskfree;

        $diskusedize = $used / 1073741824;
        $diskuse1   = round(100 - (($diskusedize / $disktotalsize) * 100));
        $diskuse = round(100 - ($diskuse1)) . '%';
        
    return view('servermonitor',compact('diskuse','disktotalsize','diskusedize'));
}
}
