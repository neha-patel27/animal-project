<?php
session_start();
include 'db.php';

// helper to generate captcha and store both answer and question in session
function gen_captcha() {
    $n1 = rand(1, 9);
    $n2 = rand(1, 9);
    $_SESSION['captcha']   = $n1 + $n2;
    $_SESSION['captcha_q'] = "$n1 + $n2 = ?";
}

if (!isset($_SESSION['captcha']) || !isset($_SESSION['captcha_q'])) {
    gen_captcha();
}

$captcha_question = $_SESSION['captcha_q'];
$error = '';
$old = ['name'=>'', 'category'=>'', 'description'=>'', 'life'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']        = trim($_POST['name'] ?? '');
    $old['category']    = $_POST['category'] ?? '';
    $old['description'] = trim($_POST['description'] ?? '');
    $old['life']        = $_POST['life'] ?? '';
    $captcha_input      = trim($_POST['captcha'] ?? '');

    if ($captcha_input === '' || !is_numeric($captcha_input) || intval($captcha_input) !== intval($_SESSION['captcha'])) {
        $error = '❌ Wrong captcha. Please try again.';
        gen_captcha();
        $captcha_question = $_SESSION['captcha_q'];
    } else {
        $imageName = '';
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $orig = basename($_FILES['image']['name']);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
            $imageName = time() . '_' . $safe;
            $target = $upload_dir . $imageName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $error = 'Failed to upload image.';
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare(
                "INSERT INTO animals (name, category, image, description, life_expectancy) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $old['name'], $old['category'], $imageName, $old['description'], $old['life']);
            $stmt->execute();
            $stmt->close();

            unset($_SESSION['captcha'], $_SESSION['captcha_q']);
            header("Location: index.php");
            exit();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Animal Submission</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f5f6fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2d3436;
    }

    form label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      color: #333;
    }

    input[type="text"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 6px;
      box-sizing: border-box;
      font-size: 15px;
    }

    textarea {
      resize: vertical;
    }

    input[type="file"] {
      margin-top: 10px;
    }

    .radio-group label {
      margin-right: 15px;
      font-weight: normal;
    }

    .error {
      background: #ffe6e6;
      color: #d63031;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
    }

    input[type="submit"] {
      width: 100%;
      background: #0984e3;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      margin-top: 20px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: #74b9ff;
    }

    a {
      text-decoration: none;
      color: #0984e3;
      display: inline-block;
      margin-top: 15px;
      text-align: center;
      width: 100%;
    }

    @media (max-width: 600px) {
      .container {
        margin: 20px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🐾 Submit Animal Information</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label>Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($old['name']) ?>" required>

      <label>Category:</label>
      <div class="radio-group">
        <label><input type="radio" name="category" value="Herbivore" <?= $old['category']=='Herbivore' ? 'checked' : '' ?>> Herbivore</label>
        <label><input type="radio" name="category" value="Omnivore" <?= $old['category']=='Omnivore' ? 'checked' : '' ?>> Omnivore</label>
        <label><input type="radio" name="category" value="Carnivore" <?= $old['category']=='Carnivore' ? 'checked' : '' ?>> Carnivore</label>
      </div>

      <label>Image:</label>
      <input type="file" name="image" accept="image/*" <?= empty($old['name']) ? 'required' : '' ?>>

      <label>Description:</label>
      <textarea name="description" rows="4" required><?= htmlspecialchars($old['description']) ?></textarea>

      <label>Life Expectancy:</label>
      <select name="life" required>
        <option value="">--Select--</option>
        <option <?= $old['life']=='0-1 year' ? 'selected' : '' ?>>0-1 year</option>
        <option <?= $old['life']=='1-5 years' ? 'selected' : '' ?>>1-5 years</option>
        <option <?= $old['life']=='5-10 years' ? 'selected' : '' ?>>5-10 years</option>
        <option <?= $old['life']=='10+ years' ? 'selected' : '' ?>>10+ years</option>
      </select>

      <label>Captcha: <?= htmlspecialchars($captcha_question) ?></label>
      <input type="text" name="captcha" required>

      <input type="submit" value="Submit">
    </form>

    <a href="index.php">⬅️ Go to Animal Listing</a>
  </div>
</body>
</html>
