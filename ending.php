<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            background: black;
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Dancing Script', cursive;
            text-align: center;
            overflow: hidden;
        }
        h1 { opacity: 0; animation: fadein 5s forwards; font-size: 2.5rem; }
        .heart { font-size: 5rem; animation: heartbeat 2s infinite; margin-bottom: 20px; }
        
        @keyframes fadein { 0% { opacity: 0; } 100% { opacity: 1; } }
        @keyframes heartbeat { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="heart">❤️</div>
    <h1>"Terima kasih sudah hadir dalam hidupku."</h1>
    
    <script>
        // Fade out music logic
        const audio = new Audio('assets/music/bgm.mp3');
        // Note: Audio won't play automatically here unless user interacted before 
        // passing audio context, usually handled by single page feel, simple version implies silence or user interaction.
        
        setTimeout(() => {
            // Redirect back to start or close
            // window.location.href = "index.php"; 
        }, 10000);
    </script>
</body>
</html>