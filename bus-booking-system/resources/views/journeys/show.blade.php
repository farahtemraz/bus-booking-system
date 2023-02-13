@extends('layouts.app')

@section('content')
<div class="wrapper trip-details">
  <h1>Available trips for your desired journey from {{$journeys[0]->from}} to {{$journeys[0]->to}}</h1>
  @for($i = 0; $i<sizeof($journeys); $i++)
  <p>{{$i+1}} - </p>
    <p>Trip that contains this route ----> {{ $trips[$i]->name }}</p>
    <p>Trip Route: </p>
    @foreach($trips[$i]->stations as $station)
        {{ $station }} -
    @endforeach
    <p>Your journey's Route: </p>
    @foreach($journeysStations[$i] as $station)
        {{ $station }} -
    @endforeach
    <p>Choose your seat from one of the available seats below: </p>
    <div class="flex">
    @if(sizeof($seatsForEachTrip[$i]))
        @foreach($seatsForEachTrip[$i] as $seat)
            <form action="{{ route('trips.book', $seat->id) }}" method="POST">
            @csrf
            <input type="hidden" value={{json_encode($journeysStations[$i])}} name=stations>
            <button>{{ $seat->id }}</button>
        </form>
        @endforeach
    @else
        <p>There are currently no available seats on this trip</p>
    @endif
    </div>
  @endfor
</div>
<a href="{{ route('trips.index') }}" class="back"><- Back to all trips</a>
@endsection