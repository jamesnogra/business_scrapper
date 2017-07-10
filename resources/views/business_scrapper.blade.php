<!DOCTYPE html>
<html>
    <head>
        <title>Business Scrapper</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    </head>

    <body>

        <div class="w3-container w3-teal">
            <div class="w3-row">
                <div class="w3-col" style="width:10%;padding-top:7px;"><p>Search for:</p></div>
                <div class="w3-col" style="width:70%;"><p><input class="w3-input" type="text" id="search-for" value="Women Spa"></p></div>
                <div class="w3-col" style="width:10%;">
                    <p>
                        <select class="w3-input" id="us-state">
                            <option value="">Select</option>
                            @foreach ($us_places as $i)
                                <option value="{{$i}}">{{$i}}</option>
                            @endforeach
                        </select>
                    </p>
                </div>
                <div class="w3-col" style="width:10%;"><p><button id="search-btn" class="w3-button w3-black"><i class="fa fa-search"></i> Search</button></p></div>
            </div>
        </div>

        <div class="w3-container">
            <table class="w3-table" id="table-results" style="font-size:10px;">
                <tr>
                    <th>Place ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Website</th>
                </tr>
            </table>
        </div>

    </body>

    <script>

        $(document).ready(function() {

            var all_places = [<?php echo '"'.implode('","', $us_places).'"' ?>];

            $('#search-btn').click(function() {
                var query = $('#search-for').val() + " " + $('#us-state').val();
                var api_key = 'AIzaSyCcgDQxvo7XbkNPtuVp98RLH0ApPt21CkI';
                var place_id, place_data;
                for (var a=0; a<all_places.length; a++) {
                    query = $('#search-for').val() + " " + all_places[a];
                    console.log("Searching for: " + query);
                    searchAndInsert(query, api_key);
                }
            });

        });

        function searchAndInsert(query, api_key) {
            $.get("/get-places?query="+query+"&key="+api_key, { } )
                .done(function(data) {
                    //iterate through places_id
                    for (var x=0; x<data.length; x++) {
                        place_id = data[x]['place_id'];
                        $.get("/get-place-info?place_id="+place_id+"&key="+api_key, { } )
                            .done(function(place_info) {
                                place_data = JSON.parse(place_info);
                                //then save to DB
                                $.post("/save-place", {
                                    _token: '{{ csrf_token() }}',
                                    place_id: place_data['place_id'],
                                    name: place_data['name'],
                                    address: place_data['formatted_address'],
                                    phone: (place_data['formatted_phone_number']) ? place_data['formatted_phone_number'] : '',
                                    website: (place_data['website']) ? place_data['website'] : '',
                                    opening_hours: (place_data['opening_hours'] && place_data['opening_hours']['weekday_text']) ? place_data['opening_hours']['weekday_text'].toString() : '',
                                    lat: place_data['geometry']['location']['lat'],
                                    lng: place_data['geometry']['location']['lng'],
                                }, function(data, status){
                                    if (data['status'] == 'OK') {
                                        $('#table-results').append(formatAsTable(place_data['place_id'], place_data['name'], place_data['formatted_address'], place_data['formatted_phone_number'], place_data['website']));
                                    }
                                });
                            }
                        );
                    }
                }
            );
        }

        function formatAsTable(place_id, name, address, phone, website) {
            return '<tr>\
                        <td>'+place_id+'</td>\
                        <td>'+name+'</td>\
                        <td>'+address+'</td>\
                        <td>'+phone+'</td>\
                        <td>'+website+'</td>\
                    </tr>';
        }

    </script>

</html>
