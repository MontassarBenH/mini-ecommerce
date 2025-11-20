<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mini E-Commerce</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .auth-container h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #0056b3;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="auth-container">
        <h1>Login</h1>
        
        <div id="error-message" class="error-message"></div>
        <div id="success-message" class="success-message"></div>
        
        <form id="login-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-submit">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="<?= BASE_URL ?>/register">Register here</a></p>
        </div>
    </div>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
    
    <script>
        const loginForm = document.getElementById('login-form');
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');
        
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
            
            const formData = new FormData(loginForm);
            const data = {
                action: 'login',
                email: formData.get('email'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('<?= BASE_URL ?>/api/auth', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    successMessage.textContent = 'Login successful! Redirecting...';
                    successMessage.style.display = 'block';
                    
                    setTimeout(() => {
                        window.location.href = '<?= BASE_URL ?>/my-orders';
                    }, 1000);
                } else {
                    errorMessage.textContent = result.message || 'Login failed';
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                errorMessage.textContent = 'Network error. Please try again.';
                errorMessage.style.display = 'block';
            }
        });
    </script>
</body>
</html>