
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" id="signup" style="display:none;">
      <h1 class="form-title">Register</h1>
      <form method="post" action="register.php" id="signupForm">
        <div class="input-group">
           <i class="fas fa-user"></i>
           <input type="text" name="fName" id="fName" placeholder="First Name" required>
           <label for="fname">First Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lName" id="lName" placeholder="Last Name" required>
            <label for="lName">Last Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <label for="email">Email</label>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>
        <!-- Hidden input for keystroke data -->
        <input type="hidden" name="keystrokeData" id="keystrokeData">
        <input type="submit" class="btn" value="Sign Up" name="signUp">
      </form>

      <p class="or">----------or--------</p>

      <div class="icons">
        <i class="fab fa-google"></i>
        <i class="fab fa-facebook"></i>
      </div>

      <div class="links">
        <p>Already Have an Account?</p>
        <button id="signInButton">Sign In</button>
      </div>
    </div>

    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php" id="signinForm">
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="emailSignIn" placeholder="Email" required>
              <label for="emailSignIn">Email</label>
          </div>

          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="passwordSignIn" placeholder="Password" required>
              <label for="passwordSignIn">Password</label>
          </div>

          <!-- Hidden input for keystroke data -->
          <input type="hidden" name="keystrokeData" id="keystrokeDataSignIn">

          <p class='recover'>
            <a href="#">Recover Password</a>
          </p>

         <input type='submit' class='btn' value='Sign In' name='signIn'>
        </form>

        <p class='or'>----------or--------</p>

        <div class='icons'>
          <i class='fab fa-google'></i>
          <i class='fab fa-facebook'></i> 
        </div>

        <div class='links'>
          <p>Don't have an account yet?</p>
          <button id='signUpButton'>Sign Up</button>
        </div>
      </div>

      <!-- JavaScript for Keystroke Logging and Form Toggling -->
      <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log("DOM fully loaded and parsed");

            // Toggle between Sign Up and Sign In forms
            const signUpButton = document.getElementById('signUpButton');
            const signInButton = document.getElementById('signInButton');
            const signUpForm = document.getElementById('signup');
            const signInForm = document.getElementById('signIn');

            signUpButton.addEventListener('click', function () {
                signUpForm.style.display = 'block';
                signInForm.style.display = 'none';
            });

            signInButton.addEventListener('click', function () {
                signInForm.style.display = 'block';
                signUpForm.style.display = 'none';
            });

            // Function to capture keystrokes
            function captureKeystrokes(inputElement, keystrokeData) {
                inputElement.addEventListener('keydown', function (event) {
                    const key = event.key; // The key pressed
                    const timestamp = new Date().getTime(); // Current timestamp
                    keystrokeData.push({ key, timestamp });
                    console.log(`Key pressed: ${key}, Timestamp: ${timestamp}`);
                });
            }

            // For Registration Form
            const signupForm = document.getElementById('signupForm');
            const signupKeystrokeData = [];
            const signupInputs = signupForm.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            signupInputs.forEach(input => captureKeystrokes(input, signupKeystrokeData));

            // For Login Form
            const signinForm = document.getElementById('signinForm');
            const signinKeystrokeData = [];
            const signinInputs = signinForm.querySelectorAll('input[type="email"], input[type="password"]');
            signinInputs.forEach(input => captureKeystrokes(input, signinKeystrokeData));

            // Before submitting the form, store keystroke data in the hidden input
            signupForm.addEventListener('submit', function (event) {
                console.log("Sign Up form submitted");
                document.getElementById('keystrokeData').value = JSON.stringify(signupKeystrokeData);
                console.log("Keystroke Data:", signupKeystrokeData);
            });

            signinForm.addEventListener('submit', function (event) {
                console.log("Sign In form submitted");
                document.getElementById('keystrokeDataSignIn').value = JSON.stringify(signinKeystrokeData);
                console.log("Keystroke Data:", signinKeystrokeData);
            });
        });
      </script>

</body>
</html>