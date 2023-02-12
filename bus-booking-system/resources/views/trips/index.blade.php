
@extends('layouts.app')

@section('content')
<div class="wrapper pizza-index">
  <h1>Available Trips</h1>
  <br>
  @foreach($trips as $trip)
    <div class="trip-item">
      <h4><a href="/trips/{{ $trip->id }}">{{ $trip->name }}</a></h4>
    </div>
  @endforeach
    <hr class="solid">
    <form action="/trips" method="POST">
    @csrf
    <label for="type">From:</label>
    <br>
    <select name="from" id="from">
      <option value="Cairo">Cairo</option>
      <option value="Asyut">Asyut</option>
      <option value="AlFayyum">AlFayyum</option>
      <option value="AlMinya">AlMinya</option>
      <option value="Alexandria">Alexandria</option>
      <option value="Suez">Suez</option>
      <option value="Port-Said">Port-Said</option>
    </select>
    <br>
    <label for="type">To:</label>
    <br>
    <select name="to" id="to">
      <option value="Cairo">Cairo</option>
      <option value="Asyut">Asyut</option>
      <option value="AlFayyum">AlFayyum</option>
      <option value="AlMinya">AlMinya</option>
      <option value="Alexandria">Alexandria</option>
      <option value="Suez">Suez</option>
      <option value="Port-Said">Port-Said</option>

    </select>
    <br> 
    <br>
    <input type="submit" value="Search">
    <p class="mssg"> {{session('mssg')}} </p>
  </form>

</div>

@endsection