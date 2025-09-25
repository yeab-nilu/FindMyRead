<?php
// FindMyRead Setup Script
// Run this file to set up the database and initial data

require_once 'config/database.php';

echo "<h1>FindMyRead Setup</h1>";
echo "<p>Setting up database and initial data...</p>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Read and execute schema
    $schema = file_get_contents('database/schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    echo "<p style='color: green;'>✓ Database schema created</p>";
    
    // Read and execute seed data
    $seed = file_get_contents('database/seed.sql');
    $statements = explode(';', $seed);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    echo "<p style='color: green;'>✓ Sample data inserted</p>";
    
    // Create additional directories if needed
    $directories = ['actions', 'pages', 'uploads'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "<p style='color: green;'>✓ Created directory: $dir</p>";
        }
    }
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>Your FindMyRead application is ready to use.</p>";
    echo "<p><a href='index.php' style='background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to FindMyRead</a></p>";
    
    echo "<h3>Default User Accounts:</h3>";
    echo "<ul>";
    echo "<li><strong>alice_reader</strong> / password</li>";
    echo "<li><strong>bob_bookworm</strong> / password</li>";
    echo "<li><strong>charlie_literature</strong> / password</li>";
    echo "<li><strong>diana_analyst</strong> / password</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your XAMPP configuration and try again.</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
    padding: 10px;
    background: white;
    border-radius: 5px;
    border-left: 4px solid #ddd;
}
</style>
