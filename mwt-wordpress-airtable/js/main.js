// Provides capability for a user to close an event by a completion date and optionally completion notes.

const closeEvents = document.querySelectorAll('.submit-button'); // Select by CSS Selector  - in this case class="submit-button"

// Only add lsiteners if we are on a page where we have MTM Events..
if (closeEvents) {
    closeEvents.forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            const event = e.target.getAttribute('data-event');
            closeEvent(event);
        })
    });
}
    
// This is what we will do if we detect a 'click'...
function closeEvent(event) {
    // Use the unique ID of the form to get the form and the entered data.
    let eventForm = document.getElementById(`visit-form-${event}`);
    let eventNote = document.getElementById(`note-${event}`).value;
    let eventDate = document.getElementById(`date-${event}`).value;
    if (eventDate == "") {
        alert('Please enter a completion date');
        return false;
    } 
    // Construct the array to be POSTed
    let postData = {
        "eventid": event,
        "closenote": eventNote,
        "completeddate": eventDate,
    }

    // For now, add a post...   
    var updateEvent = new XMLHttpRequest();
    updateEvent.open('POST', mwtWebtech.siteURL + '/wp-json/mwtwebtech/v1/events'); // This will change in due course
    updateEvent.setRequestHeader("X-WP-Nonce", mwtWebtech.nonce); // Use the nonce to prevent CSRF
    updateEvent.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    // Define the callback that will handle success and failure..
    updateEvent.onreadystatechange = function() {
        if (updateEvent.readyState == 4) {
            var status = updateEvent.status;
            if (status === 0 || (status >= 200 && status < 400)) {
                // Give user feedback - in this case by adding the closure data to the event and removing the form.
                document.getElementById(`div-${event}`).innerHTML = "<p>Closed on " + postData.completeddate + "<br>Notes: " + postData.closenote + "</p><hr>";
                // Only if the update is successful will we remove the specific form.
                eventForm.remove();
                // And remove the associated textarea
                document.getElementById(`note-${event}`).remove();
                } else {
                    alert("Update failed.");
                }
            }
    }
    //  send the request
    updateEvent.send(JSON.stringify(postData));
}
