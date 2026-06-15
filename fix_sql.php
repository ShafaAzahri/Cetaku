<?php
$sql = file_get_contents('database cetakufinal.sql');

// Match ALTER TABLE statements modifying a column to AUTO_INCREMENT
// Example: ALTER TABLE `alamats` MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
preg_match_all("/ALTER TABLE `([^`]+)`\s+MODIFY `([^`]+)`([^,]+)AUTO_INCREMENT/", $sql, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    $table = $match[1];
    $column = $match[2];

    // Find the CREATE TABLE statement for this table, and find the column definition to append AUTO_INCREMENT
    // Example: CREATE TABLE `alamats` (\n  `id` bigint NOT NULL,
    $pattern = "/(CREATE TABLE `$table` \([^;]*?`$column`\s+[^,\n]+)(,|\n)/is";
    $sql = preg_replace($pattern, "$1 AUTO_INCREMENT$2", $sql, 1);
}

// Remove the ALTER TABLE MODIFY blocks
$sql = preg_replace("/--\s*AUTO_INCREMENT for table [^\n]+\s*--\s*ALTER TABLE `[^`]+`\s*MODIFY `[^`]+`[^;]+;/is", "", $sql);

// Also remove "-- AUTO_INCREMENT for dumped tables" header
$sql = preg_replace("/--\s*AUTO_INCREMENT for dumped tables\s*--/is", "", $sql);

file_put_contents('database_cetakufinal_fixed.sql', $sql);
echo "Database SQL fixed for TiDB!\n";
