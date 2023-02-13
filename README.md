# Bus Seats Reservation System

This project is a web development project of a system to reserve seats on busses for your desired trips.

# Motivation:

This project was built to create an easy interface for users to be able to search and book seats for their desired trips smoothly and quickly without facing booking problems.

# Development stack

The language used was PHP and the used stack included Laravel Framework and MySQL database. I installed XAMPP to have the needed environment for PHP and MySQL and used "phpMyAdmin" as the admin panel for MySQL database.

# Setting up the project

- clone the project
- install composer dependencies -> `composer install`
- install NPM dependencies -> `npm install`
- create .env file and fill it with the following content -> `
- create an empty database for the application and call it `bus-booking-system` 
- connect to the database through the .env file configuration
- migrate the database to create the tables -> `php artisan migrate`
- import the database dump file provided in phpMyAdmin to obtain the data used locally for testing
