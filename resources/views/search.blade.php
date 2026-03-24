<!DOCTYPE html>
<html>

<head>
    <title>Hotel Search UI</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
        }

        .box {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-left: 20%;
            margin-right: 20%;
        }

        button {
            border: 1px solid #ccc;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            background: #fff;
        }

        button:hover {
            background: #f5f5f5;
        }

        .label {
            font-size: 10px;
            color: #777;
        }

        .value {
            font-size: 14px;
        }

        .counter button {
            width: 25px;
            height: 25px;
        }
    </style>
</head>

<body>
    <div class="box">
        <div style="position: relative;">
        <button id="arrivalBtn">
            <div>
                <div class="label">Check-in</div>
                <div class="value" id="arrivalText"></div>
            </div>
        </button>
         <!-- Hidden Inputs -->
        <input type="date" id="arrivalDateInput" name="check_in" style="position:absolute; opacity:0; pointer-events:none; bottom:0; left:2%;"  >
       
        </div>
        <!-- Check-out -->
         <div style="position: relative;">
        <button id="departureBtn">
            <div>
                <div class="label">Check-out</div>
                <div class="value" id="departureText"></div>
            </div>
        </button>
         <input type="date" id="departureDateInput" name="check_out" style="position:absolute; opacity:0; pointer-events:none; bottom:0; left:2%;"  >
         </div>
        <!-- Adults -->
        <div>
            <button id="decreaseGuest">-</button>
            <span id="guestCount">1</span>
            <button id="increaseGuest">+</button>
        </div>

        <button id="searchAction">Search</button>

       

    </div>
    <div id="bookingSummary" style="display:none; position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:1px solid #ccc; padding:15px; box-shadow:0 -2px 10px rgba(0,0,0,0.1);">

        <div style="display:flex; justify-content:space-between; align-items:center;">

            <div>
                <h4>Stay Summary</h4>
                <p id="summaryRoom"></p>
                <p id="summaryDetails"></p>
                <p id="summaryPrice" style="font-weight:bold;"></p>
            </div>

            <div>
                <button id="clearBtn">Clear</button>
                <button>Book Now</button>
            </div>

        </div>

    </div>
    <div style="padding-bottom:120px;">

        <h1 id="pageTitle"></h1>
        <p id="pageSubTitle"></p>
        <div id="roomResults"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const arrivalBtn = document.getElementById("arrivalBtn");
            const departureBtn = document.getElementById("departureBtn");

            const arrivalText = document.getElementById("arrivalText");
            const departureText = document.getElementById("departureText");

            const arrivalInput = document.getElementById("arrivalDateInput");
            const departureInput = document.getElementById("departureDateInput");

            const increaseGuest = document.getElementById("increaseGuest");
            const decreaseGuest = document.getElementById("decreaseGuest");
            const guestCount = document.getElementById("guestCount");

            function formatDisplay(date) {
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatValue(date) {
                return date.toISOString().split('T')[0];
            }

            let today = new Date();

            let arrivalDate = new Date(today);
            let departureDate = new Date(today);
            departureDate.setDate(departureDate.getDate() + 1);


            arrivalInput.value = formatValue(arrivalDate);
            departureInput.value = formatValue(departureDate);

            arrivalInput.min = formatValue(today);
            departureInput.min = formatValue(departureDate);

            arrivalText.innerText = formatDisplay(arrivalDate);
            departureText.innerText = formatDisplay(departureDate);


            arrivalBtn.addEventListener("click", () => {
                arrivalInput.showPicker();
            });

            departureBtn.addEventListener("click", () => {
                departureInput.showPicker();
            });


            arrivalInput.addEventListener("change", function() {

                arrivalDate = new Date(this.value);

                let nextDay = new Date(arrivalDate);
                nextDay.setDate(nextDay.getDate() + 1);

                departureDate = nextDay;

                departureInput.min = formatValue(nextDay);
                departureInput.value = formatValue(nextDay);

                arrivalText.innerText = formatDisplay(arrivalDate);
                departureText.innerText = formatDisplay(nextDay);
            });


            departureInput.addEventListener("change", function() {

                let selected = new Date(this.value);

                if (selected <= arrivalDate) {
                    selected.setDate(arrivalDate.getDate() + 1);
                    this.value = formatValue(selected);
                }

                departureDate = new Date(this.value);

                departureText.innerText = formatDisplay(departureDate);
            });


            let guests = 1;

            increaseGuest.addEventListener("click", () => {
                if (guests < 3) {
                    guests++;
                    guestCount.innerText = guests;
                }
            });

            decreaseGuest.addEventListener("click", () => {
                if (guests > 1) {
                    guests--;
                    guestCount.innerText = guests;
                }
            });


            document.getElementById("searchAction").addEventListener("click", () => {

                console.log({
                    check_in: arrivalInput.value,
                    check_out: departureInput.value,
                    guests: guests
                });

                fetch(`/search?check_in=${arrivalInput.value}&check_out=${departureInput.value}&guests=${guests}`)
                    .then(res => res.json())
                    .then(data => {
                        updateSlogan(arrivalInput.value, departureInput.value, guests)
                        renderRooms(data);
                    })
                    .catch(err => console.error(err));
            });

          

            function renderRooms(data) {

                let container = document.getElementById("roomResults");

                container.innerHTML = "";

                if (!data.length) {
                    container.innerHTML = "<p>No rooms found</p>";
                    return;
                }

                data.forEach(room => {

                    let html = `
              <div class="room-card" style="border:1px solid #ddd; border-radius:10px; margin-bottom:20px; overflow:hidden;">
        
                  <div style="display:flex;">
            
            <!-- Image -->
            <div style="width:40%;">
                <img src="https://zotel-stay-buddy-assignment.lovable.app/assets/room-standard-DNGELkrD.jpg" 
                     style="width:75%; height:75%; object-fit:cover;">
            </div>

            <!-- Content -->
            <div style="padding:15px; width:60%;">

                <h3>${room.room_type}</h3>

                ${
                    room.availability.status === 'sold_out'
                    ? `<p style="color:red;">Sold Out</p>`
                    : `
                        <p style="color:green;">${room.availability.rooms_left} rooms left</p>

                        ${room.meal_plans.map(plan => `
                            <div style="margin-top:10px; border-top:1px solid #eee; padding-top:10px;">
                                
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    
                                    <div>
                                        <strong>
                                            ${plan.type === 'room_only' ? 'Room Only' : 'With Breakfast'}
                                        </strong>
                                        
                                        <div style="font-size:12px; color:gray;">
                                            <span style="color:orange;">-${plan.discount}%</span>
                                            <span style="text-decoration:line-through;">
                                                ₹${plan.original_price}
                                            </span>
                                        </div>
                                    </div>

                                    <div style="text-align:right;">
                                        <div style="font-size:18px; font-weight:bold;">
                                            ₹${plan.final_price}
                                        </div>
                                        <button style="margin-top:5px;" onclick="selectRoom('${room.room_type}','${plan.type}',${plan.final_price})">Select</button>
                                        
                                    </div>
                                </div>
                            </div>`).join('')}`}</div></div></div>`;

                    document.getElementById("roomResults").innerHTML += html;
                });

            }

            function updateSlogan(checkIn, checkOut, guests) {

                let start = new Date(checkIn);
                let end = new Date(checkOut);

                let nights = (end - start) / (1000 * 60 * 60 * 24);

                //  Guest text
                let guestText = guests == 1 ? "1 adult" : guests + " adults";

                //  Discount logic
                let dicount = 0;
                let discountText = "";
                let today = new Date();
                let diffDays = Math.ceil((start - today) / (1000 * 60 * 60 * 24));

                if (nights >= 3) {
                    dicount += 10; // "10% long stay deal";
                }
                if (diffDays <= 2) {
                    dicount += 5; // "5% last minute deal";
                }
                if (dicount >= 10) discountText = dicount + "% long stay deal";
                else discountText = dicount + "% last minute deal";
                // Final text
                document.getElementById("pageTitle").innerText =
                    "Select your stay at Zotel Demo Property.";

                document.getElementById("pageSubTitle").innerText =
                    `${nights} night${nights > 1 ? 's' : ''} · ${guestText}` +
                    (discountText ? ` · ${discountText}` : '');
            }



            document.getElementById("clearBtn").addEventListener("click", function() {
                const popup = document.getElementById("bookingSummary");

                popup.style.display = "none";

                // optional: reset content
                document.getElementById("summaryRoom").innerText = "";
                document.getElementById("summaryDetails").innerText = "";
                document.getElementById("summaryPrice").innerText = "";
            });

        });

          function selectRoom(roomType, mealPlan, price) {

                const checkIn = document.getElementById("arrivalDateInput").value;
                const checkOut = document.getElementById("departureDateInput").value;
                const guests = document.getElementById("guestCount").innerText;

                let start = new Date(checkIn);
                let end = new Date(checkOut);
                let nights = (end - start) / (1000 * 60 * 60 * 24);

                //  Update UI
                document.getElementById("summaryRoom").innerText =
                    `${roomType} · ${mealPlan === 'room_only' ? 'Room Only' : 'With Breakfast'}`;

                document.getElementById("summaryDetails").innerText =
                    `${checkIn} to ${checkOut} · ${guests} adult · ${nights} nights`;

                document.getElementById("summaryPrice").innerText =
                    `₹${price}`;

                //  Show popup
                document.getElementById("bookingSummary").style.display = "block";
            }
    </script>
</body>

</html>