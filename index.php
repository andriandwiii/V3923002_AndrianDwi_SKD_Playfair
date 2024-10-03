<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playfair Cipher</title>
    <style>
    body {
        background-color: #f0f0f5;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 450px;
        border: 2px solid #6c63ff;
    }

    h1 {
        color: #6c63ff;
        font-size: 1.8em;
        margin-bottom: 15px;
        text-align: center;
        text-transform: uppercase;
    }

    input[type="text"] {
        width: calc(100% - 20px);
        padding: 8px;
        margin: 8px 0;
        border: 2px solid #ff6584;
        border-radius: 8px;
        font-size: 1em;
        background-color: #f4f4fa;
        color: #333;
    }

    input[type="text"]::placeholder {
        color: #999;
    }

    .btn {
        background-color: #ff6584;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 1em;
        cursor: pointer;
        border-radius: 8px;
        margin: 8px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #ff4060;
    }

    .footer {
        margin-top: 15px;
        font-size: 0.9em;
        color: #999;
        text-align: center;
    }

    .footer span {
        color: #6c63ff;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Playfair Cipher Enkripsi&Dekripsi</h1>

        <form method="POST" action="">
            <label for="plaintext">Plaintext:</label><br>
            <input type="text" id="plaintext" name="plaintext" placeholder="Masukan plaintext" required><br><br>

            <label for="keyword">Keyword:</label><br>
            <input type="text" id="keyword" name="keyword" placeholder="Masukan keyword" required><br><br>

            <button type="submit" class="btn">Enkripsi & Dekripsi</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Fungsi untuk memformat teks
            function prepareText($text) {
                $text = strtoupper(str_replace(' ', '', $text));
                $text = str_replace('J', 'I', $text); // 'J' digantikan oleh 'I'
                $preparedText = '';
                for ($i = 0; $i < strlen($text); $i += 2) {
                    $first = $text[$i];
                    $second = ($i + 1 < strlen($text)) ? $text[$i + 1] : 'X';

                    if ($first == $second) {
                        $second = 'X';
                        $i--;
                    }

                    $preparedText .= $first . $second;
                }

                if (strlen($preparedText) % 2 != 0) {
                    $preparedText .= 'X';
                }

                return $preparedText;
            }

            // Fungsi untuk membangun matriks Playfair
            function buildMatrix($keyword) {
                $alphabet = 'ABCDEFGHIKLMNOPQRSTUVWXYZ';
                $matrix = [];
                $usedChars = [];

                $keyword = strtoupper(str_replace('J', 'I', $keyword));
                foreach (str_split($keyword) as $char) {
                    if (!in_array($char, $usedChars)) {
                        $matrix[] = $char;
                        $usedChars[] = $char;
                    }
                }

                foreach (str_split($alphabet) as $char) {
                    if (!in_array($char, $usedChars)) {
                        $matrix[] = $char;
                        $usedChars[] = $char;
                    }
                }

                return array_chunk($matrix, 5);
            }

            // Fungsi untuk mencari posisi karakter di dalam matriks
            function getPosition($matrix, $char) {
                for ($i = 0; $i < 5; $i++) {
                    for ($j = 0; $j < 5; $j++) {
                        if ($matrix[$i][$j] == $char) {
                            return [$i, $j];
                        }
                    }
                }
                return null;
            }

            // Fungsi enkripsi Playfair
            function encrypt($text, $matrix) {
                $text = prepareText($text);
                $cipherText = '';

                for ($i = 0; $i < strlen($text); $i += 2) {
                    $firstChar = $text[$i];
                    $secondChar = $text[$i + 1];

                    list($row1, $col1) = getPosition($matrix, $firstChar);
                    list($row2, $col2) = getPosition($matrix, $secondChar);

                    if ($row1 == $row2) {
                        $cipherText .= $matrix[$row1][($col1 + 1) % 5] . $matrix[$row2][($col2 + 1) % 5];
                    } elseif ($col1 == $col2) {
                        $cipherText .= $matrix[($row1 + 1) % 5][$col1] . $matrix[($row2 + 1) % 5][$col2];
                    } else {
                        $cipherText .= $matrix[$row1][$col2] . $matrix[$row2][$col1];
                    }
                }

                return $cipherText;
            }

            // Fungsi dekripsi Playfair
            function decrypt($cipherText, $matrix) {
                $plainText = '';

                for ($i = 0; $i < strlen($cipherText); $i += 2) {
                    $firstChar = $cipherText[$i];
                    $secondChar = $cipherText[$i + 1];

                    list($row1, $col1) = getPosition($matrix, $firstChar);
                    list($row2, $col2) = getPosition($matrix, $secondChar);

                    if ($row1 == $row2) {
                        $plainText .= $matrix[$row1][($col1 + 4) % 5] . $matrix[$row2][($col2 + 4) % 5];
                    } elseif ($col1 == $col2) {
                        $plainText .= $matrix[($row1 + 4) % 5][$col1] . $matrix[($row2 + 4) % 5][$col2];
                    } else {
                        $plainText .= $matrix[$row1][$col2] . $matrix[$row2][$col1];
                    }
                }

                return $plainText;
            }

            // Mengambil input dari form
            $plaintext = $_POST['plaintext'];
            $keyword = $_POST['keyword'];

            echo "<h2>Hasil:</h2>";
            echo "Plaintext: $plaintext<br>";
            echo "Keyword: $keyword<br>";

            // Siapkan matriks Playfair
            $matrix = buildMatrix($keyword);

            // Enkripsi
            $ciphertext = encrypt($plaintext, $matrix);
            echo "Enkripsi Text: $ciphertext<br>";

            // Dekripsi
            $decryptedText = decrypt($ciphertext, $matrix);
            echo "Deskripsi Text: $decryptedText<br>";
        }
        ?>
    </div>
</body>
</html>
