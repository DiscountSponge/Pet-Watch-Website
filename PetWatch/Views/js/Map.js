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
        //Without arrow "this" keyword would refer to the document
        //using arrow it refers to the instance of the Map class
        document.addEventListener("DOMContentLoaded", () => {
            const checker = document.getElementById("map");

            if (checker) {
                this.leafletMap = L.map('map').setView([54.5, -3.5], 6);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(this.leafletMap);

                // Grouping to make it not cluttered
                this.markerCluster = L.markerClusterGroup();
                // Add the cluster group to the map
                this.leafletMap.addLayer(this.markerCluster);

                //has it wait 400 ms and checks the size of the html box its in so that we dont have a mostly blank map
                setTimeout(() => {
                    this.leafletMap.invalidateSize();
                }, 400);

                this.leafletMap.on('locationfound', (e) => {
                    var radius = e.accuracy;
                    // User location pin
                    L.marker(e.latlng, {icon: this.pawIcon}).addTo(this.leafletMap)
                        .bindPopup("You are within " + radius.toFixed(2) + " metres of this point").openPopup();
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
    //Below is security token so somebody canrt read json data
    request() {
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        const token = tokenElement ? tokenElement.getAttribute('content') : '';

        //Attach token to string so that it can be checked in controller
        const url = 'mapController.php?token=' + encodeURIComponent(token);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok'); //checking if network responded
                }
                // We use .json() now because PetData implements jsonSerializable
                return response.json();
            })
            .then(data => {
                this.updatePage(data); // call function to populate map, sends it the json data
            })
            .catch(error => {
                console.error('Error fetching map data:', error);
                document.getElementById('map').innerHTML = "Failed to load map";
            });
    }

    updatePage(pets) {
        if (!pets || pets.length === 0 || pets.error) return;
        //looping through json data to get each pet, and put them in db
        //Creates card for pets when clicked on

        pets.forEach(pet => {
            const statusClass = pet.status === "lost" ? "badge bg-danger" : "badge bg-success";
            //popupHTML will be placed into the popup feature of markers
            let popupHtml = `
            <div class="card mb-3" style="width: 18rem; border: none;">
                <img src="${pet.photoURL}" class="card-img-top" style="height: 150px; object-fit: cover;">
                
                <div class="card-body" style="padding: 0.5rem;">
                    <h5 class="card-title" style="margin-bottom: 0.5rem;">${pet.name}</h5>
                    <p class="card-text" style="font-size: 0.8rem; margin-bottom: 0.5rem;">${pet.description}</p>
                </div>

                <ul class="list-group list-group-flush" style="font-size: 0.8rem;">
                    <li class="list-group-item" style="padding: 0.2rem 0.5rem;">
                        <strong>Status:</strong> <span class="${statusClass}">${pet.status}</span>
                    </li>
                    <li class="list-group-item" style="padding: 0.2rem 0.5rem;"><strong>Species:</strong> ${pet.species}</li>
                    <li class="list-group-item" style="padding: 0.2rem 0.5rem;"><strong>Reported:</strong> ${pet.dateReported}</li>
                </ul>
                
                <div class="card-body" style="padding: 0.5rem;">
                    <a href="#" 
                       class="btn btn-outline-info btn-sm w-100">
                       Report Sighting
                    </a>
                </div>
            </div>`;

            // Use the latitude and longitude from the serialized pet object
            if (pet.latitude && pet.longitude) {
                let newMarker = L.marker([pet.latitude, pet.longitude], {icon: this.pawIcon})
                    .bindPopup(popupHtml);

                this.markerCluster.addLayer(newMarker);
            }
        });
    }
}

const map = new Map(); //initialise class so html can use it