class LiveSearch {
    constructor(live, regular) {
        // Where the content will be
        this.searchElement = document.getElementById(live);
        this.regElement = document.getElementById(regular);
        this.searchTimeout = null;

        // Adding timer so we arent spamming the database witgh requests
        //previously every letter would send a request to the database
        //Creating feature to wait before sending the request

        let loginData = document.getElementById("sessionData");

        // Ensure it actually exists before trying to read it, and attach it to 'this'
        if (loginData) {
            this.userID = loginData.dataset.first;
            this.userType = loginData.dataset.second;
        } else {
            this.userID = null;
            this.userType = null;
        }

    }

    //Handle search to control when the page goes back to default after the search box has been cleared
    //Will show pets as usual
    handleSearch(str) {
        str = str.trim();
        clearTimeout(this.searchTimeout); // function that cancels previously set timeout
        this.searchTimeout = setTimeout(() => {

            // Runs the code after the user has stopped typing for 300 ms
            this.execute(str);

        }, 300);


    }

    execute(str) {
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
        this.searchElement.innerHTML = ""; //sets seatch items to nothing


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

                return response.json();
            })
            .then(data => {
                // Pass the data to updatePage method
                this.updatePage(data);
            })
            .catch(error => {
                // If the server crashes or the network fails, catch it here
                console.error("Error fetching search hints:", error);
                this.searchElement.innerHTML = "<p class='text-danger'>An error occurred while searching.</p>";
            });
    }

    updatePage(pets) {
        if (pets === "no suggestions" || !pets || pets.length === 0) {
            this.searchElement.innerHTML = "<p class='text-center mt-5 w-100'>No matches found.</p>";
            return;
        }

        let html = "";

        pets.forEach(pet => {
            const statusClass = pet.status === "lost" ? "bg-danger" : "bg-success";
            const safeName = pet.name.replace(/'/g, "&apos;").replace(/"/g, "&quot;");

            html += `
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="${pet.photoURL}"
                 class="card-img-top"
                 alt="${pet.name}"
                 style="height: 250px; width=250px; object-fit: cover;">

            <div class="card-body">
                <h5 class="card-title">${pet.name}</h5>
                <p class="card-text">${pet.description}</p>
            </div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Status:</strong>
                    <span class="badge ${statusClass}">${pet.status}</span>
                </li>
                <li class="list-group-item"><strong>Species:</strong> ${pet.species}</li>
                <li class="list-group-item"><strong>Breed:</strong> ${pet.breed}</li>
                <li class="list-group-item"><strong>Colour:</strong> ${pet.colour}</li>
                <li class="list-group-item"><strong>Date Reported:</strong> ${pet.dateReported}</li>
                <li class="list-group-item"><strong>Pet ID:</strong> ${pet.id}</li>

                ${pet.comment ? `
                    <li class="list-group-item"><strong>Sightings:</strong> ${pet.comment}<br></li>
                    <li class="list-group-item"><strong>Location(s):</strong> ${pet.latitude}<br>${pet.longitude}</li>
                ` : ""}

                ${this.userType ? `
                    <a href="sighting.php?pet_id=${pet.id}&name=${encodeURIComponent(pet.name)}" class="btn btn-outline-info btn-sm">
                        Report Sighting Of Pet
                    </a>
                ` : ''}

                ${this.userID && this.userID == pet.userID ? `
                    <button type="button" 
                       class="btn btn-outline-danger btn-sm w-100" 
                       onclick="petClass.openDeleteModal(${pet.id}, '${safeName}')">
<!--                       Open the Modal, sends it id and name for confirmRION MESSAGE and actual deletion-->
                          Delete Pet
                    </button>
                     <button type="submit" class="btn btn-outline-danger btn-sm" onclick="petClass.openDeleteModal(${pet.id}, '${safeName}')">
                    Delete Pet
                   </button>
                ` : ''}
            </ul>
        </div>
    </div>`;
        });

        this.searchElement.innerHTML = html;
    }
}

const liveSearch = new LiveSearch("liveContent", "regularContent");