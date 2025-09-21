<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <style>
    body {
      background: #181a20;
      color: #e4e6eb;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }
    h1 {
      margin-top: 60px;
      color: #e4e6eb;
      font-size: 2.2rem;
      letter-spacing: 1px;
    }
    .links {
      margin-top: 40px;
      display: flex;
      gap: 30px;
    }
    a {
      background: #22242c;
      color: #61dafb;
      text-decoration: none;
      padding: 14px 28px;
      border-radius: 8px;
      font-size: 1.1rem;
      transition: background 0.2s, color 0.2s;
      box-shadow: 0 2px 8px rgba(44,62,80,0.18);
      border: 1px solid #333;
    }
    a:hover {
      background: #61dafb;
      color: #22242c;
    }
  </style>
</head>
<body>

<h1>Welcome to the Admin Dashboard</h1>

<div class="links">
  <a href="https://movie-system-vue.vercel.app/" target="blank">Go to movie system</a>
  <a href="https://movie-reservation-system-production-46e8.up.railway.app/admin" target="blank">Go to admin panel</a>
</div>

</body>
</html>