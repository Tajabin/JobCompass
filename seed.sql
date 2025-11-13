-- seed.sql for jobcompass.com
DROP DATABASE IF EXISTS jobcompass;
CREATE DATABASE jobcompass CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobcompass;

-- users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullName VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  educationLevel VARCHAR(100),
  experienceLevel VARCHAR(50),
  preferredTrack VARCHAR(100),
  password VARCHAR(255),
  skills TEXT,
  about TEXT,
  cv_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- jobs table
CREATE TABLE jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  company VARCHAR(255),
  location VARCHAR(255),
  requiredSkills TEXT,
  recommendedExperience VARCHAR(50),
  type VARCHAR(50),
  track VARCHAR(100),
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- resources table
CREATE TABLE resources (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  platform VARCHAR(100),
  url TEXT,
  relatedSkills TEXT,
  cost VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed jobs (about 18 entries focused on student/entry-level)
INSERT INTO jobs (title, company, location, requiredSkills, recommendedExperience, type, track, description) VALUES
('Frontend Intern', 'BrightWeb Ltd','Remote','[\"HTML\",\"CSS\",\"JavaScript\"]','Fresher','Internship','Web Development','Work on frontend components.'),
('Junior Backend Developer','DataWorks','Dhaka','[\"PHP\",\"MySQL\"]','Fresher','Full-time','Web Development','Build APIs and database logic.'),
('Data Analyst Intern','Insight Labs','Remote','[\"Excel\",\"SQL\",\"Python\"]','Fresher','Internship','Data','Analyze datasets and prepare reports.'),
('UI/UX Design Intern','Creative Co','Chittagong','[\"Figma\",\"Design Thinking\"]','Fresher','Internship','Design','Assist with prototype and UI design.'),
('Social Media Assistant','MarketFlow','Remote','[\"Content Creation\",\"Communication\"]','Fresher','Part-time','Marketing','Support social campaigns.'),
('Junior QA Tester','QualityWorks','Remote','[\"Testing\",\"Attention to Detail\"]','Fresher','Full-time','Web Development','Test features and report bugs.'),
('Junior DevOps Assistant','CloudStart','Remote','[\"Linux\",\"Docker\"]','Junior','Internship','Web Development','Support deployment tasks.'),
('Junior Machine Learning Intern','AIDynamics','Dhaka','[\"Python\",\"Pandas\"]','Fresher','Internship','Data','Assist model training and data cleaning.'),
('Content Writer','EduPress','Remote','[\"Writing\",\"Research\"]','Fresher','Part-time','Marketing','Write educational content.'),
('Customer Support Intern','HelpHub','Dhaka','[\"Communication\",\"Problem Solving\"]','Fresher','Internship','Other','Support customers via chat.'),
('WordPress Developer','SiteMakers','Remote','[\"PHP\",\"WordPress\"]','Junior','Freelance','Web Development','Create and customize WP sites.'),
('Junior Database Admin','DataKeep','Chittagong','[\"MySQL\",\"SQL\"]','Junior','Full-time','Data','Manage DB backups and queries.'),
('Graphic Design Intern','PixelArt','Remote','[\"Photoshop\",\"Illustrator\"]','Fresher','Internship','Design','Create marketing visuals.'),
('Excel Analyst','BizIntel','Remote','[\"Excel\",\"Pivot Tables\"]','Fresher','Part-time','Data','Prepare spreadsheets and dashboards.'),
('Front-end React Intern','NextGen','Remote','[\"React\",\"JavaScript\",\"HTML\"]','Fresher','Internship','Web Development','Build interactive UI.'),
('Marketing Intern','AdVibe','Remote','[\"SEO\",\"Content Creation\"]','Fresher','Internship','Marketing','Assist campaign optimization.'),
('Freelance Translator','LinguaPro','Remote','[\"Translation\",\"Communication\"]','Fresher','Freelance','Other','Translate documents.'),
('Junior Android Developer','MobileLab','Dhaka','[\"Java\",\"Android\"]','Junior','Full-time','Web Development','Assist in app development.');

-- Seed resources (about 18)
INSERT INTO resources (title, platform, url, relatedSkills, cost) VALUES
('HTML Crash Course - Full Tutorial','YouTube','https://www.youtube.com/watch?v=UB1O30fR-EE','[\"HTML\"]','Free'),
('JS Basics - Beginner to Advanced','Coursera','https://www.coursera.org/learn/javascript','[\"JavaScript\"]','Free'),
('PHP for Beginners','Udemy','https://www.udemy.com/course/php-for-beginners/','[\"PHP\"]','Paid'),
('MySQL for Data Analysis','YouTube','https://www.youtube.com/watch?v=7S_tz1z_5bA','[\"MySQL\",\"SQL\"]','Free'),
('Excel Essentials','Coursera','https://www.coursera.org/learn/excel','[\"Excel\"]','Free'),
('Intro to Data Analysis with Python','edX','https://www.edx.org/course/data-analysis-python','[\"Python\"]','Paid'),
('Figma UI Basics','YouTube','https://www.youtube.com/watch?v=FTFaQWZBqQ8','[\"Figma\",\"Design\"]','Free'),
('Communication Skills Mini-Course','Udemy','https://www.udemy.com/course/communication-skills/','[\"Communication\"]','Paid'),
('React Basics','YouTube','https://www.youtube.com/watch?v=w7ejDZ8SWv8','[\"React\",\"JavaScript\"]','Free'),
('SEO Foundations','Coursera','https://www.coursera.org/learn/seo','[\"SEO\"]','Free'),
('Git & GitHub Crash Course','YouTube','https://www.youtube.com/watch?v=RGOj5yH7evk','[\"Git\"]','Free'),
('Docker Quickstart','YouTube','https://www.youtube.com/watch?v=3c-iBn73dDE','[\"Docker\"]','Free'),
('Photoshop for Beginners','Udemy','https://www.udemy.com/course/photoshop-for-beginners/','[\"Photoshop\"]','Paid'),
('Pivot Tables Masterclass','YouTube','https://www.youtube.com/watch?v=9NUjHBNWe9M','[\"Excel\"]','Free'),
('Introduction to Machine Learning','Coursera','https://www.coursera.org/learn/machine-learning','[\"Machine Learning\",\"Python\"]','Free'),
('WordPress Theme Development','YouTube','https://www.youtube.com/watch?v=3a1G6-3O7eQ','[\"WordPress\",\"PHP\"]','Free'),
('Android Development for Beginners','YouTube','https://www.youtube.com/watch?v=fis26HvvDII','[\"Android\",\"Java\"]','Free'),
('Content Writing for Beginners','YouTube','https://www.youtube.com/watch?v=QpP6T2S1-4Q','[\"Writing\"]','Free');