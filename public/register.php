<?php
require_once '../includes/header.php';

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!isValidEmail($email)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email already registered";
            }
        } catch(PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
            error_log("Registration error: " . $e->getMessage());
        }
    }

    // If no errors, create the user
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            
            redirect('dashboard.php', 'Registration successful! Welcome to HobbyHub.', 'success');
        } catch(PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<!-- Background with gradient overlay -->
<div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden">
  <!-- Decorative background elements -->
  <div class="absolute inset-0 overflow-hidden opacity-20">
    <div class="absolute rounded-full bg-indigo-200 w-64 h-64 -top-32 -left-32 animate-float"></div>
    <div class="absolute rounded-full bg-purple-200 w-96 h-96 top-1/4 -right-48 animate-float-delay"></div>
    <div class="absolute rounded-full bg-blue-200 w-80 h-80 bottom-32 left-1/4 animate-float-delay-2"></div>
  </div>
  
  <!-- Background image with overlay -->
  <div class="absolute inset-0 z-0">
    <img src="https://png.pngtree.com/png-clipart/20230824/original/pngtree-kids-hobbies-child-hobby-kid-picture-image_8441717.png" 
         alt="People enjoying hobbies together" 
         class="w-full h-full object-cover opacity-10">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/10 to-purple-900/10"></div>
  </div>

  <div class="relative z-10 w-full max-w-md px-4 py-12">
    <!-- Logo/Header -->
    <div class="text-center mb-10">
      <div class="flex justify-center mb-4">
        <div class="w-16 h-16 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg transform rotate-0 hover:rotate-12 transition-transform duration-500">
          <i class="fas fa-user-plus text-white text-2xl"></i>
        </div>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
        Join <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">HobbyHub</span>
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        Connect with fellow enthusiasts and grow your passion
      </p>
    </div>

    <!-- Card Container -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
      <!-- Gradient top border -->
      <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
      
      <div class="px-8 py-8 sm:px-10 sm:py-10">
        <?php if (!empty($errors)): ?>
          <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 shadow-sm">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  There were <?php echo count($errors); ?> errors with your submission
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <form class="space-y-5" action="register.php" method="POST">
          <!-- Name Field -->
          <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-gray-700">
              Full name
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="name" name="name" type="text" autocomplete="name" required
                     value="<?php echo htmlspecialchars($name); ?>"
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Email Field -->
          <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email address
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
              </div>
              <input id="email" name="email" type="email" autocomplete="email" required
                     value="<?php echo htmlspecialchars($email); ?>"
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Password Field -->
          <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password <span class="text-xs text-gray-500">(min 8 characters)</span>
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="password" name="password" type="password" autocomplete="new-password" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Confirm Password Field -->
          <div class="space-y-1">
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                     class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-3 py-3 border-gray-300 rounded-md transition duration-300 ease-in-out hover:border-indigo-300">
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button type="submit" 
                    class="group w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Create Account</span>
              <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </button>
          </div>
        </form>

        <!-- Divider -->
        <div class="mt-8">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Already have an account?
              </span>
            </div>
          </div>

          <!-- Login Button -->
          <div class="mt-6">
            <a href="login.php" 
               class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5">
              <span class="mr-2">Sign in instead</span>
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
  }
  .animate-float {
    animation: float 6s ease-in-out infinite;
  }
  .animate-float-delay {
    animation: float 6s ease-in-out infinite 2s;
  }
  .animate-float-delay-2 {
    animation: float 6s ease-in-out infinite 4s;
  }
</style>

<?php
require_once '../includes/footer.php';
?>