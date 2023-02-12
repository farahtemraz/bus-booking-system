<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use App\Models\Journey;
use App\Models\Seat;

use Illuminate\Http\Request;

class TripController extends Controller
{

    public function index() {
        $trips = Trip::all();
        return view('trips.index', [
        'trips' => $trips,
        ]);
  }

    public function create() {
        return view('trips.create');
  }

      public function store() {

       $trip = new Trip();
       $trip->name = request('name');
       $trip->stations = request('stations');

       $trip->save();


    return redirect('/')->with('mssg', 'Thanks for creating this trip');
  }

    public function show($id) {
        $trip = Trip::findOrFail($id);
        return view('trips.show', ['trip' => $trip]);
    }

    public function find() {

        $matchThese = ['from' => request('from'), 'to' => request('to')];

        $journeys = Journey::where($matchThese)->get();

        if(sizeof($journeys)){
            $trip_id = $journeys[0]->trip_id;
            $trip = Trip::findOrFail($trip_id);
            $tripStations = $trip->stations;
            $journeyStations = array();
            $start = array_search(request('from'), $tripStations);
            for($i = $start; $i<sizeof($tripStations); $i++){
                array_push($journeyStations,$tripStations[$i]);
                if($tripStations[$i]== $journeys[0]->to){
                    break;
                }
            }
            $match = ['trip_id' => $trip_id];
            $seats = Seat::where($match)->get();
            $availableSeats = array();
            foreach($seats as $seat){
                if($seat->occupation==""){
                    array_push($availableSeats, $seat);
                } else if(sizeof($seat->occupation)){
                    $size = sizeof($seat->occupation);
                    $indexOfFrom = array_search(request('from'), $tripStations);
                    $indexOfTo = array_search(request('to'), $tripStations);
                    $indexOfStartofSeat = array_search($seat->occupation[0], $tripStations);
                    $indexOfEndofSeat = array_search($seat->occupation[$size-1], $tripStations);
                    if($indexOfStartofSeat>=$indexOfTo || $indexOfEndofSeat <= $indexOfFrom){
                        array_push($availableSeats, $seat);
                    }
                }

            }
            return view('journeys.show', ['journeys' => $journeys, 'trip' => $trip, 'stations' => $journeyStations, 'seats' => $availableSeats]);
        } else {
            return redirect('/')->with('mssg', 'No trips available');
        }
  }

    public function book($id) {
        $seat = Seat::where('id', $id);
        // $trip_id = $seat->trip_id;
        Seat::where('id', $id)->update(['occupation'=>request('stations')]);
        // $trip = Trip::where('id', $trip_id);
        return redirect('/trips')->with('mssg', 'Seat reserved successfully!');
    }
}
