# FindMyRead - Book Recommendation System

A comprehensive book recommendation website built with PHP and MySQL, featuring intelligent recommendation algorithms, user management, and reading analytics.

## 🎯 Project Overview

**FindMyRead** is a full-stack book recommendation system that helps users discover their next favorite book through intelligent algorithms, personalized analytics, and social features. The project demonstrates advanced web development skills with a modern, scalable architecture.

## 🏗️ Technical Architecture

### Frontend (Option 1: Pure HTML, CSS, JavaScript)
- **Technology Stack**: HTML5, CSS3, Vanilla JavaScript
- **Features**: Responsive design, modern UI/UX, real-time interactions
- **Design**: Professional, clean interface with smooth animations

### Backend (Option B: Direct Integration Approach)
- **Technology Stack**: PHP (without Laravel)
- **Database**: MySQL with comprehensive normalized schema
- **Architecture**: Direct PHP integration with HTML rendering
- **Security**: Password hashing, CSRF protection, input validation

## 🚀 Key Features

### 1. User Authentication & Management
- Secure user registration and login
- Profile management with reading preferences
- Password hashing with bcrypt
- Session management

### 2. Book Catalog & Search
- Comprehensive book database with 20+ sample books
- Advanced search functionality (title, author, genre, rating)
- Genre-based browsing with color-coded categories
- Book details with reviews and ratings

### 3. Smart Recommendation System
- **Content-based Recommendations**: Based on user's favorite genres
- **Collaborative Filtering**: Based on similar users' preferences
- **Popular Books**: Trending books in the community
- **Hybrid Algorithm**: Combined approach for best results
- **Confidence Scoring**: Algorithm confidence levels

### 4. Reading Analytics Dashboard
- Books read tracking and statistics
- Monthly and yearly reading goals
- Genre preference analysis
- Reading streak tracking
- Progress visualization

### 5. Review & Rating System
- 5-star rating system
- Detailed text reviews
- Review aggregation and statistics
- User review history

### 6. Personal Reading Lists
- Want to Read list
- Currently Reading list
- Read list
- Favorites list
- Easy list management

### 7. Social Features
- User interactions tracking
- Book sharing capabilities
- Community reviews
- Reading challenges

## 📊 Database Design

### Core Entities
1. **users** - User profiles and preferences
2. **books** - Comprehensive book catalog
3. **genres** - Book categorization
4. **reviews** - User reviews and ratings
5. **user_reading_lists** - Personal book collections
6. **book_recommendations** - AI-generated suggestions
7. **user_interactions** - User behavior tracking
8. **reading_analytics** - Reading statistics and goals
9. **book_clubs** - Social reading groups
10. **reading_challenges** - Gamification features

### Key Relationships
- Users can have multiple reviews and reading lists
- Books belong to genres and can have multiple reviews
- Recommendations link users to books based on algorithms
- Analytics track user reading patterns and goals

## 🛠️ Installation & Setup

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser (Chrome, Firefox, Safari, Edge)

### Setup Instructions

1. **Download and Install XAMPP**
   - Download XAMPP from https://www.apachefriends.org/
   - Install and start Apache and MySQL services

2. **Clone/Download the Project**
   ```bash
   # If using Git
   git clone <repository-url>
   # Or download and extract the ZIP file
   ```

3. **Place Project in XAMPP Directory**
   - Copy the `book-recommendation-system` folder to `C:\xampp\htdocs\`
   - The project should be accessible at `http://localhost/book-recommendation-system/`

4. **Set Up Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database called `book_recommendation_db`
   - Import the database schema:
     - Go to the `book_recommendation_db` database
     - Click "Import" tab
     - Choose `database/schema.sql` file
     - Click "Go" to execute
   - Import the seed data:
     - Choose `database/seed.sql` file
     - Click "Go" to execute

5. **Configure Database Connection**
   - Open `config/database.php`
   - Verify database credentials (should work with default XAMPP settings):
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'book_recommendation_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

6. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/book-recommendation-system/`
   - The application should load successfully

### Default User Accounts
- **Username**: alice_reader, **Password**: password
- **Username**: bob_bookworm, **Password**: password
- **Username**: charlie_literature, **Password**: password
- **Username**: diana_analyst, **Password**: password

## 📁 Project Structure

```
book-recommendation-system/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       ├── main.js            # Main JavaScript
│       ├── recommendations.js # Recommendation features
│       ├── reading-lists.js   # Reading list management
│       └── search.js          # Search functionality
├── config/
│   └── database.php           # Database configuration
├── database/
│   ├── schema.sql             # Database schema
│   └── seed.sql               # Sample data
├── includes/
│   ├── header.php             # Common header
│   └── footer.php             # Common footer
├── actions/                   # AJAX action handlers
├── pages/                     # Additional pages
├── index.php                  # Homepage
├── login.php                  # Login page
├── register.php               # Registration page
├── dashboard.php              # User dashboard
├── books.php                  # Books listing
├── recommendations.php        # Recommendation engine
├── reading-lists.php          # Reading list management
├── analytics.php              # Reading analytics
└── README.md                  # This file
```

## 🎨 User Interface Features

### Design Principles
- **Responsive Design**: Works on all device sizes
- **Modern UI**: Clean, intuitive interface with smooth animations
- **Performance**: Optimized loading and interactions

### Interactive Elements
- Dynamic content loading
- Real-time search with suggestions
- Interactive book cards with hover effects
- Progress tracking visualizations
- Smooth animations and transitions

## 🧠 Recommendation Algorithm

### Multi-Algorithm Approach
1. **Content-based Filtering**
   - Analyzes user's favorite genres
   - Recommends books in preferred categories
   - Confidence score: 0.8

2. **Collaborative Filtering**
   - Finds users with similar reading preferences
   - Recommends books liked by similar users
   - Confidence score: 0.6-0.9

3. **Popular Books**
   - Recommends highly-rated books in user's genres
   - Confidence score: 0.7

4. **Trending Books**
   - Recently added books with good ratings
   - Confidence score: 0.75

## 📈 Analytics Features

### Reading Statistics
- Total books read
- Monthly/yearly reading goals
- Reading streak tracking
- Genre preference analysis
- Average reading time

### Visualizations
- Progress bars for reading goals
- Genre distribution charts
- Reading timeline
- Achievement badges

## 🔒 Security Features

### Authentication
- Password hashing with bcrypt
- Session management
- CSRF token protection
- Input validation and sanitization

### Data Protection
- SQL injection prevention
- XSS protection
- Secure file uploads
- Input validation

## 🚀 Performance Optimizations

### Database
- Proper indexing for fast queries
- Efficient relationship design
- Query optimization
- Pagination support

### Frontend
- Lazy loading for images
- Minified CSS and JavaScript
- Efficient DOM manipulation
- Progressive enhancement

## 🎯 Real-World Application

### Problem Solved
- **Book Discovery**: Helps users find books they'll love
- **Reading Tracking**: Monitors reading habits and goals
- **Social Reading**: Connects readers with similar interests
- **Personalization**: Provides tailored recommendations

### Target Users
- Book enthusiasts and casual readers
- Students and educators
- Book clubs and reading groups
- Libraries and bookstores

### Business Value
- User engagement through personalization
- Data-driven insights for recommendations
- Social features for community building
- Analytics for user behavior understanding

## 🧪 Testing

### Manual Testing Checklist
- [ ] User registration and login
- [ ] Book search and filtering
- [ ] Recommendation generation
- [ ] Reading list management
- [ ] Review and rating system
- [ ] Analytics dashboard
- [ ] Responsive design on mobile
- [ ] Cross-browser compatibility

### Test Data
- 20+ sample books across 15 genres
- 4 sample user accounts
- Sample reviews and ratings
- Reading list data
- Recommendation data

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure XAMPP MySQL is running
   - Check database credentials in `config/database.php`
   - Verify database exists and is imported

2. **Page Not Loading**
   - Ensure XAMPP Apache is running
   - Check file permissions
   - Verify project is in correct directory

3. **Recommendations Not Working**
   - Ensure user has reading history
   - Check database has sample data
   - Verify recommendation algorithm is running

4. **Styling Issues**
   - Clear browser cache
   - Check CSS file path
   - Verify file permissions

## 📚 API Documentation

### Available Endpoints
- `GET /books.php` - List all books
- `GET /book-details.php?id={id}` - Get book details
- `POST /actions/add-to-list.php` - Add book to reading list
- `POST /actions/rate-book.php` - Rate a book
- `GET /recommendations.php` - Get user recommendations

### Response Format
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {...}
}
```

## 🔮 Future Enhancements

### Immediate Improvements
- Mobile app development
- Advanced search filters
- Book cover image integration
- Email notifications
- Social sharing features

### Long-term Features
- Machine learning integration
- Third-party book API integration
- Advanced social features
- E-commerce integration
- Reading challenges and gamification

## 📄 License

This project is created for educational purposes. All rights reserved.

## 👥 Contributing

This is a final project submission. For educational use only.

## 📞 Support

For technical support or questions about this project, please refer to the documentation or contact the development team.

---

**FindMyRead** - Discover your next favorite book! 📚✨
