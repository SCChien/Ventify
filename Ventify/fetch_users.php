<?php
include('./core/conn.php');

$sql = "SELECT * FROM users";
$result = $conn->query($sql);

echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Password</th>
        <th>Telephone</th>
        <th>Email</th>
    </tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . $row['password'] . "</td>";
    echo "<td>" . $row['telephone'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
