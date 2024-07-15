    //Quantity button
    function updateQuantity(item, change) {
        let quantityElement = document.getElementById(`${item}-quantity`);
        let currentQuantity = parseInt(quantityElement.value);
        let newQuantity = currentQuantity + change;

        if (newQuantity < 0) {
            newQuantity = 0;    }

        quantityElement.value = newQuantity;
    }    
    // JavaScript for scroll-down button behavior
    function scrollToCustomerProgress() {
        // Scroll to the customer progress section
        document.getElementById('customer-progress').scrollIntoView({ behavior: 'smooth' });

        // Hide the button after clicking
        document.getElementById('scroll-down-button').style.display = 'none';
    }

    // Show the scroll-down button when the user is at the top of the page
    window.addEventListener('scroll', function() {
        const scrollButton = document.querySelector('.scroll-down-button');
        if (scrollButton) {
            if (window.scrollY === 0) {
                scrollButton.style.display = 'block';
            } else {
                scrollButton.style.display = 'none';
            }
        }
    });

    // Show the scroll-to-top button when the user scrolls down
    window.onscroll = function() {
        var topButton = document.getElementById("topcontrol");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            topButton.style.display = "block";
        } else {
            topButton.style.display = "none";
        }
    };

    // Scroll to the top of the document when the button is clicked
    document.getElementById("topcontrol").onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Function to trigger fonts loaded events (if needed)
    let customifyTriggerFontsLoadedEvents = function() {
        window.dispatchEvent(new Event('wf-active'));
        document.getElementsByTagName('html')[0].classList.add('wf-active');
    };

// Function to reload or refresh the customer progress table
function reloadCustomerProgressTable() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_customer_progress.php', true);

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 400) {
            // Success! Parse the JSON response
            var data = JSON.parse(xhr.responseText);
            var tableBody = document.getElementById('progress-table-body');

            // Clear existing table rows
            tableBody.innerHTML = '';

            // Populate table rows with new data
            data.forEach(function (progress_row) {
                var row = document.createElement('tr');
                row.innerHTML = '<td>#' + progress_row.order_id + '</td>' +
                    '<td>' + (progress_row.table_number !== 'N/A' ? 'Table ' + progress_row.table_number : progress_row.table_number) + '</td>' +
                    '<td class="progress-cell">' +
                    '<span class="progress-status ' + getStatusClass(progress_row.progress) + '">' + progress_row.progress + '</span>' +
                    '</td>';
                tableBody.appendChild(row);
            });
        } else {
            // Error handling
            console.error('Error fetching data: ' + xhr.statusText);
        }
    };

    xhr.onerror = function () {
        // Network errors
        console.error('Network error');
    };

    // Send the AJAX request
    xhr.send();
}


// Function to determine CSS class based on progress status
function getStatusClass(status) {
    switch (status) {
        case 'Pending':
            return 'progress-pending';
        case 'Preparing':
            return 'progress-preparing';
        case 'Delivered':
            return 'progress-delivered';
        default:
            return '';
    }
}

// Call this function to fetch and update the table initially
document.addEventListener('DOMContentLoaded', function() {
    reloadCustomerProgressTable();
});

    // Refresh the page every 15 seconds
    setInterval(function(){
        window.location.reload();
    }, 15000);

