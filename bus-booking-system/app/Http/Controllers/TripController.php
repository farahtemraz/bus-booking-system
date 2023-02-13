<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use App\Models\Journey;
use App\Models\Seat;
use App\Models\Reservation;
use App\Models\City;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{

    // Fetching all available trips to be displayed on the index page of trips
    public function index() {
        $trips = Trip::all();
        $cities = City::all();
        return view('trips.index', ['trips' => $trips, 'cities' => $cities]);
    }

    // Fetching details of individual trips
    public function show($id) {
        $trip = Trip::findOrFail($id);
        return view('trips.show', ['trip' => $trip]);
    }

    // Finding trips that contains the journey that user wants to book a seat for and showing availability of seats on it
    public function find() {

        if(request('from') == ""){
            return redirect('/trips')->with('mssg', 'Please specify your departure city');
        }
        if(request('to') == ""){
            return redirect('/trips')->with('mssg', 'Please specify your arrival city');
        }

        $query = ['from' => request('from'), 'to' => request('to')];

        $journeys = Journey::where($query)->get();
        $trips = array();
        $tripsStations = array();
        $journeysStations = array();
        $seatsForEachTrip = array();

        if(sizeof($journeys)){

            // Repeating the logic for all possible journeys since a journey can be acheivable via more than 1 trip
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

                // Finiding unoccupied seats for the specified journey

                $match = ['trip_id' => $trip_id];
                $seats = Seat::where($match)->get();
                $availableSeats = array();
                foreach($seats as $seat){
                    if($seat->occupation==""){

                        // Not occupied during any stations therefore add it to the 
                        // available seats array as it can be booked for this journey
                        array_push($availableSeats, $seat);

                    } else if(sizeof($seat->occupation)){

                        // The seat is occupied during some stations so we must check if the 
                        // stations that it's occupied during conflict with the stations of the 
                        // journey we want to book the seat for or not in order to add it as an 
                        // available seat for booking if no conflicts are found
                        // The check is based on the order of appearance of stations on the main trip route

                        $size = sizeof($seat->occupation);
                        $indexOfDepartureCity = array_search(request('from'), $trip->stations);
                        $indexOfArrivalCity = array_search(request('to'), $trip->stations);
                        $indexOfFirstStationOfSeat = array_search($seat->occupation[0], $trip->stations);
                        $indexOfLastStationOfSeat = array_search($seat->occupation[$size-1], $trip->stations);
                        if($indexOfFirstStationOfSeat>=$indexOfArrivalCity || $indexOfLastStationOfSeat <= $indexOfDepartureCity){
                            array_push($availableSeats, $seat);
                        }
                    }
                }
                array_push($seatsForEachTrip, $availableSeats);

            }
            return view('journeys.show', ['journeys' => $journeys, 'trips' => $trips, 'journeysStations' => $journeysStations, 'seatsForEachTrip' => $seatsForEachTrip]);


        } else {
            return redirect('/trips')->with('mssg', 'Sorry! No trips available for your desired journey.');

        }
    }

    // Booking a seat on a trip for a specific journey from station A to station B
    public function book($id) {
        //Seat to be booked
        $seat = Seat::findOrFail($id);

        //Stations that this seat will be occupied during
        $newStations = json_decode(request('stations'));

        //The stations that this seat is already occupied during
        $oldStations = $seat->occupation;
    
        $trip_id = $seat->trip_id;
        $trip = Trip::findOrFail($trip_id);

        //Obtaining the full stations route of the trip that this seat belongs to
        $fullStations = $trip->stations;

        if($oldStations){

            // the seat is already occupied during some stations of the trip

            $sizeNew = sizeof($newStations);
            $sizeOld = sizeof($oldStations);

            $indexFirstNewStations = array_search($newStations[0], $trip->stations);
            $indexLastNewStations = array_search($newStations[$sizeNew-1], $trip->stations);
            $indexFirstOldStations = array_search($oldStations[0], $trip->stations);
            $indexLastOldStations = array_search($oldStations[$sizeOld-1], $trip->stations);

            //The array that will contain the new stations where the seat will be occupied during after booking it for this journey
            $newSeatOccupation = array();

            // Determining the order of the stations based on the main trip's stations order

            if($indexFirstNewStations >= $indexLastOldStations){

                // Push old stations first then new stations to the final stations array

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

            } else if($indexLastNewStations >= $indexFirstOldStations){

                // Push new stations first then old stations to the final stations array

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

            // Updating occupation column of the db record for the seat to contain the new occupations array after booking
            Seat::where('id', $id)->update(['occupation'=>$newSeatOccupation]);

        } else {

            // the seat is free during all stations of the trip

            Seat::where('id', $id)->update(['occupation'=>request('stations')]);
        }

        $userId = Auth::id();

        // creating the new reservation record and adding it to the reservations table

        $reservation = new Reservation();
        
        $reservation->user_id = $userId;
        $reservation->seat_id = $id;
        $reservation->trip_id = $trip_id;
        $reservation->journey= $newStations;

        $reservation->save();
        
        
        return redirect('/trips')->with('mssg', 'Seat reserved successfully!');
    }
}
