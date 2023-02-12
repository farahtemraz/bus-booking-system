@extends('layouts.app')

@section('content')
<div class="wrapper trip-details">
  <h1>Details for route</h1>
  @foreach($journeys as $journey)
    <p>From - {{ $journey->from }}</p>
    <p>To - {{ $journey->to }}</p>
    <p>Trip name - {{ $trip->name }}</p>
    <p>Trip Route: </p>
    @foreach($trip->stations as $station)
        {{ $station }} -
    @endforeach
    <p>Journey Route: </p>
    @foreach($stations as $station)
        {{ $station }} -
    @endforeach
    <p>Available Seats: </p>
    @foreach($seats as $seat)
        <form action="{{ route('trips.book', $seat->id) }}" method="POST">
        @csrf
        <input type="hidden" value={{json_encode($stations)}} name=stations>
        <button>{{ $seat->id }}</button>
    </form>
    @endforeach
  @endforeach
</div>
<a href="{{ route('trips.index') }}" class="back"><- Back to all trips</a>
@endsection