// Contact Form
function validateForm() {
  var name = document.forms["myForm"]["name"].value;
  var email = document.forms["myForm"]["email"].value;
  var subject = document.forms["myForm"]["subject"].value;
  var comments = document.forms["myForm"]["comments"].value;
  document.getElementById("error-msg").style.opacity = 0;
  document.getElementById('error-msg').innerHTML = "";
  if (name == "" || name == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Name*</div>";
    fadeIn();
    return false;
  }
  if (email == "" || email == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Email*</div>";
    fadeIn();
    return false;
  }
  if (subject == "" || subject == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Subject*</div>";
    fadeIn();
    return false;
  }
  if (comments == "" || comments == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Comments*</div>";
    fadeIn();
    return false;
  }

  // If form validation passes, submit the data to Firebase Function
  submitToFirebase();
  return false;
}

function submitToFirebase() {
  var name = document.forms["myForm"]["name"].value;
  var email = document.forms["myForm"]["email"].value;
  var subject = document.forms["myForm"]["subject"].value;
  var comments = document.forms["myForm"]["comments"].value;

  var data = {
    name: name,
    email: email,
    subject: subject,
    comments: comments
  };

  fetch("https://us-central1-samich-pcms.cloudfunctions.net/contactMessage", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(data => {
        // Handle the response from the Firebase Function here
        if (data.success) {
          document.getElementById("simple-msg").innerHTML = "<fieldset><div id='success_page'><h5>Message sent successfully.</h5><p>Thank you, your message has been submitted to us.</p></div></fieldset>";
          document.forms["myForm"]["name"].value = "";
          document.forms["myForm"]["email"].value = "";
          document.forms["myForm"]["subject"].value = "";
          document.forms["myForm"]["comments"].value = "";
        } else {
          document.getElementById("error-msg").innerHTML = "Message submission failed.";
        }
      })
      .catch(error => {
        console.error("Error:", error);
        document.getElementById("error-msg").innerHTML = "An error occurred during message submission.";
      });
}

function fadeIn() {
  var fade = document.getElementById("error-msg");
  var opacity = 0;
  var intervalID = setInterval(function () {
    if (opacity < 1) {
      opacity = opacity + 0.5
      fade.style.opacity = opacity;
    } else {
      clearInterval(intervalID);
    }
  }, 200);
}
