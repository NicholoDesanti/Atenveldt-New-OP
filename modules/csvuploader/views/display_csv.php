<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Content - Kingdom of Atenveldt</title>
    <style>
        body {
            background-color: #003399; /* Dark blue */
            color: #FFD700; /* Gold */
            font-family: 'Arial', sans-serif;
            text-align: center;
            padding: 50px;
        }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid #FFD700;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #003399;
            color: #FFD700;
        }
    </style>
</head>
<body>
    <h1>CSV Content</h1>
    <table>
        <?php foreach ($csv_data as $index => $row): ?>
            <tr>
                <?php foreach ($row as $col): ?>
                    <td><?= htmlspecialchars($col) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
