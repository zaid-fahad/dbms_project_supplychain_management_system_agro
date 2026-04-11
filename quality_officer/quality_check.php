<?php
include "../db.php";

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_id = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;
    $moisture = isset($_POST['moisture']) && $_POST['moisture'] !== '' ? floatval($_POST['moisture']) : 'NULL';
    $purity = isset($_POST['purity']) && $_POST['purity'] !== '' ? floatval($_POST['purity']) : 'NULL';
    $grade = isset($_POST['grade']) ? $conn->real_escape_string(trim($_POST['grade'])) : '';
    $comments = isset($_POST['comments']) ? $conn->real_escape_string(trim($_POST['comments'])) : '';

    if ($batch_id && $grade) {
        $sql = "INSERT INTO Quality_Checks (batch_id, officer_id, quality_tag, moisture_content, purity, grade, comments) VALUES ($batch_id, 1, 'pending', " . ($moisture === 'NULL' ? 'NULL' : $moisture) . ", " . ($purity === 'NULL' ? 'NULL' : $purity) . ", '$grade', '$comments')";
        if ($conn->query($sql) === TRUE) {
            $message = 'Quality check submitted successfully.';
            $isSuccess = true;
        } else {
            $message = 'Error submitting quality check: ' . $conn->error;
        }
    } else {
        $message = 'Please select a batch and grade.';
    }
}

$pendingSql = "SELECT b.batch_id, b.batch_number, p.product_name, f.name AS farmer_name FROM Batches b LEFT JOIN Quality_Checks q ON b.batch_id = q.batch_id JOIN Farmers f ON b.farmer_id = f.farmer_id JOIN Products p ON b.product_id = p.product_id WHERE q.check_id IS NULL ORDER BY b.purchase_date DESC";
$pendingResult = $conn->query($pendingSql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quality Check - Quality Officer</title>
    <link rel="stylesheet" href="../style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    />
  </head>
  <body>
    <?php include '../components/topbar.html'; ?>
    <?php $page_title = 'Conduct Quality Check'; include '../components/header.html'; ?>

    <?php include 'components/nav.html'; ?>

    <main>
      <div class="card" style="max-width: 600px; margin: 0 auto">
        <div class="card-header">
          <span class="card-title">Quality Check Form</span>
        </div>
        <?php if ($message): ?>
          <div class="alert <?php echo $isSuccess ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
          </div>
        <?php endif; ?>
        <form method="post" action="quality_check.php">
          <div class="form-group">
            <label for="batch_id">Batch Number</label>
            <select id="batch_id" name="batch_id" required>
              <option value="">Select Batch</option>
              <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
                <?php while ($row = $pendingResult->fetch_assoc()): ?>
                  <option value="<?php echo $row['batch_id']; ?>"><?php echo htmlspecialchars($row['batch_number']); ?> (<?php echo htmlspecialchars($row['product_name']); ?> - <?php echo htmlspecialchars($row['farmer_name']); ?>)</option>
                <?php endwhile; ?>
              <?php else: ?>
                <option value="" disabled>No pending batches available</option>
              <?php endif; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="moisture">Moisture Content (%)</label>
            <input
              type="number"
              step="0.01"
              min="0"
              max="100"
              id="moisture"
              name="moisture"
              placeholder="Enter moisture %"
              required
            />
          </div>
          <div class="form-group">
            <label for="purity">Purity (%)</label>
            <input
              type="number"
              step="0.01"
              min="0"
              max="100"
              id="purity"
              name="purity"
              placeholder="Enter purity %"
              required
            />
          </div>
          <div class="form-group">
            <label for="grade">Grade</label>
            <select id="grade" name="grade" required>
              <option value="">Select Grade</option>
              <option value="A">Grade A (Premium)</option>
              <option value="B">Grade B (Standard)</option>
              <option value="C">Grade C (Low)</option>
            </select>
          </div>
          <div class="form-group">
            <label for="comments">Comments</label>
            <textarea
              id="comments"
              name="comments"
              rows="3"
              placeholder="Additional notes"
            ></textarea>
          </div>
          <button type="submit" class="btn-submit">
            <i class="fa fa-check"></i> Submit Quality Check
          </button>
        </form>
      </div>
    </main>
  </body>
</html>
