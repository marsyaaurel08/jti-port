# Product Requirements Document (PRD)
# PPI Student Management System

**Version:** 1.0  
**Date:** December 2024  
**Document Owner:** PPI Management Team  
**Target Launch:** February 2025

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Problem Definition](#2-problem-definition)
3. [Goals & Success Metrics](#3-goals--success-metrics)
4. [User Personas & Stories](#4-user-personas--stories)
5. [Functional Requirements](#5-functional-requirements)
6. [Non-Functional Requirements](#6-non-functional-requirements)
7. [User Experience & Workflows](#7-user-experience--workflows)
8. [Technical Considerations](#8-technical-considerations)
9. [Timeline & Phases](#9-timeline--phases)
10. [Risks & Mitigation](#10-risks--mitigation)
11. [Appendix](#11-appendix)

---

## 1. Executive Summary

### 1.1 Overview

This document outlines the requirements for developing a comprehensive web-based student management system for **PPI (Perhimpunan Pelajar Indonesia)**, the Indonesian Student Association in Japan. The system aims to replace fragmented Excel-based processes with a centralized platform that improves data management, enhances communication, and creates a sustainable knowledge base for current and future members.

### 1.2 Problem Statement

PPI currently manages student data, housing information, graduate tracking, and communications through disconnected Excel spreadsheets maintained by different administrators. This approach leads to:

- **Data duplication** when leadership changes
- **Information loss** during transitions
- **Inefficient communication** with expired email systems
- **Inability to provide demographic insights** for targeted programs
- **Repetitive manual work** answering the same questions from new students

### 1.3 Proposed Solution

A web-based application built with **Laravel 12 + Filament 4** starter kit that centralizes:
- Student registration and profile management
- Housing/apartment database with peer reviews
- Graduate tracking with interactive global map
- News/bulletin system with email broadcasting
- Demographic analytics and reporting
- Financial transparency features

**Why This Starter Kit?**

This Laravel 12 + Filament 4 starter kit provides 60% of the required functionality out-of-the-box, including:
- ✅ Complete admin panel with modern Filament 4 UI
- ✅ Role-based access control (Filament Shield v4) pre-configured
- ✅ User authentication with 2FA support (Filament Breezy)
- ✅ File upload and media management (Spatie Media Library)
- ✅ Social login (Google, GitHub, Facebook via Filament Socialite)
- ✅ Import/Export capabilities built-in
- ✅ Activity logging (Spatie Activity Log)
- ✅ User impersonation for support

This dramatically reduces development time from 18 weeks to **13 weeks** (5 weeks faster) and saves approximately **$7,500-9,500** in development costs, making the February 2025 deadline achievable.

### 1.4 Key Success Metrics

- **100% student data migration** from Excel to the system by February 2025
- **Reduce repetitive inquiries** from new students by 70%
- **Enable demographic reporting** for targeted communications and funding
- **Achieve 80% active user adoption** within first 3 months
- **Zero data loss** during leadership transitions

---

## 2. Problem Definition

### 2.1 Current State Analysis

Based on user interviews, the following pain points were identified:

#### **Pain Point 1: Data Chaos & Duplication**
- Each administrator creates separate Excel sheets
- Data gets duplicated when leadership changes
- No centralized, organized system
- Current database is nearly empty or inconsistent

#### **Pain Point 2: Lack of Demographic Insights**
- Cannot answer how many students are in specific majors/faculties
- Difficult to target communications (e.g., faculty-specific seminars)
- Cannot efficiently distribute aid (e.g., COVID relief funds)
- Cannot provide accurate data to sponsors/partners (e.g., WINSA collaboration)

#### **Pain Point 3: Manual Graduate Posting Process**
- Want to post each graduate individually on social media
- Cannot execute because there's no organized structure
- Must manually request photos from each graduate
- Time-consuming and cumbersome

#### **Pain Point 4: Housing Information Gap**
- Students complain after choosing apartments
- No prior reviews or information available
- Students make blind decisions without seeing properties
- Very scattered locations (Cijeruk no longer central gathering point)
- Need location sharing for emergency/community purposes

#### **Pain Point 5: Repetitive Questions from New Students**
- Same questions asked every year
- No FAQ or centralized resource
- Manual process to connect new students with seniors
- Time-consuming for administrators

#### **Pain Point 6: Communication System Failure**
- Email broadcast subscription expired
- Cannot send newsletters to members
- Lost important communication channel
- "Press release" content exists but no distribution method

#### **Pain Point 7: No Alumni Network Visibility**
- Don't know where alumni are located globally
- Cannot track alumni achievements
- Missing opportunity to showcase success stories
- No data on alumni publications, jobs, or research

### 2.2 Impact & Urgency

**High Impact Areas:**
- **Student Safety:** Location sharing for scattered housing helps emergency response
- **Resource Allocation:** Demographic data enables targeted aid distribution
- **Institutional Knowledge:** Prevents information loss during leadership transitions
- **Community Building:** Connects new students with seniors and alumni network

**Urgency:** February 2025 deadline for new student intake season

---

## 3. Goals & Success Metrics

### 3.1 Business Goals

1. **Centralize Data Management:** Single source of truth for all student information
2. **Improve Operational Efficiency:** Reduce manual administrative work by 60%
3. **Enable Data-Driven Decisions:** Provide demographic insights for programs and partnerships
4. **Enhance Communication:** Restore email broadcasting and create bulletin system
5. **Build Institutional Memory:** Ensure knowledge persists across leadership changes

### 3.2 User Goals

**For Administrators:**
- Easily manage student registrations and updates
- Generate reports on demographics and statistics
- Broadcast communications to targeted groups
- Track activities and publish bulletins

**For Current Students:**
- Find housing with peer reviews
- Connect with seniors in their field
- Access organizational news and updates
- Update their profile information

**For New/Prospective Students:**
- Self-register with complete information
- Browse housing options with reviews
- Find mentors/seniors in their program
- Access FAQ and resources

**For Alumni:**
- Maintain connection with community
- Showcase achievements on global map
- Mentor current students

### 3.3 Key Performance Indicators (KPIs)

| Metric | Target | Measurement Method |
|--------|--------|-------------------|
| Student Profile Completion Rate | 95% | Profiles with all required fields filled |
| Housing Review Submissions | 50+ reviews in 6 months | Number of apartment reviews submitted |
| Email Broadcast Open Rate | 40% | Email analytics tracking |
| New Student Self-Registration | 90% | Registration without admin intervention |
| User Login Frequency | 2x per month avg | Analytics tracking |
| Admin Time Saved | 60% reduction | Time tracking comparison |
| Demographics Report Generation | < 5 minutes | System performance metric |

---

## 4. User Personas & Stories

### 4.1 User Personas

#### **Persona 1: Administrator (Pengurus)**
**Name:** Budi  
**Role:** Current PPI Administrator  
**Tech Savvy:** Medium  
**Goals:**
- Manage student registrations efficiently
- Generate demographic reports for sponsors
- Publish news and bulletins
- Reduce manual repetitive work

**Pain Points:**
- Spending hours on Excel spreadsheets
- Answering same questions repeatedly
- Cannot provide accurate statistics to partners
- Fear of data loss when term ends

#### **Persona 2: New Student**
**Name:** Siti  
**Age:** 22  
**Status:** Incoming graduate student  
**Goals:**
- Find safe, affordable housing
- Connect with seniors in her program (Engineering)
- Understand PPI community and activities
- Self-register without hassle

**Pain Points:**
- Doesn't know where to live
- Cannot find upperclassmen to ask questions
- Overwhelmed with information
- Worried about making wrong housing decision

#### **Persona 3: Current Student**
**Name:** Andi  
**Year:** 2nd year undergraduate  
**Goals:**
- Help new students with housing advice
- Stay updated on PPI activities
- Find study partners in same faculty
- Share apartment experience

**Pain Points:**
- No platform to share housing reviews
- Difficult to find peers in same major
- Miss important announcements
- Cannot contribute to community knowledge

#### **Persona 4: Alumni/Graduate**
**Name:** Dewi  
**Status:** Graduated 2023, now working in Singapore  
**Goals:**
- Stay connected with PPI community
- Showcase career path for current students
- Mentor juniors in her field
- Give back to community

**Pain Points:**
- Lost touch with community after graduation
- No way to share success story
- Want to help but no platform to do so

### 4.2 User Stories

#### **High Priority (Must Have)**

**Student Registration**
- As a new student, I want to self-register with my photo and location so that I can join the PPI community immediately
- As an administrator, I want to view complete student profiles so that I can manage the community effectively
- As an administrator, I want to generate demographic reports (by scholarship type, faculty, batch) so that I can provide data to partners

**Housing Database**
- As a new student, I want to browse apartments with reviews from other students so that I can make informed housing decisions
- As a current student, I want to submit reviews and ratings for my apartment so that I can help future students
- As an administrator, I want to see a map of where students live so that I can understand geographic distribution

**Communication System**
- As an administrator, I want to send email broadcasts to all members so that I can share important announcements
- As an administrator, I want to publish news bulletins on the website so that members can stay informed
- As a student, I want to receive email notifications for important updates so that I don't miss critical information

#### **Medium Priority (Should Have)**

**Student Directory**
- As a new student, I want to search for seniors by major/faculty so that I can ask program-specific questions
- As a student, I want to update my own profile information so that my details stay current
- As an administrator, I want to track scholarship types so that we can report to HUI and other organizations

**Graduate Tracking**
- As an administrator, I want to track graduate locations on a world map so that we can showcase our alumni network
- As a graduate, I want to share my post-graduation path (job, research, publications) so that I can mentor current students
- As a student, I want to see where alumni work so that I can explore career possibilities

**Activity Management**
- As an administrator, I want to post event photos and reports so that we can maintain institutional memory
- As a member, I want to download event reports and photos so that I can reference past activities

#### **Lower Priority (Could Have)**

**Advanced Features**
- As an administrator, I want to track financial transparency reports so that members can see budget allocation
- As a student, I want to filter apartment reviews by criteria (price, location, facilities) so that I can find the best match
- As a member, I want to receive personalized content based on my major/interests so that communications are more relevant
- As an administrator, I want to generate automated social media posts for graduates so that we can celebrate achievements consistently

#### **Out of Scope (Won't Have - Version 1)**

- Event registration and ticketing system
- Payment processing for membership fees
- Mobile native applications (iOS/Android)
- Integration with university systems
- Real-time chat/messaging between members
- Document management system for internal files

---

## 5. Functional Requirements

### 5.1 Student Registration & Profile Management

#### **FR-1.1: Student Self-Registration (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Students can create accounts with email verification
- Registration form captures: Full name, email, phone, program/major, faculty, university, batch year, scholarship type (if applicable)
- Photo upload (profile picture) - required field
- Home location/address - required field with map integration
- Validation for required fields
- Duplicate email detection

**Acceptance Criteria:**
- Registration completes in < 3 minutes
- Photo uploads support JPG, PNG up to 5MB
- Location can be entered as address or selected on map
- Confirmation email sent within 1 minute

#### **FR-1.2: Student Profile Directory (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Searchable directory of all students
- Filter by: Faculty, Major, Batch Year, Scholarship Type
- Each profile shows: Name, Photo, Faculty, Major, Batch, Contact (with privacy controls)
- Privacy settings allow students to hide contact information
- Admin-only access to full data including location

**Acceptance Criteria:**
- Search returns results in < 2 seconds
- Students can toggle contact visibility
- Admins can export filtered lists to CSV

#### **FR-1.3: Demographic Dashboard (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Visual dashboard showing:
  - Total student count
  - Breakdown by scholarship type (Mombukagakusho vs others)
  - Breakdown by faculty
  - Breakdown by batch year
  - Geographic distribution map
- Export capabilities for reports
- Real-time updates when data changes

**Acceptance Criteria:**
- Dashboard loads in < 3 seconds
- Charts are interactive and filterable
- PDF export generates in < 10 seconds

### 5.2 Housing/Apartment Management

#### **FR-2.1: Apartment Database (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Database of apartments/housing locations
- Each listing includes: Name, Address, Map location, Photos (optional), Average rent range
- Apartments marked by student submissions
- Map view showing all apartments
- List view with sorting options

**Acceptance Criteria:**
- Add new apartment in < 2 minutes
- Map displays accurately with pins
- Support for at least 100 apartment listings

#### **FR-2.2: Apartment Reviews & Ratings (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Students can submit reviews for apartments they've lived in
- Review includes: Star rating (1-5), Written review, Photos (optional), Pros/Cons, Move-in/move-out dates
- Multiple reviews per apartment
- Review moderation by admins (approve/reject)
- Average rating displayed prominently

**Acceptance Criteria:**
- Review submission in < 5 minutes
- Reviews display chronologically
- Admin can moderate within dashboard
- Photos upload up to 10 per review, 5MB each

#### **FR-2.3: Apartment Search & Filtering (SHOULD HAVE)**
**Priority:** Medium

**Requirements:**
- Filter apartments by: Location/area, Price range, Rating (minimum stars), Facilities (keywords)
- Sort by: Rating, Price, Distance from campus, Most reviewed
- Save favorite apartments for later

**Acceptance Criteria:**
- Filter results update in < 2 seconds
- Can apply multiple filters simultaneously
- Favorites persist across sessions

### 5.3 Communication & News System

#### **FR-3.1: News/Bulletin Publishing (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Admin can create and publish news articles/bulletins
- Article includes: Title, Content (rich text), Featured image, Publication date, Category tags
- Homepage displays 3 most recent bulletins
- Archive page shows all bulletins with pagination
- Individual bulletin detail pages

**Acceptance Criteria:**
- Publish article in < 5 minutes
- Support rich text formatting (bold, italic, links, images)
- Homepage updates immediately after publish
- Archive supports 1000+ articles

#### **FR-3.2: Email Broadcasting (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Admin can compose and send email to all members
- Target specific groups: By faculty, By batch, By scholarship type, All members
- Email includes: Subject, HTML content, Attachments (optional)
- Track email metrics: Sent count, Open rate, Click rate
- Schedule emails for future sending

**Acceptance Criteria:**
- Send to 500 recipients in < 5 minutes
- Email deliverability > 95%
- Track opens and clicks
- Support email templates for common announcements

#### **FR-3.3: Downloadable Content (SHOULD HAVE)**
**Priority:** Medium

**Requirements:**
- Attach PDF/documents to bulletins
- Members can download event reports, photos, documents
- File storage with organization by category
- Search functionality for documents

**Acceptance Criteria:**
- Support files up to 50MB
- Download initiates immediately on click
- Search returns relevant documents in < 2 seconds

### 5.4 Graduate/Alumni Tracking

#### **FR-4.1: Graduate Profile Management (SHOULD HAVE)**
**Priority:** Medium

**Requirements:**
- Mark students as graduates with graduation year
- Graduate profiles include: Current location/country, Current occupation/employer, Research focus/publications (optional), LinkedIn/professional links (optional)
- Admin can update graduate information
- Graduates can self-update if logged in

**Acceptance Criteria:**
- Mark as graduate in < 1 minute
- Profile updates reflect immediately
- Support for 500+ graduate profiles

#### **FR-4.2: Global Alumni Map (SHOULD HAVE)**
**Priority:** Medium

**Requirements:**
- Interactive world map showing graduate locations
- Pin for each country with graduate count
- Click pin to see list of graduates in that country
- Filter by: Graduation year, Field/major, Current occupation type
- Visual statistics on most popular destinations

**Acceptance Criteria:**
- Map loads in < 3 seconds
- Smooth zoom and pan interactions
- Support for 50+ countries
- Click country pin shows graduate list immediately

#### **FR-4.3: Graduate Social Media Integration (COULD HAVE)**
**Priority:** Low

**Requirements:**
- Auto-generate social media posts for new graduates
- Template includes: Graduate photo, Name, Major, Achievement, Destination
- Admin can review and approve before posting
- Track which graduates have been posted

**Acceptance Criteria:**
- Generate post image in < 10 seconds
- Support Instagram and Facebook formats
- Image quality suitable for social media (1080x1080px minimum)

### 5.5 Financial Transparency

#### **FR-5.1: Financial Reports Publishing (COULD HAVE)**
**Priority:** Low

**Requirements:**
- Admin can upload financial reports (PDF)
- Display reports on dedicated transparency page
- Organize by fiscal year
- Include expense breakdowns and income sources

**Acceptance Criteria:**
- Upload report in < 2 minutes
- Reports listed chronologically
- Download counts tracked

### 5.6 Administrative Functions

#### **FR-6.1: User Management (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Admin dashboard for user management
- Roles: Super Admin, Admin, Member
- Admins can: Add/edit/delete users, Reset passwords, Change user roles, View activity logs
- Bulk operations support (e.g., bulk email, bulk export)

**Acceptance Criteria:**
- Admin dashboard loads in < 2 seconds
- Bulk operations process 100 users in < 30 seconds
- Activity logs retained for 1 year

#### **FR-6.2: Data Export & Reporting (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Export student data to CSV/Excel
- Export options: All students, Filtered by criteria, Custom field selection
- Generate PDF reports with charts and statistics
- Data includes all fields with proper formatting

**Acceptance Criteria:**
- Export 500 records in < 10 seconds
- CSV opens properly in Excel
- PDF report is professionally formatted

#### **FR-6.3: System Settings (MUST HAVE)**
**Priority:** Critical for MVP

**Requirements:**
- Configure email settings (SMTP)
- Manage site settings (logo, name, contact info)
- Set privacy policies and terms
- Configure notification preferences

**Acceptance Criteria:**
- Settings save immediately
- Email test feature works
- Changes reflect across site immediately

---

## 6. Non-Functional Requirements

### 6.1 Performance

**NFR-1: Page Load Time**
- All pages must load within 3 seconds on standard broadband (5 Mbps)
- API responses within 1 second for 95% of requests
- Database queries optimized for < 500ms response time

**NFR-2: Scalability**
- Support 1,000 concurrent users
- Handle 10,000 registered student profiles
- Store 5,000+ apartment reviews
- Process 100,000 email sends per month

**NFR-3: File Upload Performance**
- Images (up to 5MB) upload within 10 seconds
- PDFs (up to 50MB) upload within 30 seconds
- Concurrent uploads supported (5 simultaneous users)

### 6.2 Security

**NFR-4: Authentication & Authorization**
- Secure password storage (bcrypt/argon2)
- Email verification for new registrations
- Role-based access control (Super Admin, Admin, Member)
- Session timeout after 24 hours of inactivity

**NFR-5: Data Protection**
- HTTPS encryption for all communications
- Sensitive data (passwords, personal info) encrypted at rest
- SQL injection protection
- XSS (Cross-Site Scripting) protection
- CSRF (Cross-Site Request Forgery) tokens

**NFR-6: Privacy Compliance**
- Students control visibility of contact information
- GDPR-compliant data handling (right to delete, export)
- Admin access logged and auditable
- Location data only visible to admins

### 6.3 Reliability & Availability

**NFR-7: Uptime**
- 99.5% uptime (< 44 hours downtime per year)
- Scheduled maintenance during low-traffic hours (announced 48h in advance)

**NFR-8: Backup & Recovery**
- Daily automated backups
- Point-in-time recovery within last 30 days
- Backup retention for 1 year
- Recovery time objective (RTO): 4 hours
- Recovery point objective (RPO): 24 hours

**NFR-9: Error Handling**
- Graceful error messages (user-friendly, no technical jargon)
- Error logging for debugging
- Automatic retry for transient failures
- Email notification to admins for critical errors

### 6.4 Usability

**NFR-10: User Interface**
- Responsive design (mobile, tablet, desktop)
- Intuitive navigation (3-click rule to any page)
- Consistent design language across all pages
- Accessible (WCAG 2.1 Level AA compliance where feasible)

**NFR-11: Internationalization**
- Support Indonesian and English languages
- Language toggle easily accessible
- All text externalized for translation

**NFR-12: Browser Compatibility**
- Support latest 2 versions of Chrome, Firefox, Safari, Edge
- Graceful degradation for older browsers

### 6.5 Maintainability

**NFR-13: Code Quality**
- Modular architecture for easy updates
- Comprehensive inline documentation
- Code follows established style guides
- Unit test coverage > 70% for critical functions

**NFR-14: Logging & Monitoring**
- Application logging (errors, warnings, info)
- Performance monitoring (response times, resource usage)
- User activity tracking (analytics)
- Automated alerts for system issues

### 6.6 Compatibility

**NFR-15: Email Client Compatibility**
- Email broadcasts render correctly in: Gmail, Outlook, Yahoo Mail, Apple Mail
- Responsive email design for mobile devices

**NFR-16: Data Migration**
- Support import from Excel/CSV for initial data migration
- Validation during import to ensure data integrity
- Error reporting for failed imports

---

## 7. User Experience & Workflows

### 7.1 Key User Flows

#### **Flow 1: New Student Registration**

1. Student visits website
2. Clicks "Register" button
3. Fills registration form (name, email, program, faculty, scholarship, photo, location)
4. Submits form
5. Receives verification email
6. Clicks verification link
7. Account activated
8. Redirected to profile page
9. Can browse apartments, connect with seniors

**Success Criteria:** Complete registration in < 5 minutes with clear instructions at each step

---

#### **Flow 2: Finding Housing**

1. New student logs in
2. Navigates to "Housing" section
3. Views map with apartment locations
4. Filters by area/price/rating
5. Clicks apartment to see details
6. Reads reviews from other students
7. Views photos and pros/cons
8. Saves favorites or contacts reviewer for more info

**Success Criteria:** Find suitable apartment options within 10 minutes

---

#### **Flow 3: Admin Publishing News Bulletin**

1. Admin logs in
2. Navigates to "News Management"
3. Clicks "Create New Article"
4. Enters title, content (rich text), uploads image
5. Selects category tags
6. Previews article
7. Publishes
8. Article appears on homepage immediately
9. Optionally sends email notification to members

**Success Criteria:** Publish bulletin in < 5 minutes

---

#### **Flow 4: Admin Generating Demographic Report**

1. Admin logs in
2. Navigates to "Reports" section
3. Selects report type (e.g., "Students by Faculty")
4. Applies filters (batch year, scholarship type)
5. Views interactive charts and tables
6. Exports to PDF or Excel
7. Shares with sponsors/partners

**Success Criteria:** Generate and export report in < 3 minutes

---

### 7.2 Wireframe Requirements

**Homepage:**
- Hero section with PPI branding
- 3 featured bulletins (cards with image, title, excerpt)
- Quick statistics (total students, countries represented, apartments reviewed)
- Call-to-action buttons (Register, Browse Housing, Contact)

**Student Directory:**
- Search bar at top
- Filter sidebar (faculty, major, batch, scholarship)
- Grid of student cards (photo, name, major, batch)
- Click card to see full profile

**Housing Map:**
- Interactive map taking 60% of screen
- Sidebar with list view (30% of screen)
- Filter controls above sidebar
- Apartment cards with rating, price range, review count

**Admin Dashboard:**
- Sidebar navigation (Users, Housing, News, Reports, Settings)
- Statistics cards (new registrations, pending reviews, email metrics)
- Recent activity feed
- Quick actions (Send Email, Publish News, Export Data)

---

## 8. Technical Considerations

### 8.1 Technology Stack - Laravel 12 + Filament 4 Starter

**Base Framework:**
- **Laravel Framework**: 12.0 (Backend framework)
- **FilamentPHP**: 4.0 (Modern admin panel framework)
- **PHP**: 8.3.24
- **Tailwind CSS**: 4.0 (For styling - pre-configured)
- **Livewire**: 3.0 (Reactive components)

**Pre-Configured Features (Included in Starter):**

**Authentication & Authorization:**
- ✅ **Filament Shield v4**: Role-Based Access Control (RBAC) - Already configured
- ✅ **Filament Breezy v3**: User profile management and 2FA support
- ✅ **Filament Socialite v3**: Google, GitHub, Facebook OAuth login (ready to use)
- ✅ **Filament Impersonate v4**: User impersonation for support

**Development & Debugging:**
- ✅ **Laravel Telescope v5**: Application debugging and monitoring
- ✅ **Laravel Debugbar**: Debug toolbar for local development
- ✅ **Laravel Pail**: Real-time log viewing

**Media & Content:**
- ✅ **Spatie Media Library v11**: File upload and media management
- ✅ **Filament Spatie Media Library Plugin v4**: Filament integration
- ✅ **Blade FontAwesome**: Icon support

**Activity & Logging:**
- ✅ **Spatie Activity Log v4**: User activity tracking and audit logs
- ✅ Import/Export capabilities built-in

**Additional Technology Needs:**

**Database:**
- MySQL (recommended for both development and production)
- PostgreSQL (alternative option)
- Support for geospatial data (location/maps)

**Testing:**
- **Pest v4**: Modern PHP testing framework (pre-configured)
- **Pest Laravel Plugin v4**: Laravel-specific testing helpers
- PHPUnit v12 (underlying test runner)

**Maps Integration:**
- Google Maps API or Mapbox
- Laravel integration for geocoding

**Email Service:**
- **Resend**: Modern email API service (recommended)
- Laravel's built-in mail system configured with Resend

**File Storage:**
- **Spatie Media Library v11** (included) with local storage
- Cloud Storage (optional): AWS S3, Google Cloud Storage, or Cloudinary
- CDN (optional): CloudFront or Cloudflare for image delivery

**Charts & Analytics:**
- **Filament Widgets**: Built-in chart widgets
- Alternative: Chart.js, ApexCharts

**Deployment:**
- **Laravel Sail v1**: Docker development environment (included)
- Traditional hosting: Shared hosting, VPS, or cloud platforms
- Laravel Forge or Ploi for deployment automation

**Version Control:**
- Git with GitHub/GitLab
- Branching strategy: Git Flow or GitHub Flow

### 8.2 Architecture Approach

**Recommended: Monolithic Laravel Application (Ideal for MVP)**

This Laravel 12 + Filament 4 starter provides a solid monolithic architecture that's perfect for rapid development:

**Advantages:**
- ✅ All features pre-configured (RBAC, Media, Activity Logs, Social Auth)
- ✅ Faster initial development (60% of boilerplate already done)
- ✅ Easier debugging and deployment
- ✅ No need to configure authentication or authorization from scratch
- ✅ Filament 4 provides instant admin UI with minimal code
- ✅ Laravel 12's streamlined structure (no Kernel files)

**Structure:**
- **Admin Panel**: Filament 4 (at `/admin` route)
  - User management with Shield v4
  - All CRUD operations
  - Dashboard with widgets
  - Settings and configuration
  - Activity logs and impersonation

- **Public Website**: Laravel Blade views (at `/` route)
  - Homepage with news
  - Housing directory
  - Student directory (public view)
  - Graduate map

- **API**: Laravel Sanctum (at `/api` route)
  - RESTful endpoints with Laravel API Resources
  - Token-based authentication
  - Optional: Add API documentation (Scramble, L5-Swagger, or similar)
  - Integrate with Shield for role-based API access

**Can scale to microservices later if needed, but NOT recommended for MVP**

### 8.3 Database Schema Considerations

**Key Tables (Laravel Migrations):**

**users** (Extend existing table)
```php
// Already includes: id, name, email, password, email_verified_at, remember_token, timestamps
// Add these fields via migration:
- profile_photo_path (string, nullable) // Using Spatie Media Library
- phone (string, nullable)
- faculty_id (foreignId, nullable) // FK to faculties table
- major (string, nullable)
- batch_year (year, nullable)
- scholarship_type (enum: 'mombukagakusho', 'other', 'none')
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- address (text, nullable)
- is_graduate (boolean, default: false)
- graduation_year (year, nullable)
- timestamps
```

**faculties** (New table)
```php
- id (bigIncrements)
- name (string) // e.g., "Engineering", "Medicine", "Economics"
- code (string) // e.g., "ENG", "MED"
- timestamps
```

**apartments**
```php
- id (bigIncrements)
- name (string)
- address (text)
- latitude (decimal)
- longitude (decimal)
- area (string, nullable) // e.g., "Cijeruk", "Ikebukuro"
- average_rent_min (integer, nullable)
- average_rent_max (integer, nullable)
- description (text, nullable)
- created_by (foreignId) // FK to users
- timestamps
- softDeletes
```

**apartment_reviews**
```php
- id (bigIncrements)
- apartment_id (foreignId) // FK to apartments
- user_id (foreignId) // FK to users
- rating (tinyInteger) // 1-5
- review_text (text)
- pros (text, nullable)
- cons (text, nullable)
- move_in_date (date, nullable)
- move_out_date (date, nullable)
- is_approved (boolean, default: false)
- approved_by (foreignId, nullable) // FK to users
- approved_at (timestamp, nullable)
- timestamps
- softDeletes
```

**news_bulletins**
```php
- id (bigIncrements)
- title (string)
- slug (string, unique)
- content (longText) // HTML content
- excerpt (text, nullable)
- featured_image (string, nullable) // Using Spatie Media Library
- category (string, nullable) // e.g., "event", "announcement", "news"
- author_id (foreignId) // FK to users
- published_at (timestamp, nullable)
- views_count (integer, default: 0)
- is_featured (boolean, default: false)
- timestamps
- softDeletes
```

**email_campaigns**
```php
- id (bigIncrements)
- subject (string)
- content (longText) // HTML content
- target_filter (json) // e.g., {"faculty": "engineering", "batch": "2024"}
- sent_at (timestamp, nullable)
- sent_count (integer, default: 0)
- open_count (integer, default: 0)
- click_count (integer, default: 0)
- created_by (foreignId) // FK to users
- status (enum: 'draft', 'scheduled', 'sent', 'failed')
- scheduled_at (timestamp, nullable)
- timestamps
```

**alumni_profiles** (Extends users table data)
```php
- id (bigIncrements)
- user_id (foreignId, unique) // FK to users
- current_country (string)
- current_city (string, nullable)
- current_employer (string, nullable)
- occupation (string, nullable)
- research_focus (text, nullable)
- publications (json, nullable) // Array of publication objects
- linkedin_url (string, nullable)
- personal_website (string, nullable)
- achievements (text, nullable)
- timestamps
```

**Pivot Tables (Created by Filament Shield):**
- `model_has_roles` (users ↔ roles)
- `model_has_permissions` (users ↔ permissions)
- `role_has_permissions` (roles ↔ permissions)

**Activity Log Tables (Created by Spatie Activity Log):**
- `activity_log` (user activity tracking and audit trail)

**Indexes (Add in migrations):**
```php
// users table
$table->index('email');
$table->index('faculty_id');
$table->index('batch_year');
$table->index('scholarship_type');
$table->index(['latitude', 'longitude']); // Spatial index for maps

// apartments table
$table->index(['latitude', 'longitude']); // Spatial index
$table->index('area');

// apartment_reviews table
$table->index('apartment_id');
$table->index('user_id');
$table->index('rating');
$table->index('is_approved');

// news_bulletins table
$table->index('published_at');
$table->index('category');
$table->index('is_featured');
$table->index('slug');
```

**Using Spatie Media Library for Images:**
- Profile photos
- Apartment review photos
- News bulletin featured images
- Stored in `media` table (auto-created by Spatie package)

### 8.4 API Design Principles

**Using Laravel Sanctum (Pre-configured):**

Laravel Sanctum provides token-based API authentication. Build API endpoints as needed using Laravel's API resources.

**Example API Endpoints:**
```
GET    /api/users           - List all users (with pagination, filtering)
POST   /api/users           - Create new user
GET    /api/users/{id}      - Get user details
PUT    /api/users/{id}      - Update user
DELETE /api/users/{id}      - Delete user
```

**Authentication:**
- **Token-based authentication** (Laravel Sanctum v4 - included)
- Login endpoint: `POST /api/auth/login` (create this endpoint)
- Returns API token for subsequent requests
- Include token in header: `Authorization: Bearer {token}`

**API Endpoints (Build in routes/api.php):**
```php
// In routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Custom demographic stats
    Route::get('/demographics/stats', [DemographicsController::class, 'stats']);
    
    // Custom apartment search
    Route::post('/apartments/search', [ApartmentController::class, 'search']);
    
    // Custom alumni map data
    Route::get('/alumni/map', [AlumniController::class, 'mapData']);
});
```

**Response Format (Laravel API Resources):**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "faculty": "Engineering",
    "major": "Computer Science"
  }
}
```

**List Response with Pagination (Laravel Standard):**
```json
{
  "data": [
    { "id": 1, "name": "..." },
    { "id": 2, "name": "..." }
  ],
  "links": {
    "first": "http://api.example.com/api/users?page=1",
    "last": "http://api.example.com/api/users?page=10",
    "prev": null,
    "next": "http://api.example.com/api/users?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

**API Documentation (Optional):**
- Can add API documentation using:
  - **Scramble**: Auto-generates OpenAPI docs from your code
  - **L5-Swagger**: OpenAPI/Swagger implementation for Laravel
  - **Postman Collections**: Manual API documentation
  - **Scribe**: Generate API documentation from Laravel routes

**Query Parameters:**
- Filtering: `?filter[name]=John&filter[faculty_id]=1`
- Sorting: `?sort=name` or `?sort=-created_at` (descending)
- Pagination: `?page=2&per_page=20`
- Search: `?search=keyword`
- Relationships: `?include=faculty,reviews`

### 8.5 Security Implementation

**Built-in Security (Laravel 12 + Filament 4):**

**Authentication:**
- ✅ Laravel's built-in authentication (already configured)
- ✅ Email verification for new registrations
- ✅ Password hashing with bcrypt (Laravel default)
- ✅ Session management with secure cookies
- ✅ Two-Factor Authentication (2FA) via Filament Breezy v3

**Authorization:**
- ✅ **Filament Shield v4 RBAC** (pre-configured)
  - Role-based permissions
  - Resource-level access control
  - Policy-based authorization
  - User-to-role assignment
- ✅ Super Admin capabilities
- ✅ User impersonation v4 (audited via Activity Log)

**Password Requirements (Filament Breezy):**
```php
// Configured in AdminPanelProvider
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 symbol/special character
- Requires current password for updates
```

**API Security:**
- ✅ Laravel Sanctum v4 for API tokens (included)
- ✅ Token-based authentication
- ✅ API rate limiting (configurable in routes)
- ✅ Can integrate Shield for role-based API access

**Additional Security Measures:**

**Rate Limiting:**
```php
// In routes/web.php or api.php
- Login attempts: 5 per 15 minutes per IP
- API requests: 60 per minute per user (configurable)
- Password reset: 3 per hour per email
- Email sending: Configure in admin settings
```

**Input Validation:**
- ✅ Laravel validation for all inputs
- ✅ Form Request classes for complex validation
- ✅ File upload validation (type, size, mime)
- ✅ XSS protection (Laravel escapes output by default)
- ✅ CSRF protection (Laravel middleware)
- ✅ SQL injection protection (Eloquent ORM)

**File Upload Security:**
```php
// Using Spatie Media Library v11 (included)
- Allowed types: jpg, jpeg, png, pdf (configurable)
- Max size: 5MB for images, 50MB for documents
- Automatic virus scanning (optional, via ClamAV)
- Secure storage paths (private by default in Filament 4)
- CDN integration for public files
```

**Session Management:**
- Session timeout: 120 minutes (configurable in config/session.php)
- Secure session cookies (HTTPS only in production)
- CSRF token refresh
- Session regeneration on login

**Database Security:**
- ✅ Eloquent ORM prevents SQL injection
- ✅ Mass assignment protection ($fillable/$guarded)
- ✅ Encrypted sensitive data (optional via Laravel Crypt)
- ✅ Database connection encryption (SSL/TLS)

**Additional Recommendations:**

**Environment Security:**
```php
// In .env file
APP_ENV=production
APP_DEBUG=false  // CRITICAL: Must be false in production
APP_URL=https://your-domain.com  // Use HTTPS

// Secure keys
APP_KEY=base64:... // Auto-generated, never share
DB_PASSWORD=... // Strong password
RESEND_API_KEY=... // Keep secret
```

**HTTPS Enforcement:**
```php
// In App\Providers\AppServiceProvider.php boot() method
if (app()->environment('production')) {
    \URL::forceScheme('https');
}
```

**Security Headers (Add to nginx/apache or middleware):**
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Strict-Transport-Security: max-age=31536000
- Content-Security-Policy: (configure based on needs)

**Privacy Controls:**
```php
// User privacy settings
- Contact visibility toggle
- Profile visibility (public/members-only)
- Location sharing (admin-only by default)
- GDPR compliance features:
  - Data export
  - Account deletion
  - Privacy policy acceptance
```

### 8.6 Third-Party Integrations

**Required:**
- Email Service (SendGrid/AWS SES)
- Map Service (Google Maps/Mapbox)
- Cloud Storage (AWS S3/Cloudinary)
- Analytics (Google Analytics/Mixpanel)

**Optional:**
- Social Media APIs (for auto-posting graduates)
- Payment Gateway (for future membership fees)
- SMS Service (for critical notifications)

---

## 9. Timeline & Phases

### 9.1 Development Phases

**Note:** With this Laravel 12 + Filament 4 starter, development is approximately **60% faster** because authentication, authorization, and admin UI are pre-configured. The timeline below reflects this acceleration.

#### **Phase 1: MVP Development (6 weeks) - Target: February 2025**

**Week 1: Setup & Foundation (3-4 days)**
- ✅ Clone and configure the starter kit
- ✅ Setup database and environment
- ✅ Configure Shield RBAC v4 (already 80% done)
- ✅ Setup Social OAuth (Google, GitHub, Facebook - already configured)
- ✅ Configure email service (choose provider)
- ✅ Customize branding (logo, colors, name)
- Create database migrations for custom tables

**Week 2: Student Management Module**
- Create `Student` Filament Resource (using stubs)
- Extend users table with custom fields (faculty, major, batch, scholarship, location)
- Create Faculty management
- Implement student registration form (public)
- Setup profile photo upload (Spatie Media Library)
- Create demographic dashboard widget
- Excel import for existing data

**Week 3: Housing Module**
- Create `Apartment` Filament Resource
- Create `ApartmentReview` Filament Resource
- Implement map integration (Google Maps/Mapbox)
- Setup photo uploads for apartments and reviews
- Create review moderation workflow
- Build public housing directory page

**Week 4: Communication System**
- Create `NewsBulletin` Filament Resource
- Setup rich text editor for content
- Implement featured image upload
- Create public homepage with featured news
- Setup email campaign system with Resend
- Create email templates using Laravel Mail and Resend

**Week 5: Public Website & Integration**
- Design and build public pages:
  - Homepage (news, statistics)
  - Student directory (public view)
  - Housing directory with map
  - About/Contact pages
- Implement search and filtering
- Responsive design (mobile-friendly)
- SEO optimization

**Week 6: Testing, Data Migration & Launch**
- Comprehensive testing (manual + automated)
- Data migration from Excel
- Performance optimization
- Security audit
- Admin training and documentation
- Soft launch to core team
- Bug fixes
- Public launch

**MVP Scope (Must Have for February):**
1. ✅ Student registration with photo and location (RBAC pre-configured)
2. ✅ Student directory with search (Filament Resource)
3. ✅ Demographic dashboard (Filament Widgets)
4. ✅ Apartment database with reviews (Filament Resources)
5. ✅ Map integration for apartments and student locations
6. ✅ News bulletin system (Filament Resource)
7. ✅ Email broadcasting (Resend integration)
8. ✅ Admin panel (Filament 4 - 90% ready)
9. ✅ API endpoints (Laravel Sanctum with API Resources)

**What This Starter Provides Out-of-the-Box:**
- ✅ Complete admin panel with Filament 4
- ✅ User authentication and management
- ✅ Role-based access control (Shield v4)
- ✅ User profile management (Breezy v3)
- ✅ Activity logging (Spatie Activity Log v4)
- ✅ File upload system (Spatie Media Library v11)
- ✅ Import/Export capabilities
- ✅ 2FA support (Breezy)
- ✅ User impersonation (v4 with audit)
- ✅ Social OAuth (Google, GitHub, Facebook)
- ✅ Development tools (Telescope, Debugbar, Pail)
- ✅ Testing framework (Pest v4)

---

#### **Phase 2: Enhanced Features (4 weeks) - Target: April 2025**

**Week 7-8: Graduate Tracking**
- Extend users table for alumni data
- Create `AlumniProfile` Filament Resource
- Build interactive world map
- Implement country/city filtering
- Create graduate statistics dashboard
- Alumni data entry workflow

**Week 9-10: Advanced Features**
- Email scheduling and templates
- Advanced demographic reports with charts
- PDF export for reports
- Downloadable content management
- Financial transparency reports (optional)
- Enhanced apartment filtering
- Photo galleries for apartments

---

#### **Phase 3: Optimization & Polish (3 weeks) - Target: June 2025**

**Week 11-12: Performance & Analytics**
- Performance optimization
- Caching implementation (Redis optional)
- User activity tracking
- Advanced analytics dashboard
- Mobile app responsiveness improvements
- SEO enhancements

**Week 13: Social Integration & Automation**
- Auto-generate graduate social media posts
- Integration with Instagram/Facebook APIs (optional)
- Automated email notifications
- Scheduled reports
- System monitoring and alerts

---

**Total Development Time:**
- **Phase 1 (MVP):** 6 weeks (vs 8 weeks traditional)
- **Phase 2:** 4 weeks (vs 6 weeks traditional)
- **Phase 3:** 3 weeks (vs 4 weeks traditional)
- **Total:** 13 weeks with this starter (vs 18 weeks traditional)

**Time Saved with Laravel 12 + Filament 4 Starter: ~5 weeks (38% faster)**

---

### 9.2 Milestones & Deliverables

| Milestone | Date | Deliverables |
|-----------|------|--------------|
| **M1: Project Kickoff** | Week 1 | Tech stack finalized, Development environment ready, Database schema approved |
| **M2: Core Functionality** | Week 4 | Authentication working, Student CRUD operations, Admin panel functional |
| **M3: MVP Feature Complete** | Week 7 | All Phase 1 features implemented, Internal testing completed |
| **M4: Soft Launch** | Week 8 | Data migrated, System live, Admin training completed |
| **M5: Public Launch** | February 2025 | Full user access, Marketing materials ready, Support documentation |
| **M6: Enhanced Release** | April 2025 | Graduate tracking live, Advanced features deployed |
| **M7: Optimization Release** | June 2025 | Performance improvements, Analytics dashboards, Social automation |

---

### 9.3 Resource Requirements

**Development Team:**

**Minimum Team (Recommended with this starter):**
- **1 Full-stack Developer** with Laravel + Filament experience
  - Can handle both backend (Laravel 12) and frontend (Blade + Tailwind 4)
  - Starter kit reduces complexity significantly
  - Filament 4 handles admin UI automatically
  
- **1 UI/UX Designer** (part-time, weeks 1-2 only)
  - Focus on public-facing pages (homepage, directories)
  - Admin panel design already handled by Filament
  - 15-20 hours total

- **1 Project Manager** (PPI representative)
  - 10 hours/week ongoing
  - Requirements gathering, testing, feedback

**Optional (For Faster Development):**
- **QA Tester** (weeks 5-6, part-time)
- **Additional Developer** (if timeline is tight)

**Time Commitment:**
- Lead Developer: Full-time (40 hours/week) for 6 weeks
- Designer: 15-20 hours total (weeks 1-2)
- PM: 10 hours/week ongoing
- QA: 20 hours/week for weeks 5-6

**Skills Required:**

**Essential:**
- PHP 8.3+ and Laravel 12
- Filament 4.x (learning curve is gentle)
- MySQL/PostgreSQL/SQLite
- Git version control
- Tailwind CSS 4.0
- Livewire 3.0

**Nice to Have:**
- Google Maps API integration
- Email service integration
- Laravel Sail / Docker
- Pest testing framework
- API development with Sanctum

**Budget Considerations:**

**One-Time Costs:**
- Developer contract/salary (6 weeks)
- Designer contract (20 hours)
- Domain name (~$15/year)
- SSL certificate (free with Let's Encrypt)

**Monthly Recurring Costs:**
- **Hosting:** $10-50/month
  - Shared hosting: $10-20/month (good for start)
  - VPS (DigitalOcean/Vultr): $20-50/month (recommended)
  - Cloud (AWS/GCP): $30-100/month (if scaling)

- **Email Service (Resend):** $0-20/month
  - Free tier: 3,000 emails/month
  - Pro plan: $20/month for 50,000 emails/month
  - Excellent deliverability and analytics
  
- **Google Maps API:** $0-50/month
  - $200 free credit per month
  - Usually sufficient for small to medium usage
  
- **File Storage (if using cloud):** $5-20/month
  - Local storage: Free (included with hosting)
  - AWS S3: ~$5-10/month for moderate usage
  - Optional for MVP

**Total Estimated Monthly Cost: $15-70/month**
(Can start at $15/month with free tiers and basic hosting)

**Cost Savings with This Starter:**
- **~60% reduction in development time** = ~$3,000-5,000 saved in developer costs
- **Pre-built admin UI (Filament 4)** = ~$2,000 saved in UI development
- **Built-in RBAC (Shield v4)** = ~$1,500 saved in security implementation
- **Pre-configured packages** = ~$1,000 saved in integration time
- **Total estimated savings: $7,500-9,500** for MVP

**Return on Investment:**
- Faster time to market (February deadline achievable)
- Proven, battle-tested components (Filament 4, Laravel 12)
- Active Laravel and Filament community support
- Easy to maintain and extend
- Large Laravel developer talent pool
- Modern tech stack with long-term support

---

## 9.4 Laravel 12 + Filament 4 Starter Setup & Quick Start

### Initial Setup (Day 1)

**1. Clone and Install Project**
```bash
# Clone the repository
git clone <repository-url> ppi-system

cd ppi-system

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
```

**2. Configure Database**
```env
# In .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppi_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

**3. Configure Email Service (Resend)**
```env
MAIL_MAILER=resend
RESEND_API_KEY=your_resend_api_key
MAIL_FROM_ADDRESS="admin@ppijapan.org"
MAIL_FROM_NAME="PPI Japan"
```

**4. Configure Social OAuth (Already in starter)**
```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/admin/oauth/callback/google

# GitHub OAuth
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret

# Facebook OAuth
FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
```

**5. Run Migrations**
```bash
php artisan migrate --seed
```

**6. Setup RBAC (Shield v4)**
```bash
# Generate permissions for all resources
php artisan shield:generate --all

# Create super admin
php artisan shield:super-admin
```

**7. Start Development Server**
```bash
# Using the convenient composer script
composer run dev

# This starts:
# - Laravel server (port 8000)
# - Queue worker
# - Log viewer (Pail)
# - Vite dev server

# Or using Laravel Sail (Docker)
./vendor/bin/sail up -d
```

**8. Access the Application**
- Admin panel: `http://localhost:8000/admin`
- Default credentials: As created in shield:super-admin command
- Telescope: `http://localhost:8000/telescope` (debug dashboard)

### Creating Custom Resources

**Example: Creating Apartment Resource**
```bash
# Generate Filament Resource with all the bells and whistles
php artisan make:filament-resource Apartment --generate

# This creates:
# - app/Filament/Resources/ApartmentResource.php
# - app/Filament/Resources/ApartmentResource/Pages/
# - Automatic CRUD operations
# - Built-in form and table
```

**Customizing the Resource:**
```php
// app/Filament/Resources/ApartmentResource.php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\Textarea::make('address')->required(),
        Forms\Components\TextInput::make('latitude')->numeric(),
        Forms\Components\TextInput::make('longitude')->numeric(),
        Forms\Components\TextInput::make('average_rent_min')->numeric(),
        Forms\Components\TextInput::make('average_rent_max')->numeric(),
        Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
            ->multiple()
            ->maxFiles(10),
    ]);
}
```

### Creating Custom Migrations

**Example: Extending Users Table**
```bash
php artisan make:migration add_custom_fields_to_users_table --table=users
```

```php
// In the migration file
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable()->after('email');
        $table->foreignId('faculty_id')->nullable()->constrained();
        $table->string('major')->nullable();
        $table->year('batch_year')->nullable();
        $table->enum('scholarship_type', ['mombukagakusho', 'other', 'none'])
              ->default('none');
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->text('address')->nullable();
        $table->boolean('is_graduate')->default(false);
        $table->year('graduation_year')->nullable();
    });
}
```

### Creating Widgets for Dashboard

**Example: Student Statistics Widget**
```bash
php artisan make:filament-widget StudentStatsWidget --stats-overview
```

```php
// app/Filament/Widgets/StudentStatsWidget.php
protected function getStats(): array
{
    return [
        Stat::make('Total Students', User::count()),
        Stat::make('Mombukagakusho', User::where('scholarship_type', 'mombukagakusho')->count()),
        Stat::make('Graduates', User::where('is_graduate', true)->count()),
    ];
}
```

### Customizing Admin Panel

**Branding:**
```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->brandName('PPI Japan')
        ->brandLogo(asset('images/logo.png'))
        ->colors([
            'primary' => '#2563eb', // Customize primary color
        ])
        ->favicon(asset('images/favicon.png'))
        // ... other configurations
    ;
}
```

### Development Best Practices with Laravel 12 + Filament 4

**1. Use Filament 4's Built-in Features**
- Don't reinvent the wheel - Filament 4 has built-in filters, search, bulk actions
- Use Filament's relationship managers for one-to-many relationships
- Leverage Filament Actions for custom operations
- Use Filament Schemas namespace for layouts (Section, Grid, Tabs)

**2. Leverage Spatie Media Library v11**
```php
// In your model
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Apartment extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
             ->useDisk('public');
    }
}
```

**3. Use Shield v4 for Permissions**
```php
// Automatically protect resources
// Shield v4 generates permissions like:
// - view_apartment
// - create_apartment
// - update_apartment
// - delete_apartment

// Assign to roles in admin panel
// Supports policy-based authorization
```

**4. Write Tests with Pest v4**
```php
// tests/Feature/ApartmentTest.php
use function Pest\Laravel\{get, post, assertDatabaseHas};

it('can create an apartment', function () {
    $data = ['name' => 'Test Apartment', 'address' => '123 Test St'];

    post('/admin/apartments', $data);

    assertDatabaseHas('apartments', $data);
});
```

**5. Custom API Endpoints**
```php
// routes/api.php
use App\Http\Controllers\Api\DemographicsController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/demographics', [DemographicsController::class, 'index']);
});
```

### Testing

```bash
# Run all tests (Pest v4)
php artisan test

# Or use composer script
composer run test

# Run specific test file
php artisan test tests/Feature/ApartmentTest.php

# Run with code coverage
php artisan test --coverage

# Create a test (Pest syntax)
php artisan make:test ApartmentTest --pest
```

### Deployment

**Option 1: Traditional Server**
```bash
# On your server
git clone your-repo
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

**Option 2: Docker with Laravel Sail**
```bash
# Already configured in project
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --force
```

**Option 3: Platform as a Service**
- Laravel Forge (recommended, $12/month)
- Ploi.io ($10/month)
- Laravel Cloud (when available)
- DigitalOcean App Platform

---

## 10. Risks & Mitigation

### 10.1 Technical Risks

**Risk 1: Data Migration Issues**
- **Probability:** High
- **Impact:** High
- **Description:** Existing Excel data may be incomplete, inconsistent, or corrupted
- **Mitigation:**
  - Perform data audit before migration
  - Create data cleaning scripts
  - Implement validation during import
  - Manual review and correction process
  - Backup original Excel files

**Risk 2: Email Deliverability**
- **Probability:** Medium
- **Impact:** High
- **Description:** Emails may land in spam, reducing communication effectiveness
- **Mitigation:**
  - Use reputable email service (SendGrid/AWS SES)
  - Implement SPF, DKIM, DMARC authentication
  - Monitor bounce and spam rates
  - Provide unsubscribe option
  - Start with small batches to build reputation

**Risk 3: Scalability Issues**
- **Probability:** Low
- **Impact:** Medium
- **Description:** System may slow down as user base grows
- **Mitigation:**
  - Design with scalability in mind from start
  - Implement caching (Redis)
  - Use CDN for static assets
  - Monitor performance metrics
  - Plan for database optimization

**Risk 4: Security Vulnerabilities**
- **Probability:** Medium
- **Impact:** Critical
- **Description:** Data breach or unauthorized access
- **Mitigation:**
  - Security audit before launch
  - Regular security updates
  - Input validation and sanitization
  - Rate limiting and DDoS protection
  - Regular backups
  - Incident response plan

### 10.2 Project Risks

**Risk 5: Timeline Delays**
- **Probability:** High
- **Impact:** High
- **Description:** February deadline may be missed due to scope creep or technical challenges
- **Mitigation:**
  - Strict MoSCoW prioritization (focus on Must Have only for MVP)
  - Weekly progress reviews
  - Buffer time built into schedule (1 week)
  - Clear scope boundaries
  - Agile approach for flexibility

**Risk 6: User Adoption**
- **Probability:** Medium
- **Impact:** High
- **Description:** Students may not switch from Excel/existing methods
- **Mitigation:**
  - User training and onboarding
  - Create tutorial videos and documentation
  - Incentivize early adopters
  - Gather feedback and iterate quickly
  - Make registration mandatory for new students

**Risk 7: Resource Availability**
- **Probability:** Medium
- **Impact:** High
- **Description:** Developer or key team member becomes unavailable
- **Mitigation:**
  - Clear documentation throughout development
  - Knowledge sharing sessions
  - Code repository with good README
  - Identify backup resources
  - Use established technologies (easier to find help)

### 10.3 User Acceptance Risks

**Risk 8: Privacy Concerns**
- **Probability:** Medium
- **Impact:** Medium
- **Description:** Students may be hesitant to share location and personal data
- **Mitigation:**
  - Clear privacy policy
  - Granular privacy controls
  - Explain how data is used
  - Show only what's necessary
  - Admin-only access to sensitive data (location)
  - GDPR compliance

**Risk 9: Poor Data Quality**
- **Probability:** Medium
- **Impact:** Medium
- **Description:** Users may enter incomplete or inaccurate information
- **Mitigation:**
  - Required fields enforced
  - Validation rules
  - Admin review and correction capabilities
  - Periodic data quality audits
  - Email reminders to complete profiles

### 10.4 Contingency Plans

**If Timeline Slips:**
- Launch with reduced MVP (Must Have only)
- Delay Should Have features to Phase 2
- Increase team resources temporarily
- Extend soft launch period for testing

**If Budget Exceeds:**
- Use open-source alternatives for paid services
- Start with smaller hosting plan
- Reduce third-party integrations
- Seek sponsorship from PPI partners

**If Technical Blocker:**
- Pivot to alternative technology
- Bring in specialist consultant
- Simplify feature scope
- Community/forum for technical help

---

## 11. Appendix

### 11.1 Glossary

| Term | Definition |
|------|------------|
| **PPI** | Perhimpunan Pelajar Indonesia - Indonesian Student Association |
| **HUI** | Higher education institution in Indonesia that tracks scholarship recipients |
| **Mombukagakusho** | Japanese government scholarship (MEXT) |
| **Pengurus** | Administrator/Board member of PPI |
| **Press Release** | News bulletin or announcement |
| **Cijeruk** | Previously central meeting location for students |
| **MVP** | Minimum Viable Product - first version with core features |
| **MoSCoW** | Prioritization method: Must have, Should have, Could have, Won't have |
| **Filament** | Modern admin panel framework for Laravel applications |
| **Laravel** | PHP web application framework |
| **Filament Shield** | RBAC (Role-Based Access Control) plugin for Filament |
| **Filament Breezy** | User profile management and 2FA plugin |
| **Spatie Media Library** | Laravel package for handling media files and attachments |
| **Spatie Activity Log** | Laravel package for tracking user activities and audit logs |
| **Eloquent ORM** | Laravel's database object-relational mapping system |
| **RBAC** | Role-Based Access Control - permission system based on user roles |
| **2FA** | Two-Factor Authentication - additional security layer |
| **Laravel Sanctum** | API authentication system for Laravel |
| **Resend** | Modern email API service with excellent deliverability |
| **Livewire** | Full-stack framework for Laravel that makes building dynamic interfaces simple |
| **Pest** | Modern PHP testing framework with expressive syntax |
| **Laravel Telescope** | Elegant debug assistant for Laravel applications |
| **Laravel Pail** | Real-time log viewer for Laravel applications |

### 11.2 References

**Project Resources:**
- Original user interview transcript (Voice_251207_072820_original.txt)
- HUI website: [Reference for student data submission]
- Current PPI website (if available)

**Technical Documentation:**
- **Filament Documentation:** https://filamentphp.com/docs/4.x
- **Laravel Documentation:** https://laravel.com/docs/12.x
- **Filament Shield:** https://filamentphp.com/plugins/bezhansalleh-shield
- **Filament Breezy:** https://filamentphp.com/plugins/jeffgreco-breezy
- **Filament Socialite:** https://filamentphp.com/plugins/dutchcodingcompany-socialite
- **Filament Impersonate:** https://filamentphp.com/plugins/stechstudio-impersonate
- **Spatie Media Library:** https://spatie.be/docs/laravel-medialibrary/v11
- **Spatie Activity Log:** https://spatie.be/docs/laravel-activitylog/v4
- **Laravel Sanctum:** https://laravel.com/docs/12.x/sanctum
- **Laravel Telescope:** https://laravel.com/docs/12.x/telescope
- **Resend Documentation:** https://resend.com/docs
- **Resend Laravel Integration:** https://resend.com/docs/send-with-laravel
- **Pest Testing:** https://pestphp.com/docs
- **Tailwind CSS v4:** https://tailwindcss.com/docs
- **Livewire v3:** https://livewire.laravel.com/docs
- **Scramble (Optional API Docs):** https://scramble.dedoc.co/

**Learning Resources:**
- Filament YouTube Channel: https://www.youtube.com/@filamentphp
- Laravel Bootcamp: https://bootcamp.laravel.com/
- Laracasts: https://laracasts.com/ (Premium Laravel tutorials)
- Pest Testing Course: https://pestphp.com/learn
- Tailwind CSS Tutorials: https://tailwindcss.com/tutorials

**Community Support:**
- Filament Discord: https://filamentphp.com/discord
- Laravel Discord: https://discord.gg/laravel
- Spatie Discord: https://spatie.be/discord
- Pest Discord: https://pestphp.com/discord

### 11.3 Document History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | December 2024 | PPI Management Team | Initial PRD based on user interviews |

### 11.4 Approval & Sign-off

This document requires approval from:

- [ ] PPI President/Chairperson
- [ ] PPI Technical Committee
- [ ] Development Team Lead
- [ ] Key Stakeholders

**Approved by:** ___________________________  
**Date:** ___________________________

---

## Next Steps

1. **Review and feedback:** Share this PRD with all stakeholders for input (deadline: 1 week)
2. **Finalize scope:** Confirm MVP features based on budget and timeline
3. **Team formation:** Identify and onboard development team
4. **Technical design:** Create detailed technical architecture document
5. **Sprint planning:** Break down work into 2-week sprints
6. **Kickoff meeting:** Align team on goals, timeline, and expectations

---

**Document End**
