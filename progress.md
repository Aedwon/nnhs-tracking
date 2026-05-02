# SGMS System Progress Overview

This document tracks the current implementation state and features of the School Grade Management System (SGMS).

## 🚀 Core Features & Implementation State

### 1. User Roles & Authentication
- **Admin (Principal)**: Full system control, approval of unlock requests, and high-level progress monitoring.
- **Teacher (Subject Teacher)**: Grade entry for assigned subjects, draft saving, and finalization.
- **Adviser (Section Adviser)**: Student roster management and subject-teacher assignments for their section.

### 2. Grade Management (Simplified Workflow)
- **Direct Quarterly Entry**: Teachers enter raw quarterly grades (Q1-Q4) directly without complex sub-component (WW/PT/QA) calculations.
- **Multi-Quarter View**: A unified grade sheet showing all four quarters for each student.
- **Automatic Final Rating**: Real-time calculation of the average grade across all submitted quarters.
- **Draft vs. Final**: Support for saving drafts before committing final grades to the system.

### 3. Adviser Portal
- **Student Roster**: Advisers can add/manage students specifically for their assigned section.
- **Subject-Teacher Mapping**: Interface to link subjects to specific teachers within their section.
- **Consolidation Monitoring**: Visual progress bars showing how many subject teachers have finalized their grades for the section.

### 4. Grade Unlock & Security
- **Sheet Lockdown**: Grades become read-only once finalized by the teacher.
- **Unlock Requests**: Teachers can submit requests to the Principal to unlock a finalized sheet for corrections.
- **Principal Approval**: A dashboard for the Principal to review, approve, or reject unlock requests.

### 5. Dashboards & Analytics
- **Teacher Dashboard**: Shows assigned subjects, progress bars, and critical deadlines.
- **Adviser Dashboard**: Monitor consolidation progress across all subjects in the section.
- **Admin Heatmap**: High-level visual tracking of grading progress across the entire school.

## 🛠️ Technical Stack
- **Framework**: Laravel (Eloquent ORM)
- **Frontend**: Blade Templates, TailwindCSS, Alpine.js (for dynamic UI components)
- **Database**: Standardized schema for Students, Subjects, Sections, Grading Periods, and Grades.

## 📅 Pending / Future Enhancements
- [x] Individual "Delete" buttons for students/subjects in adviser management.
- [x] Confirmation modals for destructive actions.
- [ ] Official Report Card (DepEd Form 138) generation (PDF).
- [ ] Multi-year data archiving.

---
*Last Updated: 2026-05-02*
