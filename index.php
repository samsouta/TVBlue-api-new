<?php
// index.php: Display the form to enter the m3u8 URL
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HLS Embed Generator</title>
    <style>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enter m3u8 URL</h1>
        <form action="process.php" method="post">
            <label for="m3u8url">M3U8 URL:</label><br>
            <input type="url" id="m3u8url" name="m3u8url" required style="width: 100%; padding: 8px;"><br><br>
            <input type="submit" value="Generate Embed Link" style="padding: 8px 16px;">
        </form>
    </div>
</body>
</html>
