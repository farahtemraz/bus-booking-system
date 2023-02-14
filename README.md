# Bus Seats Reservation System

This project is a web development project of a system to reserve seats on busses for your desired trips.

**Motivation**:

This project was built to create an easy interface for users to be able to search and book seats for their desired trips smoothly and quickly without facing booking problems.

**Development stack**

The language used was PHP and the used stack included Laravel Framework and MySQL database. I installed XAMPP to have the needed environment for PHP and MySQL and used "phpMyAdmin" as the admin panel for MySQL database.

**Setting up the project**

- clone the project
- install composer dependencies -> `composer install`
- install NPM dependencies -> `npm install`
- create .env file and fill it with the following content -> `
- create an empty database for the application and call it `bus-booking-system` 
- connect to the database through the .env file configuration
- migrate the database to create the tables -> `php artisan migrate`
- import the database dump file provided in phpMyAdmin to obtain the data used locally for testing
- to run the project -> `php artisan serve`
  - in case of an error `Vite manifest not found` is encountered, try running `npm run dev` in the terminal 

**Database Structure**

***Tables***

- Users
  - contains all registered users on the system when they sign-up through the registration flow
  - columns: id, name, email, email_verified_at, password, remeber_token, created_at, updated_at
- Trips
  - contains the predefined trips that the system offers
  - each trip can contain multiple intermediate stops
  - columns: id, created_at, updated_at, name, from, to stations, date, time
  - the stations column contains a json string that can then be turned into an array that contains all the stops that the trip has including the start and end stops
- Journeys
  - a journey is the users actual desired trip, be it a full trip or a sub-trip of a larger predefined full trip
  - the start and end of the journey have to be within the stations of one of the predefined trips
  - columns: id, trip_id, from, to, created_at, updated_at
  - the trip_id column is a foreign keya from the primary key of the Trips table which is the id 
  - the trip_id links a journey to the trip that it is a subsidary of
- Seats
  - contains a record for each seat of each trip
  - since each trip has 12 seats only (1 bus), therefore there are 12 records for each trip
  - columns: id, trip_id, occupation, created_at, updated_at
  - like Journeys table, trip_id is a foreign key linking a seat to the trip that it is part of its bus
  - each seat is fixed to a certain trip via the trip_id field
  - occupation field contains a json string that can then be turned into an array that contains the stations at which this seat is currently booked
  - initially, all occupation values are empty indicating that all seats are free
- Cities
  - contains some Egyptian cities to specify the choices that the user can use to search for a trip
  - columns: id, created_at, updated_at, name
- Reservations
  - contains records of the successful reservation of seats
  - columns: id, user_id, seat_id, trip_id, journey, created_at, updated_at
  - user_id, seat_id and trip_id are all foreign keys that link the user who made the reservation to the seat he/she reserved and to the trip that this seat belongs to
  - the journey column indicates the journey that this user who made this reservation is going to (from where to where)
  
- Busses
  - Since each trip has one specific bus, this table relates a bus to a trip 
  - Columns: id, trip_id, created_at, updated_at

**Data**

***Trips***

We have 4 predefined trips:
- Cairo -> Asyut that passes by AlFayyum then by AlMinya
- Alexandria -> Suez that passes by Cairo then by AlFayyum
- Luxor -> Aswan that is a direct trip with no intermediate stops
- Giza -> AlMinya that passes by Cairo

***Journeys***

- From trip 1:
  - Cairo -> AlFayyum
  - Cairo -> AlMinya
  - Cairo -> Asyut
  - AlFayyum -> AlMinya
  - AlFayyum -> Asyut
  - AlMinya -> Asyut
- From trip 2:
  - Alexandria -> Cairo
  - Alexandria -> AlFayyum
  - Alexandria -> Suez
  - Cairo -> AlFayyum
  - Cairo -> Suez
  - AlFayyum -> Suez
- From trip 3: 
  - Luxor -> Aswan
- From trip 4:
  - Giza -> AlMinya
  - Giza -> Cairo
  - Cairo -> AlMinya 

***Cities***

- Alexandria
- Cairo
- AlMinya
- AlFayyum
- Asyut
- Suez
- Mansoura
- Tanta
- Luxor
- Aswan
- Giza

***Seats***

- 48 records, 12 for each trip representing a seat each

***Users and Reservations***

- initially empty and are filled upon user's interaction with the system

***Booking logic***

The main aim of the task was coming up with the implementation logic that would make seats availability visible to the user based on which trip/sub-trip this seat has already been booked for and comparing it with the trip/sub-trip we're searching for in order to know if it is possible to book this seat for this trip without confliciting with other booking operated in it or not.

The solution I came up with was as follows:

I created an "occupation" column for each seat that specifies the sub-part of the trip that this seat is related to that it is booked during. So for example, say we have a seat with ID 17 associated with trip 1, which is a trip that goes through Cairo, AlFayyum, AlMinya, Asyut. So, the full trip is represented as follows: ["Cairo", "AlFayyum", "AlMinya", "Asyut"]. Say that a user wanted to book a journey from Cairo to AlFayyum. So, a possible trip would be trip 1, the one mentioned above, as it has a sub trip starting at Cairo and ending at AlFayyum. Seat 17 initially has its occupation column value as an empty string, which indicates that it is free from the beginning of trip 1 till it's end (no booking performed on it yet). Hence, the user will find seat 17 as one of the booking options on that trip. Say he chooses seat 17 and books it. What happens during booking is that the occupation column of this seat gets updated with the journey of the user who have just booked it. In our running example, this would be ["Cairo","AlFayyum"]. Now, imagine another user is now searching for a trip from AlFayyum to Asyut. When quering the database, we will find that this sub-trip is part of the big trip from Cairo to Asyut where the user's journey would be from AlFayyum, passing by AlMinya then reaching Asyut. Since the result trip is also trip 1 like the previous query, this means that seat 17 is also one of the seats that should be presented to the user for booking since the seat is associated with the whole trip not just a sub-trip. Now, before saying that seat 17 is a possible booking option and presenting it to the user, we first have to make sure that this seat will be unoccupied during the sub-trip that the user is trying to book. This check is done as follows:

We have 3 arrays that are used to determine the availbility of a seat. An array of the full route of the big trip, an array of the route of stations of that trip where this seat is occupied during so far and an array of the route of the stations that the user is currently searching for seats for. In our running example, those would be ["Cairo","AlFayyum", "AlMinya", "Asyut"], ["Cairo", "AlFayyum"], ["AlFayyum","AlMinya","Asyut"] respectively. We know that the order of the trip must be enforced, meaning that we should follow the main trip's route for any sub-routes and those sub-routes have to come in the order they are present at in the main route. So, we can't say that there is a sub-trip from trip 1 that goes from AlMinya to AlFayyum for example as the order is not enforced. I used this fact to check for seat avaialbility by looking at the current occupation array of the seat and the array of the route that should be added. Then, I would see, using the indicies of occurences of each city in the main trip route, which city comes before the other. So, in our example, we are trting to book this seat for  ["AlFayyum","AlMinya","Asyut"], so we have to check that this seat is not busy during any of these stations. So, we look at the current occupations array ["Cairo", "AlFayyum"]. If the first element of the new array comes after the last element of the current occupation array (or is equal to it), then this route could be added and would cause no conflicts. Similiarly, if the last element of the current occupations array comes before the first element of the new array, then we can add this route to this seat with no conflicts. In our example, "AlFayyum" (first element of the new array) is the same as the last element of the current occupations array, so the route can be added to the seat and the new occupations array would be ["Cairo", "AlFayyum", "AlMinya","Asyut"] since this seat is now booked by a user from Cairo to AlFayyum then this user will leave and another user will get on that seat from AlFayyum to Asyut. Hence, with this new occupations array, if we try to book this seat for any other route in trip 1, it will not be available as it is busy during the full route now as shown by the new occupation array.

## Some test cases to try
- Test case 1: (all seats are still empty)
  - search for a trip from Alexandria to Cairo -> o/p = seats in Alexandria-Suez trip
  - pick a seat to book
  - search for another trip from Cairo to Suez -> o/p seats in Alexandria-Suez trip AND the same seat that was booked for Alexandria-Cairo trip will also be available to be booked again
  - book it again
  - now try to search for Alexandria-Suez 
  - the previously booked seat will no longer be available as it is busy during all stations of the trip due to the previous 2 bookings, you will have to choose another seat to book on this trip (if available)

## Remarks

- The user has to be logged in in order to use the system (authentication)
- in case of a new user, he/she can register first in order to use the system

## Testing and Screenshots

***Login Page***


![Login](/Screenshot/Login.png)

***Registration Page***

![Registartion](/Screenshot/Registartion.png)

***Invalid credentials***

![Invalid](/Screenshot/InvalidCredentials.png)

***Home page***

![Home](/Screenshot/Home.png)

***Trips page***

Here the user can either view a list of all available full trips on the system or search the system for a specific journey froma certain city to another city and see if there is a trip that covers this journey

![Trips](/Screenshot/Trips.png)

***Trips details page***

The user is redirected here when he/she clicks on one of the trips listed on the Trips page shown above. They can see full trip details here

![Details](/Screenshot/TripDetails.png)

***Cities dropdown***

![Cities](/Screenshot/CitiesDropDown.png)

***Result of searching for a trip from Cairo to AlFayyum***

Here we can see the result of the query. We have 2 trips in the system that can take a passnenger from Cairo to AlFayyum. The user can see details of the trip as well as the available seats for booking here.

![Result](/Screenshot/SearchResult.png)

***Booking***

To book a seat, the user clicks on the seat number and then is redirected here and shown a success message that the seat was successfully reserved to him/her.

![Book](/Screenshot/SeatBooked.png)

***Full trip***

Here we see the output when we search for a certain journey and this journey is full booked (no seats avaiable to be reserved)

![FullTrip](/Screenshot/FullTrip.png)

***Unavailable trips***

Here we see the output when the user searches for a journey that is not provided by the system

![NoTrips](/Screenshot/NoTrips.png)

***Data Validation***

Here we see the error message shown when the user tries to search for a journey without specifying either departure city or arrival city or both

![Error](/Screenshot/BlankDepature.png)
![Error](/Screenshot/ErrorBlankDeparture.png)

***Logout***

![Logout](/Screenshot/Logout.png)

***Intermediate trips***

Here we see an example where the user first searches for "Giza-Cairo" and gets the result. The result is a sub-trip of Giza-AlMinya trip. He decides to book seat 37. Next, he searches for "Cairo-AlMinya", which happens to be another sub-trip of "Giza-AlMinya" and he finds that seat 37 also available because it was only book for Giza-Cairo, after that it is free again that's why it can be re-booked by another person for "Cairo-AlMinya"

![Search](/Screenshot/Search3.png)
![Search](/Screenshot/SearchResult2.png)
![Search](/Screenshot/sub-trip.png)
![Search](/Screenshot/sub-trip-res.png)

***Occupied Seats***

Here we tested searching for a "Luxor-Aswan" journey. We got the output where we can book seat 25, and we did book it. When we queried the same route again, we no longer see seat 25 as it is now occupied.

![Occupied](/Screenshot/Luxor-Aswan1.png)
![Occupied](/Screenshot/Luxor-Aswan2.png)

**Remarks**

- Since this project was mainly a backend task, minimal effort was dedicated to the UI and hence future work could include enhancing the UI.
