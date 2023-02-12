@extends('layouts.app')

@section('content')
<div class="wrapper trip-details">
  <h1>Details for {{ $trip->name }}</h1>
  <p class="from">From - {{ $trip->from }}</p>
  <p class="to">To - {{ $trip->to }}</p>
  <p class="to">Available Seats - {{ $trip->seats }}</p>
  <p class="stations">Stations:</p>
  <ul>
    @foreach( $trip->stations as $station )
        <li>{{$station}}</li>
    @endforeach
  </ul>
</div>
<a href="{{ route('trips.index') }}" class="back"><- Back to all trips</a>
@endsection