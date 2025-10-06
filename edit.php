<?php include 'db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM animals WHERE id=$id");
$animal = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $description = $_POST['description'];
  $life = $_POST['life'];
  $image = $animal['image'];

  if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
  }

  $stmt = $conn->prepare("UPDATE animals SET name=?, category=?, image=?, description=?, life_expectancy=? WHERE id=?");
  $stmt->bind_param("sssssi", $name, $category, $image, $description, $life, $id);
  $stmt->execute();

  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Animal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- ✅ Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 600px;">
      <h2 class="text-center mb-4">✏️ Edit Animal Information</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Name of Animal</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($animal['name']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Category</label><br>
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="category" value="Herbivore" <?= $animal['category']=='Herbivore'?'checked':'' ?>>
            <label class="form-check-label">Herbivore</label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="category" value="Omnivore" <?= $animal['category']=='Omnivore'?'checked':'' ?>>
            <label class="form-check-label">Omnivore</label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="category" value="Carnivore" <?= $animal['category']=='Carnivore'?'checked':'' ?>>
            <label class="form-check-label">Carnivore</label>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($animal['description']) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Life Expectancy</label>
          <select name="life" class="form-select">
            <option <?= $animal['life_expectancy']=='0-1 year'?'selected':'' ?>>0-1 year</option>
            <option <?= $animal['life_expectancy']=='1-5 years'?'selected':'' ?>>1-5 years</option>
            <option <?= $animal['life_expectancy']=='5-10 years'?'selected':'' ?>>5-10 years</option>
            <option <?= $animal['life_expectancy']=='10+ years'?'selected':'' ?>>10+ years</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" name="image" class="form-control">
          <small class="text-muted d-block mt-2">
            Current Image: <?= htmlspecialchars($animal['image']) ?>
          </small>
          <?php if (!empty($animal['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($animal['image']) ?>" class="img-thumbnail mt-2" style="max-height: 150px;">
          <?php endif; ?>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg">Update Animal</button>
        </div>
      </form>
      <div class="text-center mt-3">
        <a href="index.php" class="btn btn-link">⬅️ Back to List</a>
      </div>
    </div>
  </div>
</body>
</html>
