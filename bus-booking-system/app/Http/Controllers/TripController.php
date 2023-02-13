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
        $trips = array();
        $tripsStations = array();
        $journeysStations = array();
        $seatsForEachTrip = array();

        if(sizeof($journeys)){
            foreach($journeys as $journey) {
                $trip_id = $journey->trip_id;
                $trip = Trip::findOrFail($trip_id);
                array_push($trips, $trip);
                array_push($tripsStations, $trip->stations);
                $journeyStations = array();
                $start = array_search(request('from'), $trip->stations);
                for($i = $start; $i<sizeof($trip->stations); $i++){
                    array_push($journeyStations,$trip->stations[$i]);
                    if($trip->stations[$i]== $journey->to){
                        break;
                    }
                }
                array_push($journeysStations, $journeyStations);

                $match = ['trip_id' => $trip_id];
                $seats = Seat::where($match)->get();
                $availableSeats = array();
                foreach($seats as $seat){
                    if($seat->occupation==""){
                        array_push($availableSeats, $seat);
                    } else if(sizeof($seat->occupation)){
                        $size = sizeof($seat->occupation);
                        $indexOfFrom = array_search(request('from'), $trip->stations);
                        $indexOfTo = array_search(request('to'), $trip->stations);
                        $indexOfStartofSeat = array_search($seat->occupation[0], $trip->stations);
                        $indexOfEndofSeat = array_search($seat->occupation[$size-1], $trip->stations);
                        if($indexOfStartofSeat>=$indexOfTo || $indexOfEndofSeat <= $indexOfFrom){
                            array_push($availableSeats, $seat);
                        }
                    }
                }
                array_push($seatsForEachTrip, $availableSeats);

            }
            return view('journeys.show', ['journeys' => $journeys, 'trips' => $trips, 'journeysStations' => $journeysStations, 'seatsForEachTrip' => $seatsForEachTrip]);


        } else {
            return redirect('/')->with('mssg', 'No trips available');
        }

        

        // if(sizeof($journeys)){
        //     $trip_id = $journeys[0]->trip_id;
        //     $trip = Trip::findOrFail($trip_id);
        //     $tripStations = $trip->stations;
        //     $journeyStations = array();
        //     $start = array_search(request('from'), $tripStations);
        //     for($i = $start; $i<sizeof($tripStations); $i++){
        //         array_push($journeyStations,$tripStations[$i]);
        //         if($tripStations[$i]== $journeys[0]->to){
        //             break;
        //         }
        //     }
        //     $match = ['trip_id' => $trip_id];
        //     $seats = Seat::where($match)->get();
        //     $availableSeats = array();
        //     foreach($seats as $seat){
        //         if($seat->occupation==""){
        //             array_push($availableSeats, $seat);
        //         } else if(sizeof($seat->occupation)){
        //             $size = sizeof($seat->occupation);
        //             $indexOfFrom = array_search(request('from'), $tripStations);
        //             $indexOfTo = array_search(request('to'), $tripStations);
        //             $indexOfStartofSeat = array_search($seat->occupation[0], $tripStations);
        //             $indexOfEndofSeat = array_search($seat->occupation[$size-1], $tripStations);
        //             if($indexOfStartofSeat>=$indexOfTo || $indexOfEndofSeat <= $indexOfFrom){
        //                 array_push($availableSeats, $seat);
        //             }
        //         }

        //     }
        //     return view('journeys.show', ['journeys' => $journeys, 'trip' => $trip, 'stations' => $journeyStations, 'seats' => $availableSeats]);
        // } else {
        //     return redirect('/')->with('mssg', 'No trips available');
        // }
  }

    public function book($id) {
        $seat = Seat::findOrFail($id);
        $newStations = json_decode(request('stations'));
        $oldStations = $seat->occupation;
        $trip_id = $seat->trip_id;
        $trip = Trip::findOrFail($trip_id);
        $fullStations = $trip->stations;

        if($oldStations){

            $sizeNew = sizeof($newStations);
            $sizeOld = sizeof($oldStations);

            $indexFirstNew = array_search($newStations[0], $trip->stations);
            $indexLastNew = array_search($newStations[$sizeNew-1], $trip->stations);
            $indexFirstOld = array_search($oldStations[0], $trip->stations);
            $indexLastOld = array_search($oldStations[$sizeOld-1], $trip->stations);

            $newSeatOccupation = array();


            if($indexFirstNew >= $indexLastOld){
                foreach($oldStations as $station){
                    if(!in_array($station, $newSeatOccupation)){
                        array_push($newSeatOccupation, $station);
                    }
                }
                foreach($newStations as $station){
                    if(!in_array($station, $newSeatOccupation)){
                        array_push($newSeatOccupation, $station);
                    }
                }

            } else if($indexLastNew >= $indexFirstOld){
                foreach($newStations as $station){
                    if(!in_array($station, $newSeatOccupation)){
                        array_push($newSeatOccupation, $station);
                    }
                }
                foreach($oldStations as $station){
                    if(!in_array($station, $newSeatOccupation)){
                        array_push($newSeatOccupation, $station);
                    }
                }
            }

            $newSeatOccupation = json_encode($newSeatOccupation);

            Seat::where('id', $id)->update(['occupation'=>$newSeatOccupation]);

        } else {
            Seat::where('id', $id)->update(['occupation'=>request('stations')]);
            return redirect('/trips')->with('mssg', 'Seat reserved successfully!');
        }

        
        return redirect('/trips')->with('mssg', 'Seat reserved successfully!');
    }
}
