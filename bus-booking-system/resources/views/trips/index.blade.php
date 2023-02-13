
@extends('layouts.app')

@section('content')
<div class="wrapper trip-index">
    <h1>Search for your trip</h1>
    <form action="/trips" method="POST">
    @csrf
    <label for="type">From:</label>
    <br>
    <select name="from" id="from">
        <option value="" disabled selected hidden>Choose departure city</option>
        @foreach($cities as $city)
            <option value="{{$city->name}}">{{$city->name}}</option>
        @endforeach
    </select>
    <br>
    <label for="type">To:</label>
    <br>
    <select name="to" id="to">
        <option value="" disabled selected hidden>Choose arrival city</option>
        @foreach($cities as $city)
            <option value="{{$city->name}}">{{$city->name}}</option>
        @endforeach
    </select>
    <br> 
    <br>
    <input type="submit" value="Search">
    <p class="mssg"> {{session('mssg')}} </p>
    </form>
    <hr class="solid">
    <h1>Available Trips</h1>
    @foreach($trips as $trip)
    <div class="trip-item">
        <ul>
            <li><a class="list" href="/trips/{{ $trip->id }}">{{ $trip->name }}</a></li>
        </ul>
    </div>
  @endforeach
</div>

@endsection