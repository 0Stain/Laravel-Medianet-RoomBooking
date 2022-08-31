# Project Stage MEDIANET

## Description
This project is a room booking management system developed for Medianet. The purpose of this web app is to allow Medianet employees to easily check the availability and book one of the boxes for a meeting without overlapping with other employees.

## Pre-installation
* PHP version 8



## Installation
* Clone the repositore using **git clone https://github.com/0Stain/Laravel-Medianet-RoomBooking.git**
* Copy **.env.example**, rename it **.env** and change to your variables
* Run these commands
``` bash
composer update
```
``` bash
php artisan key:generate
```
``` bash
php artisan migrate
```
``` bash
php artisan db:seed
```

## Login credentials

* **Admin :**  admin@admin.com | password
* **User :**  user@user.com | password

