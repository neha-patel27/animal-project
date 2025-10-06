<?php include 'db.php'; ?>

<?php
// Count visitors
$conn->query("INSERT INTO visitors () VALUES ()");
$visitors = $conn->query("SELECT COUNT(*) as total FROM visitors");
$count = $visitors->fetch_assoc()['total'];

// Build filters and sort
$where = [];
$order = "ORDER BY created_at DESC";

if (!empty($_GET['category'])) {
  $where[] = "category = '" . $conn->real_escape_string($_GET['category']) . "'";
}

if (!empty($_GET['life'])) {
  $where[] = "life_expectancy = '" . $conn->real_escape_string($_GET['life']) . "'";
}

if (!empty($_GET['sort'])) {
  if ($_GET['sort'] == 'alpha') $order = "ORDER BY name ASC";
  elseif ($_GET['sort'] == 'date') $order = "ORDER BY created_at DESC";
}

$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM animals $whereClause $order";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Animal Listing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f5f6fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h1 {
      text-align: center;
      color: #2d3436;
    }

    p {
      text-align: center;
      font-size: 16px;
      color: #636e72;
    }

    .actions {
      text-align: center;
      margin-bottom: 20px;
    }

    .actions a {
      display: inline-block;
      background: #00b894;
      color: white;
      padding: 10px 15px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 15px;
      transition: background 0.3s;
    }

    .actions a:hover {
      background: #55efc4;
    }

    form {
      text-align: center;
      margin-bottom: 25px;
    }

    select, button {
      padding: 8px 12px;
      margin: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    button {
      background: #0984e3;
      color: white;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #74b9ff;
    }

    .animal-card {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .animal-card img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }

    .animal-details {
      flex: 1;
      min-width: 200px;
    }

    .animal-details strong {
      font-size: 18px;
      color: #2d3436;
    }

    .animal-details p {
      color: #636e72;
      margin: 6px 0;
    }

    .animal-actions {
      margin-top: 10px;
    }

    .animal-actions a {
      text-decoration: none;
      margin-right: 10px;
      color: #0984e3;
      font-weight: bold;
    }

    .animal-actions a:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      .animal-card {
        flex-direction: column;
        align-items: flex-start;
      }

      .animal-card img {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
      }

      .animal-actions {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
      }

      .animal-actions a {
        margin-bottom: 6px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🐾 Animal Listing</h1>
    <p>Visitor Count: <strong><?= $count ?></strong></p>

    <div class="actions">
      <a href="submission.php">➕ Add New Animal</a>
    </div>

    <form method="GET">
      <label>Category:</label>
      <select name="category">
        <option value="">All</option>
        <option <?= ($_GET['category'] ?? '') == 'Herbivore' ? 'selected' : '' ?>>Herbivore</option>
        <option <?= ($_GET['category'] ?? '') == 'Omnivore' ? 'selected' : '' ?>>Omnivore</option>
        <option <?= ($_GET['category'] ?? '') == 'Carnivore' ? 'selected' : '' ?>>Carnivore</option>
      </select>

      <label>Life Expectancy:</label>
      <select name="life">
        <option value="">All</option>
        <option <?= ($_GET['life'] ?? '') == '0-1 year' ? 'selected' : '' ?>>0-1 year</option>
        <option <?= ($_GET['life'] ?? '') == '1-5 years' ? 'selected' : '' ?>>1-5 years</option>
        <option <?= ($_GET['life'] ?? '') == '5-10 years' ? 'selected' : '' ?>>5-10 years</option>
        <option <?= ($_GET['life'] ?? '') == '10+ years' ? 'selected' : '' ?>>10+ years</option>
      </select>

      <label>Sort By:</label>
      <select name="sort">
        <option value="">--Select--</option>
        <option value="date" <?= ($_GET['sort'] ?? '') == 'date' ? 'selected' : '' ?>>Date</option>
        <option value="alpha" <?= ($_GET['sort'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alphabetical</option>
      </select>

      <button type="submit">Apply</button>
    <a href="index.php"><button type="button" class="reset-btn">Reset</button></a>
    </form>

    <hr>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="animal-card">
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="animal-details">
            <strong><?= htmlspecialchars($row['name']) ?></strong><br>
            <p>Category: <?= htmlspecialchars($row['category']) ?></p>
            <p>Life Expectancy: <?= htmlspecialchars($row['life_expectancy']) ?></p>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <div class="animal-actions">
              <a href="edit.php?id=<?= $row['id'] ?>">✏️ Edit</a>
              <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this animal?');">🗑️ Delete</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">No animals found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
