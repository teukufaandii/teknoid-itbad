        function openNav() {
            var sidenavWidth = document.getElementById("mySidenav").style.width;
            if (sidenavWidth === "200px") {
                closeNav();
            } else {
                document.getElementById("mySidenav").style.width = "200px";
                document.getElementById("Content").style.marginLeft = "200px";
            }
        }
        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("Content").style.marginLeft = "0";
        }
        /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content 
        This allows the user to have multiple dropdowns without any conflict */
        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            } else {
            dropdownContent.style.display = "block";
            }
        });
        }
// Get the modal
var accMdl = document.getElementById("accModal");

// Get the button that opens the modal
var accBtn = document.getElementById("accModalBtn");

// When the user clicks the button, toggle the modal
accBtn.onclick = function() {
    accMdl.style.display === "block" ? closeModalAcc() : openModalAcc();
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == accMdl) {
        closeModalAcc();
    }
}

// Function to open modal
function openModalAcc() {
    accMdl.style.display = "block";
}

// Function to close modal
function closeModalAcc() {
    accMdl.style.display = "none";
}
