
@extends('layouts.app')

@section('content')
<div class="wrapper create-trip">
  <h1>Create a new trip</h1>
  <form action="/trips" method="POST">
    @csrf
    <label for="name">Trip name:</label>
    <input type="text" name="name" id="name" required>
    <label for="type">From:</label>
    <select name="stations" id="stations">
      <option value="margarita">Margarita</option>
      <option value="hawaiian">Hawaiian</option>
      <option value="veg supreme">Veg Supreme</option>
      <option value="volcano">Volcano</option>
    </select>
    <label for="type">To:</label>
    <select name="stations" id="stations">
      <option value="margarita">Margarita</option>
      <option value="hawaiian">Hawaiian</option>
      <option value="veg supreme">Veg Supreme</option>
      <option value="volcano">Volcano</option>
    </select>
    <br>
    <button>Add stations</button>
    <label for="type">Stations:</label>
    <select name="stations" id="stations">
      <option value="margarita">Margarita</option>
      <option value="hawaiian">Hawaiian</option>
      <option value="veg supreme">Veg Supreme</option>
      <option value="volcano">Volcano</option>
    </select>
    <input type="submit" value="Create Trip">
  </form>
</div>
@endsection