class Map {

    constructor() {
        // Custom marker icon
        this.pawIcon = L.icon({
            iconUrl: 'Views/images/cat_logo.png',
            shadowUrl: 'Views/images/shadow_logo.png',
            iconSize:     [19, 48],
            shadowSize:   [25, 32],
            iconAnchor:   [11, 47],
            shadowAnchor: [2, 31],
            popupAnchor:  [-2, -38]
        });
        //make sure map script doesnt preemptively fire before theres a page to put it in
        document.addEventListener("DOMContentLoaded", () => {
            const checker = document.getElementById("map");

            if (checker) {
                this.leafletMap = L.map('map').setView([54.5, -3.5], 6);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(this.leafletMap);

                // Grouping to make it not ugly and cluttered
                this.markerCluster = L.markerClusterGroup();
                // Add the cluster group map
                this.leafletMap.addLayer(this.markerCluster);


                //has it wait 400 ms and checks the size of the html box its in so that we dont have a mostly blank map

                setTimeout(() => {
                    this.leafletMap.invalidateSize();
                }, 400);



                this.leafletMap.on('locationfound', (e) => {
                    var radius = e.accuracy;
                    // User location pin
                    L.marker(e.latlng, {icon: this.pawIcon}).addTo(this.leafletMap)
                        .bindPopup("You are within " + radius + " meters from this point").openPopup();
                    L.circle(e.latlng, radius).addTo(this.leafletMap);
                });

                this.leafletMap.on('locationerror', (e) => {
                    alert("Location access denied or not available.");
                });

                this.leafletMap.locate({setView: true, maxZoom: 16});

                this.request(); // call for AJAX request
            } else {
                console.log("Likely, no sightings in table");

            }
        });
    }


    // ajax request handler
    request() {
        fetch('mapController.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok'); //checking if network responded
                }
                return response.json();
            })
            .then(data => {
                this.updatePage(data); // call function to popullate map
            })
            .catch(error => {
                console.error('Error fetching map data:', error);
                document.getElementById('map').innerHTML = "Failed to load map";
            });
    }

    updatePage(pets) {
        if (!pets || pets.length === 0) return;
        //looping through json data to get each pet, andput them in db
        //Creates card for pets when clicked on
        pets.forEach(pet => {
            const statusClass = pet.status === "lost" ? "badge bg-danger" : "badge bg-success";

            let popupHtml = `
            <div class="card mb-3" style="width: 18rem; border: none;">
                <img src="${pet.photoURL}" class="card-img-top" style="height: 200px; object-fit: cover;">
                
                <div class="card-body" style="padding: 0.5rem;">
                    <h5 class="card-title" style="margin-bottom: 0.5rem;">${pet.name}</h5>
                    <p class="card-text" style="font-size: 0.9rem;">${pet.description}</p>
                </div>

                <ul class="list-group list-group-flush" style="font-size: 0.85rem;">
                    <li class="list-group-item" style="padding: 0.3rem 0.5rem;">
                        <strong>Status:</strong>
                        <span class="${statusClass}">${pet.status}</span>
                    </li>
                    <li class="list-group-item" style="padding: 0.3rem 0.5rem;"><strong>Species:</strong> ${pet.species}</li>
                    <li class="list-group-item" style="padding: 0.3rem 0.5rem;"><strong>Breed:</strong> ${pet.breed}</li>
                    <li class="list-group-item" style="padding: 0.3rem 0.5rem;"><strong>Date Reported:</strong> ${pet.dateReported}</li>
                </ul>

                
            </div>`;


            let newMarker = L.marker([pet.latitude, pet.longitude], {icon: this.pawIcon})
                .bindPopup(popupHtml);

            this.markerCluster.addLayer(newMarker);
        });
    }
}

const map = new Map() //initialise