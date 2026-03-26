class Pets {
    constructor(container) {
        this.container = document.getElementById(container);

        //Block holds user id echoed from session
        let loginData = document.getElementById("sessionData");
        if (loginData) {
            this.userID = loginData.dataset.first;
            this.userType = loginData.dataset.second;
        } else {
            this.userID = null;
            this.userType = null;
        }

        this.allPets = []; // array that contains all pets
        this.currentPage = 1; //page we currently on, initialised to 1
        this.petsPerPage = 6;

        document.addEventListener("DOMContentLoaded", () => { this.populate(); }); // run function when page is fully loaded
        //prevents js firing with nowhere to put data
    }

    openEditModal(petId) {
        fetch(`pets.php?ajax_pet_id=${petId}`)
            .then(response => {
                if (!response.ok) throw new Error("Server error");
                return response.json();
            })
            .then(pet => {
                document.getElementById('edit_pet_id').value = pet.id;
                document.getElementById('edit_existing_photo').value = pet.photoURL ? pet.photoURL.replace("Views/images/", "") : "";
                document.getElementById('edit_name').value = pet.name;
                document.getElementById('edit_status').value = pet.status;
                document.getElementById('edit_species').value = pet.species;
                document.getElementById('edit_breed').value = pet.breed;
                document.getElementById('edit_colour').value = pet.colour;
                document.getElementById('edit_dateReported').value = pet.dateReported;
                document.getElementById('edit_description').value = pet.description;
                // Shows current information so that the values that are unchanged stay the same
                $('#editPetModal').modal('show');
            })
            .catch(error => console.error("Error:", error));
    }

    openDeleteModal(petId, petName) {
        document.getElementById('deletePetName').textContent = petName;
        document.getElementById('deletePetId').value = petId;
        // Get values
        $('#deleteModal').modal('show');
    }

    populate() {
        // Function to put pets in table
        if (this.container) {
            this.container.innerHTML = "<p class='text-center mt-5'>Loading pets...</p>";
        }

        const metaTag = document.querySelector('meta[name="csrf-token"]');
        const token = metaTag ? metaTag.content : '';

        fetch("pets.php?ajax_fetch_all=true&token=" + encodeURIComponent(token))
            .then(response => { //happens if it passes the security check
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
            })
            .then(data => {
                this.allPets = data;
                this.currentPage = 1;
                this.renderCurrentPage();
            })
            .catch(error => {
                console.error("Error fetching pets:", error);
                if (this.container) {
                    this.container.innerHTML = "<p class='text-danger text-center mt-5'>An error occurred while loading pets.</p>";
                }
            });
    }

    renderCurrentPage() {
        if (!this.allPets || this.allPets.length === 0 || this.allPets === "no suggestions") {
            this.container.innerHTML = "<p class='text-center mt-5'>No pets available.</p>";
            return;
        }

        const startIndex = (this.currentPage - 1) * this.petsPerPage;
        //Picking out which chunk of the array to grab from, if page is 1 itll sstart from element 0 in array
        const endIndex = startIndex + this.petsPerPage;
        const petsToShow = this.allPets.slice(startIndex, endIndex); // for page 1 it grabs index 0,1,2,3,4,5,6 from the array
        //makes smaller array and sends that to update page to generate the html
        this.updatePage(petsToShow);
        //generating page controls under the cards
        this.renderPaginationControls();
    }

    changePage(pageNumber) {
        //Button passes page number to function so we can update the current number
        this.currentPage = pageNumber;
        this.renderCurrentPage(); //reruns array splicer for this page
        window.scrollTo({ top: this.container.offsetTop - 50, behavior: 'smooth' });
        //scroll to top of container
    }

    updatePage(pets) {
        let html = "";

        pets.forEach(pet => {
            const statusClass = pet.status === "lost" ? "bg-danger" : "bg-success";
            const safeName = pet.name.replace(/'/g, "&apos;").replace(/"/g, "&quot;");

            html += `
    <div class="col-md-4 mb-4">
        <div class="card">
            <img src="${pet.photoURL}" class="card-img-top" alt="${pet.name}" style="height: 250px; width=250px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">${pet.name}</h5>
                <p class="card-text">${pet.description}</p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Status:</strong> <span class="badge ${statusClass}">${pet.status}</span></li>
                <li class="list-group-item"><strong>Species:</strong> ${pet.species}</li>
                <li class="list-group-item"><strong>Breed:</strong> ${pet.breed}</li>
                <li class="list-group-item"><strong>Colour:</strong> ${pet.colour}</li>
                <li class="list-group-item"><strong>Date Reported:</strong> ${pet.dateReported}</li>
                <li class="list-group-item"><strong>Pet ID:</strong> ${pet.id}</li>
                ${pet.comment ? `
                <li class="list-group-item"><strong>Sightings:</strong> ${pet.comment}<br></li>
                <li class="list-group-item"><strong>Location(s):</strong> ${pet.latitude}<br>${pet.longitude}</li>
                ` : ''}
                ${this.userType ? `
                <a href="sighting.php?pet_id=${pet.id}&name=${encodeURIComponent(pet.name)}" class="btn btn-outline-info btn-sm">
                    Report Sighting Of Pet
                </a>
                ` : ''}
                ${this.userID && this.userID == pet.userID ? `
                <button type="button" 
                class="btn btn-outline-warning btn-sm w-100" 
                onclick="petClass.openEditModal(${pet.id})">
           Edit Pet
        </button>
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="petClass.openDeleteModal(${pet.id}, '${safeName}')">
                    Delete Pet
                </button>
                ` : ''}
            </ul>
        </div>
    </div>`;
        });

        if (this.container) {
            this.container.innerHTML = html;
        }
    }

    renderPaginationControls() {
        //Getting total pages, ceil probably more than needed but better safe.
        //Total pets / pets per page
        const totalPages = Math.ceil(this.allPets.length / this.petsPerPage);

        if (totalPages <= 1) return; //if pets all show on page 1 then we dont need to see buttons

        let html = `
        <div class="col-12 mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center flex-wrap">`;

        // Can only see previous if past page 1
        if (this.currentPage > 1) {
            html += `<li class="page-item"><button class="page-link" onclick="petClass.changePage(${this.currentPage - 1})">Previous</button></li>`;
        } else {
            html += `<li class="page-item disabled"><button class="page-link" tabindex="-1">Previous</button></li>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === this.currentPage) {
                html += `<li class="page-item active"><button class="page-link">${i}</button></li>`;
            } else {
                html += `<li class="page-item"><button class="page-link" onclick="petClass.changePage(${i})">${i}</button></li>`;
            }
        }

        if (this.currentPage < totalPages) {
            html += `<li class="page-item"><button class="page-link" onclick="petClass.changePage(${this.currentPage + 1})">Next</button></li>`;
        } else {
            html += `<li class="page-item disabled"><button class="page-link" tabindex="-1">Next</button></li>`;
        }

        html += `
                </ul>
            </nav>
        </div>`;

        this.container.insertAdjacentHTML('beforeend', html); //sticks at end of container so as not yo replace existing html
    }
}

const petClass = new Pets("regularContent");  //intiialise with container name