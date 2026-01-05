<?php
// search_handler.php
include 'db_connect.php';

$query = isset($_POST['query']) ? $_POST['query'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';

$sql = "SELECT * FROM tbl_items WHERE 1=1";
$params = [];
$types = "";

if (!empty($query)) {
    $sql .= " AND (ItemName LIKE ? OR Description LIKE ? OR Location LIKE ?)";
    $searchTerm = "%" . $query . "%";
    $params[] = $searchTerm; $params[] = $searchTerm; $params[] = $searchTerm;
    $types .= "sss";
}

if (!empty($type)) {
    $sql .= " AND Type = ?";
    $params[] = $type;
    $types .= "s";
}

if (!empty($category)) {
    $sql .= " AND CategoryID = ?";
    $params[] = $category;
    $types .= "i";
}

$sql .= " ORDER BY DateLostFound DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['ID'];
        $name = htmlspecialchars($row['ItemName']);
        $loc = htmlspecialchars($row['Location']);
        $desc = htmlspecialchars($row['Description']);
        $date = date("M d, Y", strtotime($row['DateLostFound']));
        $imagePath = "uploads/" . htmlspecialchars($row['Image']);
        $itemType = htmlspecialchars($row['Type']);

        $badgeClass = ($itemType == 'Found') ? 'status-badge found' : 'status-badge lost';
        $btnText = ($itemType == 'Found') ? 'Is this yours?' : 'I found this!';
        
        if (empty($row['Image']) || !file_exists($imagePath)) $imagePath = "https://via.placeholder.com/400x300?text=No+Image";

        $iconCalendar = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>';
        $iconMap = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';

        // NOTE: The HTML structure here MUST match browse.php exactly
        echo '
        <div class="item-card">
            <div class="card-image-wrapper">
                <span class="'.$badgeClass.'">'.$itemType.'</span>
                <img src="'.$imagePath.'" alt="'.$name.'" class="card-image">
            </div>
            <div class="card-content">
                <h3 class="item-title">'.$name.'</h3>
                <div class="meta-row">
                    <div class="meta-item">'.$iconCalendar . ' ' . $date.'</div>
                    <div class="meta-item">'.$iconMap . ' ' . $loc.'</div>
                </div>
                <p class="item-desc">'.$desc.'</p>
                
                <div class="button-group">
                    <button class="btn-quick-view" 
                        data-id="'.$id.'"
                        data-name="'.$name.'"
                        data-type="'.$itemType.'"
                        data-date="'.$date.'"
                        data-loc="'.$loc.'"
                        data-desc="'.$desc.'"
                        data-img="'.$imagePath.'"
                        onclick="openQuickView(this)">
                        View
                    </button>
                    
                    <button class="btn-claim" onclick="openClaimModal('.$id.')">
                        '.$btnText.'
                    </button>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;"><h3>No items found</h3><p>Try adjusting your filters.</p></div>';
}
$stmt->close();
$conn->close();
?>