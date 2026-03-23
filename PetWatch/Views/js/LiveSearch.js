class LiveSearch {
    constructor(live, regular) {
        // Where the content will be
        this.searchElement = document.getElementById(live);
        this.regElement = document.getElementById(regular);
        this.searchTimeout = null; // Adding timer so we arent spamming the database witgh requests
        //previously every letter would send a request to the database
    }
    //Handle search to control when the page goes back to default after the search box has been cleared
    handleSearch(str){
        str=str.trim();
        clearTimeout(this.searchTimeout); // function that cancels previously set timeout
        this.searchTimeout = setTimeout(() => {

            // Runs the code after the user has stopped typing for 300 ms
            this.execute(str);

        }, 300);



    }
    execute(str)
    {
        console.log(str);
        if (str.length < 2) {
            // if theres less than 2 characters then clear
            var regHTML = this.regElement.innerHTML;
            this.clearSearch(regHTML)

        } else if (str.length >= 2) {
            this.fetchHint(str)
        }
    }
    clearSearch(regHTML) {
        //show the regular content and hide the search items

        this.searchElement.style.display = "none";
        this.regElement.style.display = "flex";
        this.regElement.innerHTML = regHTML;
        this.searchElement.innerHTML= ""; //sets seatch items to nothing


    }
    // Method to handle the AJAX request
    // Method to handle the AJAX request using fetch()
    fetchHint(str) {

        // Hide the regular stuff and show the search items now
        this.regElement.style.display = "none";
        this.searchElement.style.display = "flex";
        this.searchElement.innerHTML = "<p>loading...</p>";

        //Collecting generated element for security cgeck
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        const token = tokenElement ? tokenElement.getAttribute('content') : '';


        // Whitespace and special characters now won't destroy it
        const url = "liveSearchController.php?q=" + encodeURIComponent(str) + "&token=" + encodeURIComponent(token);

        // Fetch
        fetch(url)
            .then(response => {
                // Check if the server responded successfully (Status 200)
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }

                return response.text();
            })
            .then(data => {
                // Pass the data to your updatePage method
                this.updatePage(data);
            })
            .catch(error => {
                // If the server crashes or the network fails, catch it here
                console.error("Error fetching search hints:", error);
                this.searchElement.innerHTML = "<p class='text-danger'>An error occurred while searching.</p>";
            });
    }

    // This is the function you already started!
    updatePage(responseText) {
        if (responseText === "no suggestions") {
            this.searchElement.innerHTML = "No matches found.";
            return;
        }

        const pets = JSON.parse(responseText);
        let html = ""; // Start fresh

        pets.forEach(pet => {
            // Match your PHP logic for the badge
            const statusClass = pet.status === "lost" ? "badge bg-danger" : "badge bg-success";

            // Use the exact keys from your PHP $jsonResults array

            html += `
            <div class="card mb-3" style="width: 18rem; display: inline-block; margin: 10px; vertical-align: top;">
                <img src="${pet.photoURL}" class="card-img-top" style="height: 250px; object-fit: cover;">
                
                <div class="card-body">
                    <h5 class="card-title">${pet.name}</h5>
                    <p class="card-text">${pet.description}</p>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Status:</strong>
                        <span class="${statusClass}">${pet.status}</span>
                    </li>
                    <li class="list-group-item"><strong>Species:</strong> ${pet.species}</li>
                    <li class="list-group-item"><strong>Breed:</strong> ${pet.breed}</li>
                    <li class="list-group-item"><strong>Colour:</strong> ${pet.colour}</li>
                    <li class="list-group-item"><strong>Date Reported:</strong> ${pet.dateReported}</li>
                    <li class="list-group-item"><strong>Pet ID:</strong> ${pet.id}</li>
                    
                    <li class="list-group-item"><strong>Sightings:</strong> ${pet.comment}</li>
                    <li class="list-group-item"><strong>Location(s):</strong> ${pet.latitude}, ${pet.longitude}</li>
                </ul>

                <div class="card-body">
                    <a href="#" class="btn btn-outline-info btn-sm">Report Sighting</a>
                    <a href="editPet.php?pet_id=${pet.id}" class="btn btn-outline-warning btn-sm">Edit Pet</a>
                </div>
            </div>`;
        });

        this.searchElement.innerHTML = html;
    }
}

// Initialize the class so your HTML can use it
const liveSearch = new LiveSearch("liveContent", "regularContent");
