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
  - in case an error `Vite manifest not found` is encountered, try running `npm run dev` in the terminal 

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

## Remarks

- The user has to be logged in in order to use the system (authentication)
- in case of a new user, he/she can register first in order to use the system

## Testing
