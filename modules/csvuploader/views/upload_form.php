<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV - Kingdom of Atenveldt</title>
    <style>
        body {
            background-color: #003399; /* Dark blue */
            color: #FFD700; /* Gold */
            font-family: 'Arial', sans-serif;
            text-align: center;
            padding: 50px;
        }
        h1 {
            font-family: 'Georgia', serif;
            font-size: 2.5em;
        }
        form {
            background-color: #FFD700;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
        input[type="file"] {
            display: block;
            margin: 20px auto;
            font-size: 1em;
        }
        input[type="submit"] {
            background-color: #003399;
            color: #FFD700;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #002266;
        }
    </style>
</head>
<body>
    <h1>Upload CSV File</h1>
    <form action="<?= BASE_URL ?>csvuploader/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv" required>
        <input type="submit" value="Upload">
    </form>
</body>
</html>
