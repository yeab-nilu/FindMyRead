# BookWise - Setup Instructions

## Quick Start Guide

Follow these simple steps to get BookWise running on your XAMPP server:

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP and start Apache and MySQL services
3. Open XAMPP Control Panel and ensure both Apache and MySQL are running

### Step 2: Set Up the Project
1. Copy the `book-recommendation-system` folder to `C:\xampp\htdocs\`
2. Open your web browser and go to `http://localhost/book-recommendation-system/setup.php`
3. Click through the setup process to create the database and sample data

### Step 3: Access the Application
1. Go to `http://localhost/book-recommendation-system/`
2. The application should load successfully
3. You can register a new account or use the test accounts

### Test Accounts
- **Username**: alice_reader, **Password**: password
- **Username**: bob_bookworm, **Password**: password
- **Username**: charlie_literature, **Password**: password
- **Username**: diana_analyst, **Password**: password

## Manual Database Setup (Alternative)

If the automatic setup doesn't work, follow these manual steps:

### Step 1: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create a new database
3. Name it `book_recommendation_db`
4. Set collation to `utf8mb4_unicode_ci`

### Step 2: Import Schema
1. Select the `book_recommendation_db` database
2. Click "Import" tab
3. Choose the `database/schema.sql` file
4. Click "Go" to execute

### Step 3: Import Sample Data
1. Choose the `database/seed.sql` file
2. Click "Go" to execute

### Step 4: Verify Setup
1. Go to `http://localhost/book-recommendation-system/`
2. The application should work correctly

## Troubleshooting

### Common Issues and Solutions

**Issue**: "Database connection failed"
- **Solution**: Ensure MySQL is running in XAMPP Control Panel
- Check database credentials in `config/database.php`

**Issue**: "Page not loading" or "404 Error"
- **Solution**: Ensure Apache is running in XAMPP Control Panel
- Verify project is in `C:\xampp\htdocs\book-recommendation-system\`

**Issue**: "Setup page shows errors"
- **Solution**: Check file permissions
- Ensure all files are properly uploaded
- Check XAMPP error logs

**Issue**: "Recommendations not working"
- **Solution**: Ensure sample data was imported correctly
- Check that users have reading history
- Verify database tables exist

**Issue**: "Styling looks broken"
- **Solution**: Clear browser cache
- Check that CSS files are accessible
- Verify file paths are correct

### File Structure Verification
Ensure your project has this structure:
```
book-recommendation-system/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── main.js
│       ├── recommendations.js
│       ├── reading-lists.js
│       └── search.js
├── config/
│   └── database.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── includes/
│   ├── header.php
│   └── footer.php
├── actions/
│   └── add-to-list.php
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── books.php
├── book-details.php
├── recommendations.php
├── reading-lists.php
├── analytics.php
├── logout.php
├── setup.php
└── README.md
```

## Features to Test

### 1. User Authentication
- [ ] Register a new account
- [ ] Login with existing account
- [ ] Logout functionality
- [ ] Password validation

### 2. Book Browsing
- [ ] View book listings
- [ ] Search for books
- [ ] Filter by genre
- [ ] Sort books by different criteria
- [ ] View book details

### 3. Reading Lists
- [ ] Add books to reading lists
- [ ] View different list types
- [ ] Remove books from lists
- [ ] Move books between lists

### 4. Recommendations
- [ ] Generate recommendations
- [ ] View recommendation reasons
- [ ] Add recommended books to lists
- [ ] Rate books

### 5. Analytics
- [ ] View reading statistics
- [ ] Check reading goals progress
- [ ] See genre preferences
- [ ] View recent activity

### 6. Responsive Design
- [ ] Test on desktop
- [ ] Test on tablet
- [ ] Test on mobile
- [ ] Check navigation menu

## System Requirements

- **XAMPP**: Version 7.4 or higher
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Browser**: Chrome, Firefox, Safari, or Edge
- **RAM**: Minimum 2GB
- **Storage**: 100MB free space

## Performance Tips

1. **Enable PHP Extensions**: Ensure PDO and PDO_MySQL are enabled
2. **Increase Memory Limit**: Set `memory_limit = 256M` in php.ini
3. **Enable Error Reporting**: For development, set `display_errors = On`
4. **Clear Cache**: Clear browser cache if experiencing issues

## Security Notes

- This is a development/demo system
- Default passwords are for testing only
- In production, use strong passwords and HTTPS
- Regular security updates recommended

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all files are in the correct locations
3. Ensure XAMPP services are running
4. Check XAMPP error logs for specific errors

## Success Indicators

You'll know the setup is successful when:
- ✅ Homepage loads without errors
- ✅ You can register and login
- ✅ Books are displayed correctly
- ✅ Search functionality works
- ✅ Recommendations generate
- ✅ Reading lists function properly
- ✅ Analytics display data

---

**BookWise** is now ready to use! Enjoy discovering your next favorite book! 📚✨
