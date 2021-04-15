@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>Server Monitoring</h3>
            <br>
            <span style = "font-size:15px;">Occupied Disk Space</span>
            <div class="progress progress-micro mb-10">
              <div class="progress-bar bg-indigo-400" style="width: {{$diskuse}}">
                <span class="sr-only">{{$diskuse}}</span>
              </div>
            </div>
            <span class="pull-right">{{round($diskusedize,2)}} GB /
            {{round($disktotalsize,2)}} GB ({{$diskuse}})</span>       
          </div>
    </div>

</div>
@endsection





