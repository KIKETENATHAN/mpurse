// Contact Form
function validateForm() {
  var name = document.forms["myForm"]["name"].value;
  var email = document.forms["myForm"]["email"].value;
  var phone = document.forms["myForm"]["phone"].value;
  /*var comments = document.forms["myForm"]["comments"].value;*/

  var kenyanPhonePattern = /^(\+254|0)[17]\d{8}$/;

  document.getElementById("error-msg").style.opacity = 0;
  document.getElementById('error-msg').innerHTML = "";
  if (name == "" || name == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Organization Name*</div>";
    fadeIn();
    return false;
  }
  if (email == "" || email == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Organization Email*</div>";
    fadeIn();
    return false;
  }
  if (phone == "" || phone == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Organization Phone Number*</div>";
    fadeIn();
    return false;
  }
  // Check if the phone number matches the Kenyan phone number pattern
  if (!kenyanPhonePattern.test(phone)) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a valid Kenyan phone number*</div>";
    fadeIn();
    return false;
  }
  /*if (comments == "" || comments == null) {
    document.getElementById('error-msg').innerHTML = "<div class='alert alert-warning error_message'>*Please enter a Comments*</div>";
    fadeIn();
    return false;
  }*/

  // If form validation passes, submit the data to Firebase Function
  submitToFirebase();
  return false;
}

function submitToFirebase() {
  var name = document.forms["myForm"]["name"].value;
  var email = document.forms["myForm"]["email"].value;
  var phone = document.forms["myForm"]["phone"].value;
  /*var comments = document.forms["myForm"]["comments"].value;*/

  var data = {
    name: name,
    email: email,
    phone: phone,
    /*comments: comments*/
  };

  fetch("https://us-central1-samich-pcms.cloudfunctions.net/demoRequest", {
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
          document.getElementById("simple-msg").innerHTML = "<fieldset><div id='success_page'><h5>Request sent successfully.</h5><p>Thank you, your request has been submitted to us.</p></div></fieldset>";
          document.forms["myForm"]["name"].value = "";
          document.forms["myForm"]["email"].value = "";
          document.forms["myForm"]["phone"].value = "";
          /*document.forms["myForm"]["comments"].value = "";*/
        } else {
          document.getElementById("error-msg").innerHTML = "Request submission failed.";
        }
      })
      .catch(error => {
        console.error("Error:", error);
        document.getElementById("error-msg").innerHTML = "An error occurred during request submission.";
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
