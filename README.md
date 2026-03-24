# Hotel Booking Engine – Search Implementation

## Overview

This project implements a **hotel booking search engine** using Laravel (latest version).
The focus is on **room availability and pricing search**, including discounts and meal plans.

 Note: Booking and payment flows are **intentionally excluded** as per assignment scope.

---

##  Features

###  Search Functionality

* Search by **Check-in and Check-out dates**
* Validate:

  * No past dates
  * Check-out > Check-in
* Supports **guest count (max 3 adults)**

---

### Room Availability

* 2 Room Types:

  * Standard Room
  * Deluxe Room
* Each room has **5 units**
* Shows:

  *  Available rooms count
  *  Sold Out status

---

###  Pricing Engine

* Dynamic pricing based on:

  * Number of nights
  * Number of guests (tier-based pricing)
* Supports:

  * Room Only
  * Breakfast Included

---

###  Discounts

* **Last Minute Discount**

  * Applied if check-in within 2 days
* **Long Stay Discount**

  * Applied if stay ≥ 3 nights

---

###  UI Features

* Modern booking-style search bar
* Date selection with validation
* Dynamic room listing
* Meal plan selection
* Sticky **Stay Summary** panel
* AJAX-based search (no page reload)

---

##  Architecture

```
Controller → FormRequest → Service → Response
```

###  Key Components

* **SearchRequest**

  * Handles validation logic

* **SearchService**

  * Handles:

    * Availability calculation
    * Pricing logic
    * Discount application

* **Models**

  * Room
  * RoomInventory

---

##  API

###  Endpoint

```
GET /api/search
```

###  Parameters

| Param     | Type | Description            |
| --------- | ---- | ---------------------- |
| check_in  | date | Check-in date          |
| check_out | date | Check-out date         |
| guests    | int  | Number of adults (1–3) |

---

###  Sample Response

```json
[
  {
    "room_type": "Standard Room",
    "availability": {
      "status": "available",
      "rooms_left": 3
    },
    "meal_plans": [
      {
        "type": "room_only",
        "original_price": 3000,
        "final_price": 2850,
        "discount": 5
      },
      {
        "type": "breakfast",
        "original_price": 4200,
        "final_price": 3990,
        "discount": 5
      }
    ]
  }
]
```

---

##  Database Design

### Rooms Table

* id
* name (Standard / Deluxe)

### Room Inventory Table

* id
* room_id
* date
* available_rooms
* price

---

##  Setup Instructions

```bash
git clone <repo-url>
cd project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

##  Assumptions

* Maximum **3 adults per room**
* Pricing is **guest-based (tier pricing)**
* Breakfast cost is added per guest per night
* Inventory is managed per day

---

##  Out of Scope

* Booking confirmation
* Payment integration
* User authentication

---

##  Key Highlights

* Clean architecture (Service + Request pattern)
* Optimized queries using eager loading
* Dynamic UI with AJAX
* Scalable pricing design

---

##  Future Improvements

* Add caching for search performance
* Introduce pricing rules table
* Add child pricing
* Real-time availability updates

---

##  Author

Akram Desai
