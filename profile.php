<?php
session_start();

include 'db_connect.php';



// join the herb table with the saved list to show the names and images as well

function getSavedHerbs($conn, $userId) {
    $sql = "SELECT h.herbID, h.herbName, h.imagePath
            FROM savedlist sli
            JOIN herb h ON sli.herbID = h.herbID
            WHERE sli.userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$title = "Profile page";
include 'header.php';


?>

    <main class="container">
        
        <h1>Welcome, <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $userId = $_SESSION['userID'];
                $userNameSql = "SELECT name FROM user WHERE userID = ?";
                $userNameStmt = $conn->prepare($userNameSql);
                $userNameStmt->bind_param("s", $userId);
                $userNameStmt->execute();
                $userNameResult = $userNameStmt->get_result();
                if ($userNameRow = $userNameResult->fetch_assoc()) {
                    echo htmlspecialchars($userNameRow['name']);
                } else {
                    echo 'User'; //if no name found go with default User
                }
                $userNameStmt->close();
            } else {
                echo 'Guest'; //default for non-logged-in users but they shou;dnt see this page anyways
            }
            ?>!</h1>


        <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $userId = $_SESSION['userID'];
                $savedHerbs = getSavedHerbs($conn, $userId);

                if (!empty($savedHerbs)) {
                     echo "<h2>Your Saved Herbs</h2>";
                    foreach ($savedHerbs as $herb) {
                        echo "<div class='saved-herb-item'>";
                        echo "<img src='" . htmlspecialchars($herb['imagePath']) . "' alt='" . htmlspecialchars($herb['herbName']) . "'>";
                        echo "<div class='saved-herb-info'>";
                        // once clicked on, takes the user to that herb's page
                        echo "<h3><a href='herbDetails.php?id=" . htmlspecialchars($herb['herbID']) . "'>" . htmlspecialchars($herb['herbName']) . "</a></h3>";
                        echo "</div>";
                        echo "<div class='saved-herb-actions'>";
                        echo "<button onclick='unsaveHerb(" . htmlspecialchars($herb['herbID']) . ", this)'>Unsave</button>";
                  
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-saved-herbs'>You haven't saved any herbs yet.</p>";
                }

                echo "<div class='profile-actions'>";
                echo "<h2>Account Actions</h2>";
                echo "<button class='delete-profile-btn' onclick='confirmDeleteProfile()'>Delete Profile</button>";
                echo "</div>";
                

            } else {
                echo "<p>Please log in to see your saved herbs. <a href='login.php'>Login</a></p>";
            }
            ?>





    </main>

    <?php include 'footer.php';  ?>